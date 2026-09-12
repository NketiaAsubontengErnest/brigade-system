<?php

declare(strict_types=1);

namespace Database\Migrations;

use App\Core\Database;

class CreateDues
{
    public function up(Database $db): void
    {
        // Dues Types
        $db->execute("
            CREATE TABLE dues_types (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                description TEXT,
                default_amount DECIMAL(10,2) DEFAULT 0,
                status VARCHAR(20) DEFAULT 'Active' CHECK (status IN ('Active', 'Inactive')),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Dues
        $db->execute("
            CREATE TABLE dues (
                id INT AUTO_INCREMENT PRIMARY KEY,
                dues_type_id INTEGER REFERENCES dues_types(id) ON DELETE SET NULL,
                name VARCHAR(200) NOT NULL,
                amount DECIMAL(10,2) NOT NULL,
                period VARCHAR(50),
                start_date DATE,
                due_date DATE,
                section_id INTEGER REFERENCES sections(id) ON DELETE SET NULL,
                applicable_to VARCHAR(50) DEFAULT 'All Active Members',
                status VARCHAR(20) DEFAULT 'Active' CHECK (status IN ('Active', 'Closed', 'Cancelled')),
                notes TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $db->execute("CREATE INDEX idx_dues_type ON dues(dues_type_id)");
        $db->execute("CREATE INDEX idx_dues_status ON dues(status)");
        $db->execute("CREATE INDEX idx_dues_due_date ON dues(due_date)");

        // Member Dues
        $db->execute("
            CREATE TABLE member_dues (
                id INT AUTO_INCREMENT PRIMARY KEY,
                member_id INTEGER REFERENCES members(id) ON DELETE CASCADE,
                dues_id INTEGER REFERENCES dues(id) ON DELETE CASCADE,
                amount_due DECIMAL(10,2) NOT NULL,
                amount_paid DECIMAL(10,2) DEFAULT 0,
                balance DECIMAL(10,2) NOT NULL,
                status VARCHAR(20) DEFAULT 'Unpaid' CHECK (status IN ('Unpaid', 'Partial', 'Paid', 'Overdue')),
                due_date DATE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE(member_id, dues_id)
            )
        ");

        $db->execute("CREATE INDEX idx_member_dues_member ON member_dues(member_id)");
        $db->execute("CREATE INDEX idx_member_dues_dues ON member_dues(dues_id)");
        $db->execute("CREATE INDEX idx_member_dues_status ON member_dues(status)");
        $db->execute("CREATE INDEX idx_member_dues_due_date ON member_dues(due_date)");

        // Payments
        $db->execute("
            CREATE TABLE payments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                member_id INTEGER REFERENCES members(id) ON DELETE SET NULL,
                member_dues_id INTEGER REFERENCES member_dues(id) ON DELETE SET NULL,
                amount DECIMAL(10,2) NOT NULL CHECK (amount > 0),
                payment_method VARCHAR(30) CHECK (payment_method IN ('Cash', 'Mobile Money', 'Bank Transfer', 'Other')),
                reference_number VARCHAR(100),
                receipt_number VARCHAR(50) UNIQUE NOT NULL,
                payment_date DATE NOT NULL DEFAULT CURRENT_DATE,
                recorded_by INTEGER REFERENCES users(id) ON DELETE SET NULL,
                status VARCHAR(20) DEFAULT 'Completed' CHECK (status IN ('Completed', 'Pending', 'Void', 'Refunded')),
                notes TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $db->execute("CREATE INDEX idx_payments_member ON payments(member_id)");
        $db->execute("CREATE INDEX idx_payments_dues ON payments(member_dues_id)");
        $db->execute("CREATE INDEX idx_payments_receipt ON payments(receipt_number)");
        $db->execute("CREATE INDEX idx_payments_date ON payments(payment_date)");
        $db->execute("CREATE INDEX idx_payments_status ON payments(status)");

        // Insert default dues types
        $duesTypes = [
            ['Monthly Dues', 'Regular monthly contribution', 20.00],
            ['Annual Dues', 'Annual membership fee', 100.00],
            ['Registration Dues', 'One-time registration fee', 50.00],
            ['Camp Dues', 'Annual camp contribution', 75.00],
            ['Uniform Dues', 'Uniform purchase/installment', 200.00],
            ['Training Dues', 'Training program fee', 30.00],
            ['Special Contribution', 'Special fundraising', 0.00],
            ['Event Dues', 'Event-specific fee', 0.00],
        ];

        foreach ($duesTypes as [$name, $desc, $amount]) {
            $db->execute(
                "INSERT INTO dues_types (name, description, default_amount) VALUES (:name, :desc, :amount)",
                ['name' => $name, 'desc' => $desc, 'amount' => $amount]
            );
        }
    }
}
