<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Validator;
use App\Core\Auth;

class PublicController extends Controller
{
    private Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
    }

    /**
     * Get the company profile.
     */
    private function getProfile(): array
    {
        $profile = $this->db->fetchOne("SELECT * FROM company_profile LIMIT 1") ?? [];
        // Ensure correct company name
        if (!empty($profile['company_name']) && $profile['company_name'] !== '21st and 24th Accra Boys and Girls Brigade') {
            $this->db->execute(
                "UPDATE company_profile SET company_name = :name WHERE id = :id",
                ['name' => '21st and 24th Accra Boys and Girls Brigade', 'id' => $profile['id']]
            );
            $profile['company_name'] = '21st and 24th Accra Boys and Girls Brigade';
        }
        return $profile;
    }

    public function index(): void
    {
        $profile = $this->getProfile();
        $stats = [
            'members' => (int)($this->db->fetchOne("SELECT COUNT(*) as c FROM members WHERE status = 'Active'")['c'] ?? 0),
            'officers' => (int)($this->db->fetchOne("SELECT COUNT(*) as c FROM officers WHERE status = 'Active'")['c'] ?? 0),
            'sections' => (int)($this->db->fetchOne("SELECT COUNT(*) as c FROM sections WHERE status = 'Active'")['c'] ?? 0),
            'years' => $profile['founded_date'] ? date('Y') - (int)date('Y', strtotime($profile['founded_date'])) : 0,
        ];
        $upcomingEvents = $this->db->fetchAll(
            "SELECT * FROM events WHERE start_date >= CURRENT_DATE AND status = 'Upcoming' ORDER BY start_date ASC LIMIT 5"
        );
        $latestNews = $this->db->fetchAll(
            "SELECT * FROM news WHERE status = 'Published' ORDER BY published_date DESC LIMIT 3"
        );
        $activities = $this->db->fetchAll(
            "SELECT * FROM activities WHERE status != 'Cancelled' ORDER BY date DESC LIMIT 6"
        );
        $galleryImages = $this->db->fetchAll(
            "SELECT gi.*, ga.name as album_name FROM gallery_images gi 
             JOIN gallery_albums ga ON ga.id = gi.album_id 
             WHERE ga.status = 'Active' ORDER BY gi.created_at DESC LIMIT 8"
        );
        $announcements = $this->db->fetchAll(
            "SELECT * FROM announcements WHERE status = 'Published' AND is_public = true 
             AND (expiry_date IS NULL OR expiry_date >= CURRENT_DATE) ORDER BY publish_date DESC LIMIT 3"
        );

        $this->view('public.home', [
            'profile' => $profile,
            'stats' => $stats,
            'upcomingEvents' => $upcomingEvents,
            'latestNews' => $latestNews,
            'activities' => $activities,
            'galleryImages' => $galleryImages,
            'announcements' => $announcements,
            'layout' => 'public',
        ]);
    }

    public function about(): void
    {
        $profile = $this->getProfile();
        $officers = $this->db->fetchAll(
            "SELECT o.*, m.first_name, m.last_name, m.profile_photo, p.name as position_name
             FROM officers o
             JOIN members m ON m.id = o.member_id
             JOIN positions p ON p.id = o.position_id
             WHERE o.status = 'Active'
             ORDER BY p.id ASC"
        );
        $this->view('public.about', [
            'profile' => $profile,
            'officers' => $officers,
            'layout' => 'public',
        ]);
    }

    public function leadership(): void
    {
        $profile = $this->getProfile();
        $officers = $this->db->fetchAll(
            "SELECT o.*, m.first_name, m.last_name, m.profile_photo, p.name as position_name
             FROM officers o
             JOIN members m ON m.id = o.member_id
             JOIN positions p ON p.id = o.position_id
             WHERE o.status = 'Active'
             ORDER BY p.id ASC"
        );
        $this->view('public.leadership', [
            'profile' => $profile,
            'officers' => $officers,
            'layout' => 'public',
        ]);
    }

    public function membership(): void
    {
        $profile = $this->getProfile();
        $sections = $this->db->fetchAll(
            "SELECT * FROM sections WHERE status = 'Active' ORDER BY name"
        );
        $this->view('public.membership', [
            'profile' => $profile,
            'sections' => $sections,
            'layout' => 'public',
        ]);
    }

    public function activities(): void
    {
        $profile = $this->getProfile();
        $upcoming = $this->db->fetchAll(
            "SELECT a.*, s.name as section_name
             FROM activities a
             LEFT JOIN sections s ON s.id = a.section_id
             WHERE a.status != 'Cancelled' AND a.date >= CURRENT_DATE
             ORDER BY a.date ASC"
        );
        $past = $this->db->fetchAll(
            "SELECT a.*, s.name as section_name
             FROM activities a
             LEFT JOIN sections s ON s.id = a.section_id
             WHERE a.status != 'Cancelled' AND a.date < CURRENT_DATE
             ORDER BY a.date DESC
             LIMIT 24"
        );
        $sections = $this->db->fetchAll(
            "SELECT * FROM sections WHERE status = 'Active' ORDER BY name"
        );
        $this->view('public.activities', [
            'profile' => $profile,
            'upcoming' => $upcoming,
            'past' => $past,
            'sections' => $sections,
            'layout' => 'public',
        ]);
    }

    public function events(): void
    {
        $profile = $this->getProfile();
        $upcoming = $this->db->fetchAll(
            "SELECT * FROM events
             WHERE status != 'Cancelled' AND (end_date >= CURRENT_DATE OR start_date >= CURRENT_DATE)
             ORDER BY start_date ASC"
        );
        $past = $this->db->fetchAll(
            "SELECT * FROM events
             WHERE status != 'Cancelled' AND COALESCE(end_date, start_date) < CURRENT_DATE
             ORDER BY start_date DESC
             LIMIT 12"
        );
        $this->view('public.events', [
            'profile' => $profile,
            'upcoming' => $upcoming,
            'past' => $past,
            'layout' => 'public',
        ]);
    }

    public function eventDetail(string $id): void
    {
        $profile = $this->getProfile();
        $event = $this->db->fetchOne(
            "SELECT * FROM events WHERE id = :id AND status != 'Cancelled'",
            ['id' => (int)$id]
        );
        if (!$event) {
            $this->abort(404, 'Event not found');
        }
        $others = $this->db->fetchAll(
            "SELECT * FROM events
             WHERE status != 'Cancelled' AND id != :id AND start_date >= CURRENT_DATE
             ORDER BY start_date ASC LIMIT 3",
            ['id' => (int)$id]
        );
        $this->view('public.event_detail', [
            'profile' => $profile,
            'event' => $event,
            'others' => $others,
            'layout' => 'public',
        ]);
    }

    public function news(): void
    {
        $profile = $this->getProfile();
        $news = $this->db->fetchAll(
            "SELECT n.*, u.full_name as author_name 
             FROM news n 
             LEFT JOIN users u ON u.id = n.author
             WHERE n.status = 'Published' 
             ORDER BY n.published_date DESC"
        );
        $this->view('public.news', [
            'profile' => $profile,
            'news' => $news,
            'layout' => 'public',
        ]);
    }

    public function newsDetail(string $slug): void
    {
        $profile = $this->getProfile();
        $article = $this->db->fetchOne(
            "SELECT n.*, u.full_name as author_name 
             FROM news n 
             LEFT JOIN users u ON u.id = n.author
             WHERE n.slug = :slug AND n.status = 'Published'",
            ['slug' => $slug]
        );
        if (!$article) {
            $this->abort(404, 'Article not found');
        }
        $this->view('public.news_detail', [
            'profile' => $profile,
            'article' => $article,
            'layout' => 'public',
        ]);
    }

    public function gallery(): void
    {
        $profile = $this->getProfile();
        $albums = $this->db->fetchAll(
            "SELECT ga.*,
                    (SELECT COUNT(*) FROM gallery_images WHERE album_id = ga.id) as image_count,
                    COALESCE(ga.cover_image,
                        (SELECT gi.file_path FROM gallery_images gi
                         WHERE gi.album_id = ga.id ORDER BY gi.sort_order ASC, gi.id ASC LIMIT 1)
                    ) as cover
             FROM gallery_albums ga
             WHERE ga.status = 'Active'
             ORDER BY ga.created_at DESC"
        );
        $totalPhotos = (int)($this->db->fetchOne(
            "SELECT COUNT(*) as c FROM gallery_images gi
             JOIN gallery_albums ga ON ga.id = gi.album_id AND ga.status = 'Active'"
        )['c'] ?? 0);
        $this->view('public.gallery', [
            'profile' => $profile,
            'albums' => $albums,
            'totalPhotos' => $totalPhotos,
            'layout' => 'public',
        ]);
    }

    public function galleryAlbum(string $id): void
    {
        $profile = $this->getProfile();
        $album = $this->db->fetchOne(
            "SELECT * FROM gallery_albums WHERE id = :id AND status = 'Active'",
            ['id' => (int)$id]
        );
        if (!$album) {
            $this->abort(404, 'Album not found');
        }
        $images = $this->db->fetchAll(
            "SELECT * FROM gallery_images WHERE album_id = :id ORDER BY sort_order ASC, id ASC",
            ['id' => (int)$id]
        );
        $this->view('public.gallery_album', [
            'profile' => $profile,
            'album' => $album,
            'images' => $images,
            'layout' => 'public',
        ]);
    }

    public function documents(): void
    {
        $profile = $this->getProfile();
        $documents = $this->db->fetchAll(
            "SELECT * FROM documents WHERE is_public = true ORDER BY created_at DESC"
        );
        $this->view('public.documents', [
            'profile' => $profile,
            'documents' => $documents,
            'layout' => 'public',
        ]);
    }

    public function contact(): void
    {
        $profile = $this->getProfile();
        $this->view('public.contact', [
            'profile' => $profile,
            'layout' => 'public',
        ]);
    }

    public function register(): void
    {
        $profile = $this->getProfile();
        $sections = $this->db->fetchAll(
            "SELECT * FROM sections WHERE status = 'Active' ORDER BY name"
        );
        $this->view('public.register', [
            'profile' => $profile,
            'sections' => $sections,
            'layout' => 'public',
        ]);
    }

    public function registerSubmit(): void
    {
        $this->verifyCsrf();
        $request = new \App\Core\Request();
        $data = $request->only([
            'first_name', 'middle_name', 'last_name', 'date_of_birth', 'gender',
            'phone', 'email', 'address', 'section_id',
            'guardian_name', 'guardian_relationship', 'guardian_phone', 'guardian_email',
            'emergency_contact', 'emergency_phone',
        ]);

        // Trim all values
        $data = array_map(fn($v) => is_string($v) ? trim($v) : $v, $data);

        $validator = new Validator();
        $rules = [
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female',
            'phone' => 'required|phone',
            'email' => 'email',
            'section_id' => 'required',
            'guardian_name' => 'required',
            'guardian_phone' => 'required',
        ];

        if (!$validator->validate($data, $rules)) {
            Validator::flashInput($data);
            $this->redirect('/register', 'Please fill in all required fields correctly.', 'danger');
            return;
        }

        try {
            // Create member
            $token = bin2hex(random_bytes(32));
            $this->db->execute(
                "INSERT INTO members (first_name, middle_name, last_name, date_of_birth, gender, phone, email, address, section_id, date_joined, status, verification_token)
                 VALUES (:first, :middle, :last, :dob, :gender, :phone, :email, :address, :section, CURRENT_DATE, 'Pending', :token)",
                [
                    'first' => $data['first_name'], 'middle' => $data['middle_name'] ?? null,
                    'last' => $data['last_name'], 'dob' => $data['date_of_birth'],
                    'gender' => $data['gender'], 'phone' => $data['phone'],
                    'email' => $data['email'] ?? null, 'address' => $data['address'] ?? null,
                    'section' => $data['section_id'], 'token' => $token,
                ]
            );

            $memberId = (int)$this->db->lastInsertId();

            // Create guardian
            $this->db->execute(
                "INSERT INTO guardians (member_id, full_name, relationship, phone, email, is_primary, emergency_contact)
                 VALUES (:member, :name, :rel, :phone, :email, true, true)",
                [
                    'member' => $memberId, 'name' => $data['guardian_name'],
                    'rel' => $data['guardian_relationship'] ?? null,
                    'phone' => $data['guardian_phone'], 'email' => $data['guardian_email'] ?? null,
                ]
            );

            // Notify admin
            $admins = $this->db->fetchAll(
                "SELECT id FROM users WHERE role_id IN (1, 2, 3)"
            );
            foreach ($admins as $admin) {
                $this->db->execute(
                    "INSERT INTO notifications (user_id, title, message, type, link)
                     VALUES (:uid, 'New Member Registration', :msg, 'info', '/members/pending')",
                    [
                        'uid' => $admin['id'],
                        'msg' => $data['first_name'] . ' ' . $data['last_name'] . ' has registered as a new member.',
                    ]
                );
            }

            $this->redirect('/register', 'Registration successful! Your application is being reviewed. You will be contacted once approved.', 'success');
        } catch (\Throwable $e) {
            error_log('Registration error: ' . $e->getMessage());
            $this->redirect('/register', 'An error occurred during registration. Please try again.', 'danger');
        }
    }
}
