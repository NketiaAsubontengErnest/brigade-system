<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class VerificationController extends Controller
{
    public function verifyMember(string $token): void
    {
        $db = Database::getInstance();
        $member = $db->fetchOne(
            "SELECT m.*, s.name as section_name 
             FROM members m 
             LEFT JOIN sections s ON s.id = m.section_id 
             WHERE m.verification_token = :token",
            ['token' => $token]
        );

        if (!$member) {
            http_response_code(404);
            echo '<!DOCTYPE html><html><head><title>Not Found</title><link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"></head><body style="text-align:center;padding:50px;font-family:\'Poppins\';"><h1>Member Not Found</h1><p>This verification token is invalid or the member record does not exist.</p></body></html>';
            return;
        }

        $this->view('public.verify_member', [
            'member' => $member,
        ]);
    }
}
