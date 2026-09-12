<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Core\Database;

class DatabaseSeeder
{
    public function run(Database $db): void
    {
        // Create Super Admin user
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $db->execute(
            "INSERT INTO users (full_name, email, phone, password, role_id, status) 
             VALUES (:name, :email, :phone, :password, :role, 'Active')
             ON DUPLICATE KEY UPDATE id=id",
            [
                'name' => 'Super Admin',
                'email' => 'admin@brigade.com',
                'phone' => '+233200000000',
                'password' => $password,
                'role' => 1, // Super Admin
            ]
        );

        // Create Captain user
        $captainPass = password_hash('captain123', PASSWORD_DEFAULT);
        $db->execute(
            "INSERT INTO users (full_name, email, phone, password, role_id, status)
             VALUES (:name, :email, :phone, :password, :role, 'Active')
             ON DUPLICATE KEY UPDATE id=id",
            [
                'name' => 'Captain James Mensah',
                'email' => 'captain@brigade.com',
                'phone' => '+233200000001',
                'password' => $captainPass,
                'role' => 2, // Captain
            ]
        );

        // Create Treasurer user
        $treasurerPass = password_hash('treasurer123', PASSWORD_DEFAULT);
        $db->execute(
            "INSERT INTO users (full_name, email, phone, password, role_id, status)
             VALUES (:name, :email, :phone, :password, :role, 'Active')
             ON DUPLICATE KEY UPDATE id=id",
            [
                'name' => 'Treasurer Grace Adjei',
                'email' => 'treasurer@brigade.com',
                'phone' => '+233200000002',
                'password' => $treasurerPass,
                'role' => 4, // Treasurer
            ]
        );

        // Create sample members - 21 Boys and 24 Girls (45 total)
        $sections = $db->fetchAll("SELECT id FROM sections LIMIT 5");
        $sectionIds = array_column($sections, 'id');

        $members = [
            // 21 Boys
            ['BGB-2026-0001', 'Emmanuel', 'Kwame', 'Asante', '2008-03-15', 'Male', '+233240000001', 'emmanuel@example.com', '1', 'Active'],
            ['BGB-2026-0002', 'Kwadwo', '', 'Boateng', '2009-01-10', 'Male', '+233240000003', 'kwadwo@example.com', '1', 'Active'],
            ['BGB-2026-0003', 'Kofi', '', 'Amoako', '2008-06-18', 'Male', '+233240000005', 'kofi@example.com', '2', 'Active'],
            ['BGB-2026-0004', 'Yaw', '', 'Frimpong', '2005-09-12', 'Male', '+233240000007', 'yaw@example.com', '3', 'Active'],
            ['BGB-2026-0005', 'Nana', 'Kwesi', 'Acheampong', '2009-08-08', 'Male', '+233240000009', 'nana@example.com', '1', 'Active'],
            ['BGB-2026-0006', 'Kwabena', '', 'Ofori', '2007-11-22', 'Male', '+233240000011', 'kwabena@example.com', '2', 'Active'],
            ['BGB-2026-0007', 'Kojo', 'Yaw', 'Ankomah', '2008-02-14', 'Male', '+233240000013', 'kojo@example.com', '1', 'Active'],
            ['BGB-2026-0008', 'Kweku', '', 'Sarpong', '2009-05-03', 'Male', '+233240000015', 'kweku@example.com', '3', 'Active'],
            ['BGB-2026-0009', 'Yaw', 'Boakye', 'Mensah', '2007-07-19', 'Male', '+233240000017', 'yaw.m@example.com', '2', 'Active'],
            ['BGB-2026-0010', 'Kwame', '', 'Asare', '2008-10-08', 'Male', '+233240000019', 'kwame@example.com', '1', 'Active'],
            ['BGB-2026-0011', 'Ebenezer', '', 'Osei', '2009-12-01', 'Male', '+233240000021', 'ebenezer@example.com', '3', 'Active'],
            ['BGB-2026-0012', 'Isaac', 'Kwesi', 'Tutu', '2006-04-15', 'Male', '+233240000023', 'isaac@example.com', '1', 'Active'],
            ['BGB-2026-0013', 'Samuel', '', 'Owusu', '2007-08-28', 'Male', '+233240000025', 'samuel@example.com', '2', 'Active'],
            ['BGB-2026-0014', 'Daniel', 'Kofi', 'Aidoo', '2008-01-07', 'Male', '+233240000027', 'daniel@example.com', '3', 'Active'],
            ['BGB-2026-0015', 'Michael', '', 'Appiah', '2009-03-12', 'Male', '+233240000029', 'michael@example.com', '1', 'Active'],
            ['BGB-2026-0016', 'Joseph', 'Kwame', 'Adjei', '2007-06-30', 'Male', '+233240000031', 'joseph@example.com', '2', 'Active'],
            ['BGB-2026-0017', 'Stephen', '', 'Darko', '2008-09-18', 'Male', '+233240000033', 'stephen@example.com', '3', 'Active'],
            ['BGB-2026-0018', 'Benjamin', 'Kofi', 'Nkrumah', '2009-02-25', 'Male', '+233240000035', 'benjamin@example.com', '1', 'Active'],
            ['BGB-2026-0019', 'David', '', 'Agyemang', '2006-11-09', 'Male', '+233240000037', 'david@example.com', '2', 'Active'],
            ['BGB-2026-0020', 'Patrick', 'Yaw', 'Quartey', '2007-05-14', 'Male', '+233240000039', 'patrick@example.com', '3', 'Active'],
            ['BGB-2026-0021', 'Anthony', '', 'Cudjoe', '2008-08-22', 'Male', '+233240000041', 'anthony@example.com', '1', 'Active'],
            // 24 Girls
            ['BGB-2026-0022', 'Abena', 'Afia', 'Mensah', '2007-07-22', 'Female', '+233240000002', 'abena@example.com', '2', 'Active'],
            ['BGB-2026-0023', 'Akua', 'Serwaa', 'Osei', '2006-11-05', 'Female', '+233240000004', 'akua@example.com', '3', 'Active'],
            ['BGB-2026-0024', 'Esi', 'Abena', 'Darko', '2010-02-28', 'Female', '+233240000006', 'esi@example.com', '1', 'Active'],
            ['BGB-2026-0025', 'Adwoa', 'Pokuwa', 'Gyamfi', '2007-04-30', 'Female', '+233240000008', 'adwoa@example.com', '2', 'Pending'],
            ['BGB-2026-0026', 'Efua', '', 'Nyarko', '2006-12-25', 'Female', '+233240000010', 'efua@example.com', '3', 'Active'],
            ['BGB-2026-0027', 'Ama', 'Akosua', 'Sarpong', '2008-04-12', 'Female', '+233240000012', 'ama@example.com', '1', 'Active'],
            ['BGB-2026-0028', 'Naa', 'Adjeley', 'Oman', '2009-09-03', 'Female', '+233240000014', 'naa@example.com', '2', 'Active'],
            ['BGB-2026-0029', 'Abena', 'Sampah', 'Adjei', '2007-02-18', 'Female', '+233240000016', 'abena.a@example.com', '3', 'Active'],
            ['BGB-2026-0030', 'Aisha', '', 'Mohammed', '2008-06-25', 'Female', '+233240000018', 'aisha@example.com', '1', 'Active'],
            ['BGB-2026-0031', 'Fati', 'Abdulai', 'Ibrahim', '2009-11-14', 'Female', '+233240000020', 'fati@example.com', '2', 'Active'],
            ['BGB-2026-0032', 'Akosua', '', 'Boakye', '2006-08-07', 'Female', '+233240000022', 'akosua@example.com', '3', 'Active'],
            ['BGB-2026-0033', 'Adeline', 'Kwakyewaa', 'Osei', '2007-03-29', 'Female', '+233240000024', 'adeline@example.com', '1', 'Active'],
            ['BGB-2026-0034', 'Gifty', '', 'Ansah', '2008-12-11', 'Female', '+233240000026', 'gifty@example.com', '2', 'Active'],
            ['BGB-2026-0035', 'Rose', 'Afia', 'Tetteh', '2009-07-06', 'Female', '+233240000028', 'rose@example.com', '3', 'Active'],
            ['BGB-2026-0036', 'Grace', '', 'Boateng', '2007-10-20', 'Female', '+233240000030', 'grace@example.com', '1', 'Active'],
            ['BGB-2026-0037', 'Mercy', 'Akua', 'Asare', '2008-05-16', 'Female', '+233240000032', 'mercy@example.com', '2', 'Active'],
            ['BGB-2026-0038', 'Patricia', '', 'Annor', '2009-01-28', 'Female', '+233240000034', 'patricia@example.com', '3', 'Active'],
            ['BGB-2026-0039', 'Diana', 'Serwaa', 'Asante', '2006-06-13', 'Female', '+233240000036', 'diana@example.com', '1', 'Active'],
            ['BGB-2026-0040', 'Juliana', '', 'Mensah', '2007-09-09', 'Female', '+233240000038', 'juliana@example.com', '2', 'Active'],
            ['BGB-2026-0041', 'Cynthia', 'Abena', 'Quarshie', '2008-02-21', 'Female', '+233240000040', 'cynthia@example.com', '3', 'Active'],
            ['BGB-2026-0042', 'Priscilla', '', 'Oforiwaa', '2009-04-05', 'Female', '+233240000042', 'priscilla@example.com', '1', 'Active'],
            ['BGB-2026-0043', 'Elizabeth', 'Kwakyewaa', 'Darkwah', '2007-12-17', 'Female', '+233240000044', 'elizabeth@example.com', '2', 'Active'],
            ['BGB-2026-0044', 'Theresa', '', 'Osei-Bonsu', '2008-07-31', 'Female', '+233240000046', 'theresa@example.com', '3', 'Active'],
            ['BGB-2026-0045', 'Beatrice', 'Afia', 'Adjei', '2009-10-23', 'Female', '+233240000048', 'beatrice@example.com', '1', 'Active'],
        ];

        foreach ($members as [$num, $first, $middle, $last, $dob, $gender, $phone, $email, $secIdx, $status]) {
            $sectionId = $sectionIds[(int)$secIdx - 1] ?? $sectionIds[0];
            $db->execute(
                "INSERT INTO members (member_number, first_name, middle_name, last_name, date_of_birth, gender, phone, email, section_id, date_joined, status)
                 VALUES (:num, :first, :middle, :last, :dob, :gender, :phone, :email, :section, CURRENT_DATE, :status)
                 ON DUPLICATE KEY UPDATE id=id",
                [
                    'num' => $num, 'first' => $first, 'middle' => $middle, 'last' => $last,
                    'dob' => $dob, 'gender' => $gender, 'phone' => $phone, 'email' => $email,
                    'section' => $sectionId, 'status' => $status,
                ]
            );
        }

        // Create sample activities
        $activities = [
            ['Weekly Drill', 'Regular drill practice session', date('Y-m-d', strtotime('next monday')), '15:00', '17:00', 'Brigade Hall', 1],
            ['Bible Study', 'Weekly bible study', date('Y-m-d', strtotime('next wednesday')), '16:00', '17:30', 'Church Hall', null],
            ['Sports Day', 'Inter-section sports competition', date('Y-m-d', strtotime('next saturday')), '08:00', '16:00', 'Sports Field', null],
        ];

        foreach ($activities as [$name, $desc, $date, $start, $end, $loc, $secId]) {
            $db->execute(
                "INSERT INTO activities (name, description, date, start_time, end_time, location, section_id, status)
                 VALUES (:name, :desc, :date, :start, :end, :loc, :section, 'Scheduled')
                 ON DUPLICATE KEY UPDATE id=id",
                [
                    'name' => $name, 'desc' => $desc, 'date' => $date,
                    'start' => $start, 'end' => $end, 'loc' => $loc,
                    'section' => $secId,
                ]
            );
        }

        // Create sample event
        $db->execute(
            "INSERT INTO events (name, description, start_date, end_date, location, fee, status)
             VALUES (:name, :desc, :start, :end, :loc, :fee, 'Upcoming')
             ON DUPLICATE KEY UPDATE id=id",
            [
                'name' => 'Annual Brigade Camp 2026',
                'desc' => 'Annual camping and training event for all members.',
                'start' => date('Y-m-d', strtotime('+2 months')),
                'end' => date('Y-m-d', strtotime('+2 months +3 days')),
                'loc' => ' Brigade Camp Grounds',
                'fee' => 50.00,
            ]
        );

        // Create sample announcements
        $db->execute(
            "INSERT INTO announcements (title, content, publish_date, status, is_public)
             VALUES (:title, :content, CURRENT_DATE, 'Published', true)
             ON DUPLICATE KEY UPDATE id=id",
            [
                'title' => 'Welcome to the New Brigade Year!',
                'content' => 'We are excited to begin a new year of activities. All members are encouraged to register and participate actively.',
            ]
        );

        // Create sample training courses
        $courses = [
            ['First Aid Training', 'Basic first aid certification course', '2 weeks', 'Dr. Mensah'],
            ['Drill Leadership', 'Advanced drill command and leadership', '4 weeks', 'Capt. Asante'],
            ['Public Speaking', 'Communication and public speaking skills', '3 weeks', 'Mrs. Adjei'],
        ];

        foreach ($courses as [$name, $desc, $duration, $instructor]) {
            $db->execute(
                "INSERT INTO training_courses (name, description, duration, instructor, status)
                 VALUES (:name, :desc, :dur, :inst, 'Active')
                 ON DUPLICATE KEY UPDATE id=id",
                ['name' => $name, 'desc' => $desc, 'dur' => $duration, 'inst' => $instructor]
            );
        }

        // Create sample badges
        $badges = [
            ['First Aid Badge', 'Awarded for completing first aid training', 'Complete first aid training course'],
            ['Drill Badge', 'Awarded for excellent drill performance', 'Pass drill assessment with distinction'],
            ['Service Badge', 'Awarded for community service', 'Complete 20 hours of community service'],
            ['Leadership Badge', 'Awarded for leadership development', 'Complete leadership training program'],
        ];

        foreach ($badges as [$name, $desc, $reqs]) {
            $db->execute(
                "INSERT INTO badges (name, description, requirements)
                 VALUES (:name, :desc, :reqs)
                 ON DUPLICATE KEY UPDATE id=id",
                ['name' => $name, 'desc' => $desc, 'reqs' => $reqs]
            );
        }
    }
}
