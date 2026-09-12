<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Auth;

class FinanceController extends Controller
{
    private Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->authorize('finance.view');
        $this->db = Database::getInstance();
    }

    public function index(): void
    {
        $totalIncome = (float)($this->db->fetchOne("SELECT COALESCE(SUM(amount), 0) as t FROM income WHERE status = 'Recorded'")['t'] ?? 0);
        $totalExpenses = (float)($this->db->fetchOne("SELECT COALESCE(SUM(amount), 0) as t FROM expenses WHERE status IN ('Recorded', 'Approved')")['t'] ?? 0);
        $balance = $totalIncome - $totalExpenses;

        $monthlyData = $this->db->fetchAll("
            SELECT DATE_FORMAT(d, '%b') as label, COALESCE(i.income, 0) as income, COALESCE(e.expenses, 0) as expenses
            FROM (
                SELECT DATE_SUB(CURDATE(), INTERVAL 5 MONTH) as d
                UNION ALL SELECT DATE_SUB(CURDATE(), INTERVAL 4 MONTH)
                UNION ALL SELECT DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
                UNION ALL SELECT DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
                UNION ALL SELECT DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
                UNION ALL SELECT CURDATE()
            ) months
            LEFT JOIN (SELECT DATE_FORMAT(date, '%Y-%m-01') as m, SUM(amount) as income FROM income WHERE status = 'Recorded' GROUP BY m) i ON i.m = DATE_FORMAT(d, '%Y-%m-%d')
            LEFT JOIN (SELECT DATE_FORMAT(date, '%Y-%m-01') as m, SUM(amount) as expenses FROM expenses WHERE status IN ('Recorded', 'Approved') GROUP BY m) e ON e.m = DATE_FORMAT(d, '%Y-%m-%d')
            ORDER BY d
        ");

        $recentIncome = $this->db->fetchAll("SELECT * FROM income WHERE status = 'Recorded' ORDER BY date DESC LIMIT 5");
        $recentExpenses = $this->db->fetchAll("SELECT * FROM expenses WHERE status IN ('Recorded', 'Approved') ORDER BY date DESC LIMIT 5");

        $this->setMenuActive('finance');
        $this->view('finance.index', [
            'totalIncome' => $totalIncome, 'totalExpenses' => $totalExpenses,
            'balance' => $balance, 'monthlyData' => $monthlyData,
            'recentIncome' => $recentIncome, 'recentExpenses' => $recentExpenses,
        ]);
    }

    public function income(): void
    {
        $request = new \App\Core\Request();
        $page = max(1, (int)$request->input('page', 1));

        $total = (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM income WHERE status = 'Recorded'")['c'];
        $pagination = $this->paginate($total, 20, $page);

        $income = $this->db->fetchAll(
            "SELECT i.*, u.full_name as recorded_by_name FROM income i LEFT JOIN users u ON u.id = i.recorded_by
             WHERE i.status = 'Recorded' ORDER BY i.date DESC LIMIT :limit OFFSET :offset",
            ['limit' => $pagination['per_page'], 'offset' => $pagination['offset']]
        );

        $this->setMenuActive('finance');
        $this->view('finance.income', ['income' => $income, 'pagination' => $pagination]);
    }

    public function createIncome(): void
    {
        $this->authorize('finance.create');
        $this->setMenuActive('finance');
        $this->view('finance.create_income');
    }

    public function storeIncome(): void
    {
        $this->authorize('finance.create');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $data = $request->only(['category', 'description', 'amount', 'date', 'payment_method', 'reference']);

        $this->db->execute(
            "INSERT INTO income (category, description, amount, date, payment_method, reference, recorded_by)
             VALUES (:cat, :desc, :amt, :date, :method, :ref, :by)",
            [
                'cat' => $data['category'], 'desc' => $data['description'] ?? null,
                'amt' => $data['amount'], 'date' => $data['date'] ?: date('Y-m-d'),
                'method' => $data['payment_method'] ?? null, 'ref' => $data['reference'] ?? null,
                'by' => auth()->id(),
            ]
        );

        (new Auth())->logAction('income_recorded', 'income', null, "Recorded income: {$data['category']} - {$data['amount']}");
        $this->redirect('/finance/income', 'Income recorded successfully.', 'success');
    }

    public function incomeDetail(string $id): void
    {
        $record = $this->db->fetchOne(
            "SELECT i.*, u.full_name as recorded_by_name FROM income i LEFT JOIN users u ON u.id = i.recorded_by WHERE i.id = :id",
            ['id' => (int)$id]
        );
        if (!$record) {
            $this->abort(404);
        }
        $this->setMenuActive('finance');
        $this->view('finance.income_detail', ['record' => $record]);
    }

    public function expenses(): void
    {
        $request = new \App\Core\Request();
        $page = max(1, (int)$request->input('page', 1));

        $total = (int)$this->db->fetchOne("SELECT COUNT(*) as c FROM expenses WHERE status != 'Void'")['c'];
        $pagination = $this->paginate($total, 20, $page);

        $expenses = $this->db->fetchAll(
            "SELECT e.*, u.full_name as recorded_by_name, a.full_name as approved_by_name
             FROM expenses e LEFT JOIN users u ON u.id = e.recorded_by LEFT JOIN users a ON a.id = e.approved_by
             WHERE e.status != 'Void' ORDER BY e.date DESC LIMIT :limit OFFSET :offset",
            ['limit' => $pagination['per_page'], 'offset' => $pagination['offset']]
        );

        $this->setMenuActive('finance');
        $this->view('finance.expenses', ['expenses' => $expenses, 'pagination' => $pagination]);
    }

    public function createExpense(): void
    {
        $this->authorize('finance.create');
        $this->setMenuActive('finance');
        $this->view('finance.create_expense');
    }

    public function storeExpense(): void
    {
        $this->authorize('finance.create');
        $this->verifyCsrf();

        $request = new \App\Core\Request();
        $data = $request->only(['category', 'description', 'amount', 'date', 'payment_method', 'reference']);

        $this->db->execute(
            "INSERT INTO expenses (category, description, amount, date, payment_method, reference, recorded_by)
             VALUES (:cat, :desc, :amt, :date, :method, :ref, :by)",
            [
                'cat' => $data['category'], 'desc' => $data['description'] ?? null,
                'amt' => $data['amount'], 'date' => $data['date'] ?: date('Y-m-d'),
                'method' => $data['payment_method'] ?? null, 'ref' => $data['reference'] ?? null,
                'by' => auth()->id(),
            ]
        );

        (new Auth())->logAction('expense_recorded', 'expense', null, "Recorded expense: {$data['category']} - {$data['amount']}");
        $this->redirect('/finance/expenses', 'Expense recorded successfully.', 'success');
    }

    public function expenseDetail(string $id): void
    {
        $record = $this->db->fetchOne(
            "SELECT e.*, u.full_name as recorded_by_name, a.full_name as approved_by_name
             FROM expenses e LEFT JOIN users u ON u.id = e.recorded_by LEFT JOIN users a ON a.id = e.approved_by WHERE e.id = :id",
            ['id' => (int)$id]
        );
        if (!$record) {
            $this->abort(404);
        }
        $this->setMenuActive('finance');
        $this->view('finance.expense_detail', ['record' => $record]);
    }

    public function approveExpense(string $id): void
    {
        $this->authorize('finance.approve');
        $this->verifyCsrf();

        $this->db->execute(
            "UPDATE expenses SET status = 'Approved', approved_by = :uid WHERE id = :id AND status = 'Recorded'",
            ['uid' => auth()->id(), 'id' => (int)$id]
        );

        (new Auth())->logAction('expense_approved', 'expense', (int)$id, "Approved expense #{$id}");
        $this->redirect('/finance/expenses', 'Expense approved.', 'success');
    }

    public function voidExpense(string $id): void
    {
        $this->authorize('finance.approve');
        $this->verifyCsrf();

        $this->db->execute("UPDATE expenses SET status = 'Void' WHERE id = :id", ['id' => (int)$id]);
        (new Auth())->logAction('expense_voided', 'expense', (int)$id, "Voided expense #{$id}");
        $this->redirect('/finance/expenses', 'Expense voided.', 'warning');
    }
}
