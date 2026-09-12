<?php

declare(strict_types=1);

namespace Database\Migrations;

use App\Core\Database;

class CreateFinance
{
    public function up(Database $db): void
    {
        // Income
        $db->execute("
            CREATE TABLE income (
                id INT AUTO_INCREMENT PRIMARY KEY,
                category VARCHAR(100) NOT NULL,
                description TEXT,
                amount DECIMAL(10,2) NOT NULL CHECK (amount > 0),
                date DATE NOT NULL DEFAULT CURRENT_DATE,
                payment_method VARCHAR(30) CHECK (payment_method IN ('Cash', 'Mobile Money', 'Bank Transfer', 'Other')),
                reference VARCHAR(100),
                recorded_by INTEGER REFERENCES users(id) ON DELETE SET NULL,
                status VARCHAR(20) DEFAULT 'Recorded' CHECK (status IN ('Recorded', 'Void')),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $db->execute("CREATE INDEX idx_income_date ON income(date)");
        $db->execute("CREATE INDEX idx_income_category ON income(category)");
        $db->execute("CREATE INDEX idx_income_status ON income(status)");

        // Expenses
        $db->execute("
            CREATE TABLE expenses (
                id INT AUTO_INCREMENT PRIMARY KEY,
                category VARCHAR(100) NOT NULL,
                description TEXT,
                amount DECIMAL(10,2) NOT NULL CHECK (amount > 0),
                date DATE NOT NULL DEFAULT CURRENT_DATE,
                payment_method VARCHAR(30) CHECK (payment_method IN ('Cash', 'Mobile Money', 'Bank Transfer', 'Other')),
                reference VARCHAR(100),
                approved_by INTEGER REFERENCES users(id) ON DELETE SET NULL,
                recorded_by INTEGER REFERENCES users(id) ON DELETE SET NULL,
                status VARCHAR(20) DEFAULT 'Recorded' CHECK (status IN ('Recorded', 'Approved', 'Void')),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $db->execute("CREATE INDEX idx_expenses_date ON expenses(date)");
        $db->execute("CREATE INDEX idx_expenses_category ON expenses(category)");
        $db->execute("CREATE INDEX idx_expenses_status ON expenses(status)");
    }
}
