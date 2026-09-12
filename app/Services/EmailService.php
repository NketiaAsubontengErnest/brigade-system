<?php

declare(strict_types=1);

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class EmailService
{
    private array $config;

    public function __construct()
    {
        $this->config = [
            'host' => $_ENV['MAIL_HOST'] ?? '127.0.0.1',
            'port' => (int)($_ENV['MAIL_PORT'] ?? 1025),
            'username' => $_ENV['MAIL_USERNAME'] ?? '',
            'password' => $_ENV['MAIL_PASSWORD'] ?? '',
            'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? '',
            'from_address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'no-reply@brigade.org',
            'from_name' => $_ENV['MAIL_FROM_NAME'] ?? 'Brigade Company Management',
            'mailer' => $_ENV['MAIL_MAILER'] ?? 'smtp',
        ];
    }

    /**
     * Send email via PHPMailer or fallback log.
     */
    public function send(string $toEmail, string $toName, string $subject, string $bodyHtml, string $bodyText = '', array $attachments = []): bool
    {
        // If mailer is set to log or host is empty/null, record to file log
        if (strtolower($this->config['mailer']) === 'log' || empty($this->config['host']) || $this->config['host'] === 'null') {
            return $this->logEmail($toEmail, $toName, $subject, $bodyHtml);
        }

        try {
            $mail = new PHPMailer(true);

            // Server settings
            $mail->isSMTP();
            $mail->Host       = $this->config['host'];
            $mail->SMTPAuth   = !empty($this->config['username']);
            $mail->Username   = $this->config['username'];
            $mail->Password   = $this->config['password'];
            $mail->Port       = $this->config['port'];

            if (!empty($this->config['encryption'])) {
                $mail->SMTPSecure = $this->config['encryption'] === 'tls' 
                    ? PHPMailer::ENCRYPTION_STARTTLS 
                    : PHPMailer::ENCRYPTION_SMTPS;
            }

            // Recipients
            $mail->setFrom($this->config['from_address'], $this->config['from_name']);
            $mail->addAddress($toEmail, $toName);

            // Attachments
            foreach ($attachments as $filePath => $fileName) {
                if (file_exists($filePath)) {
                    $mail->addAttachment($filePath, is_string($fileName) ? $fileName : '');
                }
            }

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $bodyHtml;
            $mail->AltBody = $bodyText ?: strip_tags($bodyHtml);

            $mail->send();
            return true;
        } catch (\Throwable $e) {
            // Log failure safely and return false
            error_log("PHPMailer failed: " . $e->getMessage());
            $this->logEmail($toEmail, $toName, "[FAILED SMTP] " . $subject, $bodyHtml . "\n\nError: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send Password Reset Link.
     */
    public function sendPasswordReset(string $toEmail, string $toName, string $resetUrl): bool
    {
        $subject = "Password Reset Request - Brigade Management System";
        $html = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px;'>
                <h2 style='color: #0d6efd;'>Brigade Company Management System</h2>
                <p>Hello " . htmlspecialchars($toName) . ",</p>
                <p>We received a request to reset your account password. Click the button below to set a new password:</p>
                <p style='text-align: center; margin: 30px 0;'>
                    <a href='" . htmlspecialchars($resetUrl) . "' style='background-color: #0d6efd; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;'>Reset My Password</a>
                </p>
                <p>Or copy and paste this URL into your browser:</p>
                <p style='word-break: break-all; color: #555; background: #f8f9fa; padding: 10px; border-radius: 4px;'>" . htmlspecialchars($resetUrl) . "</p>
                <p>This password reset link will expire in 1 hour.</p>
                <p>If you did not request a password reset, please ignore this email.</p>
                <hr style='border: none; border-top: 1px solid #eee; margin-top: 20px;'>
                <p style='font-size: 12px; color: #888;'>Brigade Company Management System</p>
            </div>
        ";

        return $this->send($toEmail, $toName, $subject, $html);
    }

    /**
     * Send Welcome Email to New Member or User.
     */
    public function sendWelcomeEmail(string $toEmail, string $toName, string $memberNumber = ''): bool
    {
        $subject = "Welcome to the Brigade!";
        $html = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px;'>
                <h2 style='color: #0d6efd;'>Welcome to Brigade Management System</h2>
                <p>Hello " . htmlspecialchars($toName) . ",</p>
                <p>Your member account has been successfully created and approved.</p>
                " . ($memberNumber ? "<p><strong>Your Member Number:</strong> " . htmlspecialchars($memberNumber) . "</p>" : "") . "
                <p>You can now log into your Member Portal to view your digital membership card, attendance records, badges, and dues.</p>
                <hr style='border: none; border-top: 1px solid #eee; margin-top: 20px;'>
                <p style='font-size: 12px; color: #888;'>Brigade Company Management System</p>
            </div>
        ";

        return $this->send($toEmail, $toName, $subject, $html);
    }

    /**
     * Send Receipt Email with PDF.
     */
    public function sendReceiptEmail(string $toEmail, string $toName, array $payment, string $pdfPath = ''): bool
    {
        $subject = "Payment Receipt - " . ($payment['receipt_number'] ?? 'Receipt');
        $attachments = $pdfPath && file_exists($pdfPath) ? [$pdfPath => basename($pdfPath)] : [];
        $html = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px;'>
                <h2 style='color: #198754;'>Payment Receipt Received</h2>
                <p>Hello " . htmlspecialchars($toName) . ",</p>
                <p>Thank you for your payment. Below are the payment details:</p>
                <ul>
                    <li><strong>Receipt #:</strong> " . htmlspecialchars($payment['receipt_number'] ?? '') . "</li>
                    <li><strong>Amount:</strong> GHS " . number_format((float)($payment['amount'] ?? 0), 2) . "</li>
                    <li><strong>Payment Date:</strong> " . htmlspecialchars($payment['payment_date'] ?? date('Y-m-d')) . "</li>
                    <li><strong>Payment Method:</strong> " . htmlspecialchars($payment['payment_method'] ?? 'Cash') . "</li>
                </ul>
                <p>Your official receipt is attached to this email.</p>
                <hr style='border: none; border-top: 1px solid #eee; margin-top: 20px;'>
                <p style='font-size: 12px; color: #888;'>Brigade Company Management System</p>
            </div>
        ";

        return $this->send($toEmail, $toName, $subject, $html, '', $attachments);
    }

    /**
     * Log email to disk as fallback.
     */
    private function logEmail(string $toEmail, string $toName, string $subject, string $body): bool
    {
        $logDir = __DIR__ . '/../../storage/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0777, true);
        }

        $logFile = $logDir . '/mail.log';
        $logEntry = sprintf(
            "[%s] TO: %s <%s> | SUBJECT: %s\n----------------------------------------\n%s\n========================================\n\n",
            date('Y-m-d H:i:s'),
            $toName,
            $toEmail,
            $subject,
            strip_tags($body)
        );

        return file_put_contents($logFile, $logEntry, FILE_APPEND) !== false;
    }
}
