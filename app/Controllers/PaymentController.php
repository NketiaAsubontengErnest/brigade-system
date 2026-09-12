<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Validator;
use App\Core\Auth;

class PaymentController extends Controller
{
    private Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->authorize('payments.view');
        $this->db = Database::getInstance();
    }

    public function index(): void
    {
        $request = new \App\Core\Request();
        $search = $request->input('search', '');
        $page = max(1, (int)$request->input('page', 1));

        $where = "p.status != 'Void'";
        $params = [];

        if ($search) {
            $where .= "  AND (LOWER(m.first_name) LIKE LOWER(:s1) OR LOWER(m.last_name) LIKE LOWER(:s2) OR LOWER(p.receipt_number) LIKE LOWER(:s3) OR LOWER(p.reference_number) LIKE LOWER(:s4))";
            $params['s1'] = $params['s2'] = $params['s3'] = $params['s4'] = "%{$search}%";
        }

        $total = (int)$this->db->fetchOne(
            "SELECT COUNT(*) as c FROM payments p LEFT JOIN members m ON m.id = p.member_id WHERE {$where}", $params
        )['c'];

        $pagination = $this->paginate($total, 20, $page);
        $params['limit'] = $pagination['per_page'];
        $params['offset'] = $pagination['offset'];

        $payments = $this->db->fetchAll(
            "SELECT p.*, m.first_name, m.last_name, m.member_number, md.balance as remaining_balance
             FROM payments p
             LEFT JOIN members m ON m.id = p.member_id
             LEFT JOIN member_dues md ON md.id = p.member_dues_id
             WHERE {$where}
             ORDER BY p.payment_date DESC, p.created_at DESC
             LIMIT :limit OFFSET :offset", $params
        );

        $this->setMenuActive('payments');
        $this->view('payments.index', ['payments' => $payments, 'pagination' => $pagination, 'search' => $search]);
    }

    public function create(): void
    {
        $this->authorize('payments.create');
        $members = $this->db->fetchAll(
            "SELECT m.id, m.member_number, m.first_name, m.last_name 
             FROM members m WHERE m.status = 'Active' ORDER BY m.first_name"
        );
        $this->setMenuActive('payments');
        $this->view('payments.create', ['members' => $members]);
    }

    public function store(): void
    {
        $this->authorize('payments.create');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $memberId = (int)$request->input('member_id');
        $memberDuesId = $request->input('member_dues_id') ? (int)$request->input('member_dues_id') : null;
        $amount = (float)$request->input('amount', 0);
        $method = $request->input('payment_method', 'Cash');
        $reference = trim($request->input('reference_number', ''));
        $paymentDate = $request->input('payment_date') ?: date('Y-m-d');
        $notes = trim($request->input('notes', ''));

        // Validation
        if (!$memberId || $amount <= 0) {
            $this->redirect('/payments/create', 'Member and valid amount are required.', 'danger');
            return;
        }

        // Get outstanding balance
        $memberDues = null;
        if ($memberDuesId) {
            $memberDues = $this->db->fetchOne(
                "SELECT * FROM member_dues WHERE id = :id AND member_id = :m",
                ['id' => $memberDuesId, 'm' => $memberId]
            );
            if (!$memberDues) {
                $this->redirect('/payments/create', 'Invalid dues record.', 'danger');
                return;
            }
            if ($amount > $memberDues['balance']) {
                $this->redirect('/payments/create', "Payment amount ({$amount}) exceeds outstanding balance ({$memberDues['balance']}).", 'danger');
                return;
            }
        }

        try {
            $this->db->beginTransaction();

            $receiptNumber = generateReceiptNumber();

            // Create payment
            $this->db->execute(
                "INSERT INTO payments (member_id, member_dues_id, amount, payment_method, reference_number, receipt_number, payment_date, recorded_by, status, notes)
                 VALUES (:m, :md, :amt, :method, :ref, :receipt, :date, :by, 'Completed', :notes)",
                [
                    'm' => $memberId, 'md' => $memberDuesId,
                    'amt' => $amount, 'method' => $method, 'ref' => $reference ?: null,
                    'receipt' => $receiptNumber, 'date' => $paymentDate,
                    'by' => auth()->id(), 'notes' => $notes,
                ]
            );
            $paymentId = (int)$this->db->lastInsertId();

            // Update member dues if applicable
            if ($memberDues && $memberDuesId) {
                $newPaid = (float)$memberDues['amount_paid'] + $amount;
                $newBalance = (float)$memberDues['amount_due'] - $newPaid;

                // Never allow negative balance
                $newBalance = max(0, $newBalance);
                $newPaid = (float)$memberDues['amount_due'] - $newBalance;

                // Recalculate status
                if ($newBalance <= 0) {
                    $status = 'Paid';
                } elseif ($newPaid > 0) {
                    $status = 'Partial';
                } else {
                    $status = 'Unpaid';
                }

                $this->db->execute(
                    "UPDATE member_dues SET amount_paid = :paid, balance = :bal, status = :st, updated_at = NOW() WHERE id = :id",
                    ['paid' => $newPaid, 'bal' => $newBalance, 'st' => $status, 'id' => $memberDuesId]
                );
            }

            (new Auth())->logAction('payment_recorded', 'payment', $paymentId, "Recorded payment {$receiptNumber}: {$amount}");
            $this->db->commit();

            $this->redirect("/payments/{$paymentId}", "Payment recorded successfully! Receipt: {$receiptNumber}", 'success');
        } catch (\Throwable $e) {
            $this->db->rollBack();
            error_log('Payment error: ' . $e->getMessage());
            $this->redirect('/payments/create', 'Error recording payment.', 'danger');
        }
    }

    public function show(string $id): void
    {
        $payment = $this->db->fetchOne(
            "SELECT p.*, m.first_name, m.last_name, m.member_number, m.phone as member_phone,
                d.name as dues_name, md.amount_due, md.balance,
                u.full_name as recorded_by_name
             FROM payments p
             LEFT JOIN members m ON m.id = p.member_id
             LEFT JOIN member_dues md ON md.id = p.member_dues_id
             LEFT JOIN dues d ON d.id = md.dues_id
             LEFT JOIN users u ON u.id = p.recorded_by
             WHERE p.id = :id", ['id' => (int)$id]
        );
        if (!$payment) {
            $this->abort(404, 'Payment not found');
        }
        $this->setMenuActive('payments');
        $this->view('payments.show', ['payment' => $payment]);
    }

    public function receipt(string $id): void
    {
        $payment = $this->db->fetchOne(
            "SELECT p.*, m.first_name, m.last_name, m.member_number,
                d.name as dues_name, u.full_name as recorded_by_name
             FROM payments p
             LEFT JOIN members m ON m.id = p.member_id
             LEFT JOIN member_dues md ON md.id = p.member_dues_id
             LEFT JOIN dues d ON d.id = md.dues_id
             LEFT JOIN users u ON u.id = p.recorded_by
             WHERE p.id = :id AND p.status = 'Completed'", ['id' => (int)$id]
        );
        if (!$payment) {
            $this->abort(404, 'Payment not found');
        }
        $profile = $this->db->fetchOne("SELECT * FROM company_profile LIMIT 1") ?? [];
        $this->view('payments.receipt', ['payment' => $payment, 'profile' => $profile]);
    }

    public function downloadReceipt(string $id): void
    {
        $this->authorize('payments.create');
        // PDF receipt generation using Dompdf
        $payment = $this->db->fetchOne(
            "SELECT p.*, m.first_name, m.last_name, m.member_number,
                d.name as dues_name, u.full_name as recorded_by_name
             FROM payments p
             LEFT JOIN members m ON m.id = p.member_id
             LEFT JOIN member_dues md ON md.id = p.member_dues_id
             LEFT JOIN dues d ON d.id = md.dues_id
             LEFT JOIN users u ON u.id = p.recorded_by
             WHERE p.id = :id AND p.status = 'Completed'", ['id' => (int)$id]
        );
        if (!$payment) {
            $this->abort(404, 'Payment not found');
        }

        $profile = $this->db->fetchOne("SELECT * FROM company_profile LIMIT 1") ?? [];
        
        // Try to use Dompdf
        if (class_exists('\Dompdf\Dompdf')) {
            $dompdf = new \Dompdf\Dompdf();
            ob_start();
            $paymentData = $payment;
            $profileData = $profile;
            require __DIR__ . '/../views/payments/receipt_pdf.php';
            $html = ob_get_clean();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $dompdf->stream("receipt_{$payment['receipt_number']}.pdf", ['Attachment' => true]);
            exit;
        }

        // Fallback: redirect to receipt view
        $this->redirect("/payments/{$id}/receipt");
    }

    public function receipts(): void
    {
        $payments = $this->db->fetchAll(
            "SELECT p.*, m.first_name, m.last_name, m.member_number
             FROM payments p LEFT JOIN members m ON m.id = p.member_id
             WHERE p.status = 'Completed' ORDER BY p.payment_date DESC"
        );
        $this->setMenuActive('payments');
        $this->view('payments.receipts', ['payments' => $payments]);
    }

    public function voidPayment(string $id): void
    {
        $this->authorize('payments.void');
        $this->verifyCsrf();

        $payment = $this->db->fetchOne("SELECT * FROM payments WHERE id = :id AND status = 'Completed'", ['id' => (int)$id]);
        if (!$payment) {
            $this->redirect('/payments', 'Payment not found.', 'danger');
            return;
        }

        try {
            $this->db->beginTransaction();

            // Void the payment
            $this->db->execute(
                "UPDATE payments SET status = 'Void', updated_at = NOW() WHERE id = :id",
                ['id' => (int)$id]
            );

            // Reverse the effect on member dues
            if ($payment['member_dues_id']) {
                $md = $this->db->fetchOne("SELECT * FROM member_dues WHERE id = :id", ['id' => $payment['member_dues_id']]);
                if ($md) {
                    $newPaid = max(0, (float)$md['amount_paid'] - (float)$payment['amount']);
                    $newBalance = (float)$md['amount_due'] - $newPaid;

                    if ($newBalance <= 0) {
                        $status = 'Paid';
                    } elseif ($newPaid > 0) {
                        $status = 'Partial';
                    } else {
                        $status = 'Unpaid';
                    }

                    $this->db->execute(
                        "UPDATE member_dues SET amount_paid = :paid, balance = :bal, status = :st, updated_at = NOW() WHERE id = :id",
                        ['paid' => $newPaid, 'bal' => $newBalance, 'st' => $status, 'id' => $payment['member_dues_id']]
                    );
                }
            }

            (new Auth())->logAction('payment_voided', 'payment', (int)$id, "Voided payment {$payment['receipt_number']}: {$payment['amount']}");
            $this->db->commit();

            $this->redirect('/payments', 'Payment voided successfully.', 'warning');
        } catch (\Throwable $e) {
            $this->db->rollBack();
            $this->redirect('/payments', 'Error voiding payment.', 'danger');
        }
    }
}
