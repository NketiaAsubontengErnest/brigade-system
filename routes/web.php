<?php

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\MemberController;
use App\Controllers\SectionController;
use App\Controllers\OfficerController;
use App\Controllers\ActivityController;
use App\Controllers\AttendanceController;
use App\Controllers\EventController;
use App\Controllers\DuesController;
use App\Controllers\PaymentController;
use App\Controllers\FinanceController;
use App\Controllers\TrainingController;
use App\Controllers\BadgeController;
use App\Controllers\AwardController;
use App\Controllers\AnnouncementController;
use App\Controllers\NewsController;
use App\Controllers\GalleryController;
use App\Controllers\DocumentController;
use App\Controllers\ReportController;
use App\Controllers\UserController;
use App\Controllers\SettingsController;
use App\Controllers\RankController;
use App\Controllers\PublicController;
use App\Controllers\MemberPortalController;
use App\Controllers\GuardianPortalController;
use App\Controllers\ProfileController;
use App\Controllers\VerificationController;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\PermissionMiddleware;

use App\Controllers\SitemapController;

$router = new Router();

// ============================================
// PUBLIC WEBSITE
// ============================================
$router->group('', [], function ($router) {
    $router->get('/sitemap.xml', [SitemapController::class, 'index']);
    $router->get('/', [PublicController::class, 'index']);
    $router->get('/about', [PublicController::class, 'about']);
    $router->get('/leadership', [PublicController::class, 'leadership']);
    $router->get('/membership', [PublicController::class, 'membership']);
    // Public content pages - open to visitors, populated from the admin panel
    $router->get('/activities', [PublicController::class, 'activities']);
    $router->get('/events', [PublicController::class, 'events']);
    $router->get('/events/view/{id}', [PublicController::class, 'eventDetail']);
    $router->get('/news', [PublicController::class, 'news']);
    $router->get('/news/article/{slug}', [PublicController::class, 'newsDetail']);
    $router->get('/gallery', [PublicController::class, 'gallery']);
    $router->get('/gallery/view/{id}', [PublicController::class, 'galleryAlbum']);
    $router->get('/contact', [PublicController::class, 'contact']);
    // Registration disabled - members can only be added by Super Admin or Captain via admin panel
    // $router->get('/register', [PublicController::class, 'register']);
    // $router->post('/register', [PublicController::class, 'registerSubmit']);
    $router->get('/verify/member/{token}', [VerificationController::class, 'verifyMember']);
});

// ============================================
// AUTHENTICATION
// ============================================
$router->group('/login', [GuestMiddleware::class], function ($router) {
    $router->get('', [AuthController::class, 'loginForm']);
    $router->post('', [AuthController::class, 'login']);
});

$router->post('/logout', [AuthController::class, 'logout']);

$router->group('/forgot-password', [GuestMiddleware::class], function ($router) {
    $router->get('', [AuthController::class, 'forgotPasswordForm']);
    $router->post('', [AuthController::class, 'forgotPassword']);
});

$router->group('/reset-password', [GuestMiddleware::class], function ($router) {
    $router->get('', [AuthController::class, 'resetPasswordForm']);
    $router->post('', [AuthController::class, 'resetPassword']);
});

// ============================================
// ADMIN DASHBOARD & PROFILE
// ============================================
$router->group('/dashboard', [AuthMiddleware::class], function ($router) {
    $router->get('', [DashboardController::class, 'index']);
});

$router->group('/profile', [AuthMiddleware::class], function ($router) {
    $router->get('', [ProfileController::class, 'index']);
    $router->post('', [ProfileController::class, 'updateProfile']);
    $router->post('/password', [ProfileController::class, 'updatePassword']);
});

// ============================================
// MEMBERS
// ============================================
$router->group('/members', [AuthMiddleware::class], function ($router) {
    $router->get('', [MemberController::class, 'index']);
    $router->get('/create', [MemberController::class, 'create']);
    $router->post('', [MemberController::class, 'store']);
    $router->get('/import', [MemberController::class, 'importForm']);
    $router->get('/import/template', [MemberController::class, 'importTemplate']);
    $router->post('/import/preview', [MemberController::class, 'importPreview']);
    $router->post('/import/process', [MemberController::class, 'importProcess']);
    $router->get('/pending', [MemberController::class, 'pending']);
    $router->get('/{id}', [MemberController::class, 'show']);
    $router->get('/{id}/edit', [MemberController::class, 'edit']);
    $router->put('/{id}', [MemberController::class, 'update']);
    $router->post('/{id}/approve', [MemberController::class, 'approve']);
    $router->post('/{id}/reject', [MemberController::class, 'reject']);
    $router->post('/{id}/deactivate', [MemberController::class, 'deactivate']);
    $router->post('/{id}/delete', [MemberController::class, 'delete']);
    $router->get('/{id}/card', [MemberController::class, 'membershipCard']);
    $router->get('/api/search', [MemberController::class, 'search']);
});

// ============================================
// SECTIONS
// ============================================
$router->group('/sections', [AuthMiddleware::class], function ($router) {
    $router->get('', [SectionController::class, 'index']);
    $router->get('/create', [SectionController::class, 'create']);
    $router->post('', [SectionController::class, 'store']);
    $router->get('/{id}', [SectionController::class, 'show']);
    $router->get('/{id}/edit', [SectionController::class, 'edit']);
    $router->put('/{id}', [SectionController::class, 'update']);
    $router->post('/{id}/toggle', [SectionController::class, 'toggle']);
    $router->post('/{id}/delete', [SectionController::class, 'delete']);
});

// ============================================
// OFFICERS
// ============================================
$router->group('/officers', [AuthMiddleware::class], function ($router) {
    $router->get('', [OfficerController::class, 'index']);
    $router->get('/create', [OfficerController::class, 'create']);
    $router->post('', [OfficerController::class, 'store']);
    // Literal paths must be registered before '/{id}', which would otherwise match them
    $router->get('/positions', [OfficerController::class, 'positions']);
    $router->post('/positions', [OfficerController::class, 'storePosition']);
    $router->put('/positions/{id}', [OfficerController::class, 'updatePosition']);
    $router->get('/{id}', [OfficerController::class, 'show']);
    $router->get('/{id}/edit', [OfficerController::class, 'edit']);
    $router->put('/{id}', [OfficerController::class, 'update']);
    $router->post('/{id}/deactivate', [OfficerController::class, 'deactivate']);
});

// ============================================
// ACTIVITIES
// ============================================
$router->group('/activities', [AuthMiddleware::class], function ($router) {
    $router->get('/manage', [ActivityController::class, 'index']);
    $router->get('/create', [ActivityController::class, 'create']);
    $router->post('', [ActivityController::class, 'store']);
    $router->get('/{id}', [ActivityController::class, 'show']);
    $router->get('/{id}/edit', [ActivityController::class, 'edit']);
    $router->put('/{id}', [ActivityController::class, 'update']);
    $router->post('/{id}/delete', [ActivityController::class, 'delete']);
});

// ============================================
// ATTENDANCE
// ============================================
$router->group('/attendance', [AuthMiddleware::class], function ($router) {
    $router->get('', [AttendanceController::class, 'index']);
    $router->get('/scan', [AttendanceController::class, 'scanForm']);
    $router->post('/api/scan-mark', [AttendanceController::class, 'scanMarkApi']);
    $router->get('/mark', [AttendanceController::class, 'markForm']);
    $router->post('/mark', [AttendanceController::class, 'mark']);
    $router->get('/session/{id}', [AttendanceController::class, 'session']);
    $router->get('/member/{id}', [AttendanceController::class, 'memberAttendance']);
    $router->post('/bulk-mark', [AttendanceController::class, 'bulkMark']);
});

// ============================================
// EVENTS
// ============================================
$router->group('/events', [AuthMiddleware::class], function ($router) {
    $router->get('/manage', [EventController::class, 'index']);
    $router->get('/create', [EventController::class, 'create']);
    $router->post('', [EventController::class, 'store']);
    $router->get('/{id}', [EventController::class, 'show']);
    $router->get('/{id}/edit', [EventController::class, 'edit']);
    $router->put('/{id}', [EventController::class, 'update']);
    $router->post('/{id}/delete', [EventController::class, 'delete']);
    $router->post('/{id}/register', [EventController::class, 'registerMember']);
    $router->get('/{id}/registrations', [EventController::class, 'registrations']);
});

// ============================================
// DUES
// ============================================
$router->group('/dues', [AuthMiddleware::class], function ($router) {
    $router->get('', [DuesController::class, 'index']);
    $router->get('/dashboard', [DuesController::class, 'dashboard']);
    $router->get('/types', [DuesController::class, 'types']);
    $router->post('/types', [DuesController::class, 'storeType']);
    $router->put('/types/{id}', [DuesController::class, 'updateType']);
    $router->get('/create', [DuesController::class, 'create']);
    $router->post('', [DuesController::class, 'store']);
    // Literal paths must be registered before '/{id}', which would otherwise match them
    $router->get('/member-dues', [DuesController::class, 'memberDues']);
    $router->get('/outstanding', [DuesController::class, 'outstanding']);
    $router->get('/overdue', [DuesController::class, 'overdue']);
    $router->get('/{id}', [DuesController::class, 'show']);
    $router->get('/{id}/members', [DuesController::class, 'members']);
});

// ============================================
// PAYMENTS
// ============================================
$router->group('/payments', [AuthMiddleware::class], function ($router) {
    $router->get('', [PaymentController::class, 'index']);
    $router->get('/create', [PaymentController::class, 'create']);
    $router->post('', [PaymentController::class, 'store']);
    // Literal paths must be registered before '/{id}', which would otherwise match them
    $router->get('/receipts', [PaymentController::class, 'receipts']);
    $router->get('/{id}', [PaymentController::class, 'show']);
    $router->get('/{id}/receipt', [PaymentController::class, 'receipt']);
    $router->get('/{id}/receipt/download', [PaymentController::class, 'downloadReceipt']);
    $router->post('/{id}/void', [PaymentController::class, 'voidPayment']);
});

// ============================================
// FINANCE
// ============================================
$router->group('/finance', [AuthMiddleware::class], function ($router) {
    $router->get('', [FinanceController::class, 'index']);
    $router->get('/income', [FinanceController::class, 'income']);
    $router->get('/income/create', [FinanceController::class, 'createIncome']);
    $router->post('/income', [FinanceController::class, 'storeIncome']);
    $router->get('/income/{id}', [FinanceController::class, 'incomeDetail']);
    $router->get('/expenses', [FinanceController::class, 'expenses']);
    $router->get('/expenses/create', [FinanceController::class, 'createExpense']);
    $router->post('/expenses', [FinanceController::class, 'storeExpense']);
    $router->get('/expenses/{id}', [FinanceController::class, 'expenseDetail']);
    $router->post('/expenses/{id}/approve', [FinanceController::class, 'approveExpense']);
    $router->post('/expenses/{id}/void', [FinanceController::class, 'voidExpense']);
});

// ============================================
// TRAINING
// ============================================
$router->group('/training', [AuthMiddleware::class], function ($router) {
    $router->get('', [TrainingController::class, 'index']);
    $router->get('/create', [TrainingController::class, 'create']);
    $router->post('', [TrainingController::class, 'store']);
    $router->get('/{id}', [TrainingController::class, 'show']);
    $router->get('/{id}/edit', [TrainingController::class, 'edit']);
    $router->put('/{id}', [TrainingController::class, 'update']);
    $router->post('/{id}/enroll', [TrainingController::class, 'enrollMember']);
    $router->put('/enrollment/{id}', [TrainingController::class, 'updateEnrollment']);
    $router->get('/{id}/enrollments', [TrainingController::class, 'enrollments']);
    $router->post('/{id}/delete', [TrainingController::class, 'delete']);
});

// ============================================
// BADGES & AWARDS
// ============================================
$router->group('/badges', [AuthMiddleware::class], function ($router) {
    $router->get('', [BadgeController::class, 'index']);
    $router->get('/create', [BadgeController::class, 'create']);
    $router->post('', [BadgeController::class, 'store']);
    $router->get('/{id}', [BadgeController::class, 'show']);
    $router->get('/{id}/edit', [BadgeController::class, 'edit']);
    $router->put('/{id}', [BadgeController::class, 'update']);
    $router->post('/{id}/award', [BadgeController::class, 'award']);
    $router->get('/{id}/members', [BadgeController::class, 'members']);
    $router->post('/{id}/delete', [BadgeController::class, 'delete']);
});

$router->group('/awards', [AuthMiddleware::class], function ($router) {
    $router->get('', [AwardController::class, 'index']);
    $router->get('/create', [AwardController::class, 'create']);
    $router->post('', [AwardController::class, 'store']);
    $router->get('/{id}', [AwardController::class, 'show']);
    $router->get('/{id}/edit', [AwardController::class, 'edit']);
    $router->put('/{id}', [AwardController::class, 'update']);
});

// ============================================
// ANNOUNCEMENTS
// ============================================
$router->group('/announcements', [AuthMiddleware::class], function ($router) {
    $router->get('', [AnnouncementController::class, 'index']);
    $router->get('/create', [AnnouncementController::class, 'create']);
    $router->post('', [AnnouncementController::class, 'store']);
    $router->get('/{id}', [AnnouncementController::class, 'show']);
    $router->get('/{id}/edit', [AnnouncementController::class, 'edit']);
    $router->put('/{id}', [AnnouncementController::class, 'update']);
    $router->post('/{id}/delete', [AnnouncementController::class, 'delete']);
});

// ============================================
// NEWS
// ============================================
$router->group('/news', [AuthMiddleware::class], function ($router) {
    $router->get('/manage', [NewsController::class, 'index']);
    $router->get('/create', [NewsController::class, 'create']);
    $router->post('', [NewsController::class, 'store']);
    $router->get('/{id}', [NewsController::class, 'show']);
    $router->get('/{id}/edit', [NewsController::class, 'edit']);
    $router->put('/{id}', [NewsController::class, 'update']);
    $router->post('/{id}/delete', [NewsController::class, 'delete']);
});

// ============================================
// GALLERY
// ============================================
$router->group('/gallery', [AuthMiddleware::class], function ($router) {
    $router->get('/manage', [GalleryController::class, 'index']);
    $router->get('/create', [GalleryController::class, 'createAlbum']);
    $router->post('/albums', [GalleryController::class, 'storeAlbum']);
    $router->get('/album/{id}', [GalleryController::class, 'showAlbum']);
    $router->get('/album/{id}/edit', [GalleryController::class, 'editAlbum']);
    $router->post('/album/{id}/edit', [GalleryController::class, 'updateAlbum']);
    $router->put('/album/{id}', [GalleryController::class, 'updateAlbum']);
    $router->post('/album/{id}/images', [GalleryController::class, 'uploadImages']);
    $router->post('/album/{id}/toggle-status', [GalleryController::class, 'toggleStatusAlbum']);
    $router->post('/image/{id}/delete', [GalleryController::class, 'deleteImage']);
    $router->post('/album/{id}/delete', [GalleryController::class, 'deleteAlbum']);
});

// ============================================
// DOCUMENTS
// ============================================
$router->group('/documents', [AuthMiddleware::class], function ($router) {
    $router->get('', [DocumentController::class, 'index']);
    $router->get('/upload', [DocumentController::class, 'uploadForm']);
    $router->post('/upload', [DocumentController::class, 'upload']);
    $router->get('/{id}', [DocumentController::class, 'show']);
    $router->get('/{id}/download', [DocumentController::class, 'download']);
    $router->post('/{id}/delete', [DocumentController::class, 'delete']);
});

// ============================================
// REPORTS
// ============================================
$router->group('/reports', [AuthMiddleware::class], function ($router) {
    $router->get('', [ReportController::class, 'index']);
    $router->get('/membership', [ReportController::class, 'membership']);
    $router->get('/attendance', [ReportController::class, 'attendance']);
    $router->get('/dues', [ReportController::class, 'dues']);
    $router->get('/finance', [ReportController::class, 'finance']);
    $router->get('/training', [ReportController::class, 'training']);
    $router->get('/events', [ReportController::class, 'events']);
    $router->get('/export/{type}', [ReportController::class, 'export']);
});

// ============================================
// USERS
// ============================================
$router->group('/users', [AuthMiddleware::class], function ($router) {
    $router->get('', [UserController::class, 'index']);
    $router->get('/create', [UserController::class, 'create']);
    $router->post('', [UserController::class, 'store']);
    $router->get('/{id}', [UserController::class, 'show']);
    $router->get('/{id}/edit', [UserController::class, 'edit']);
    $router->put('/{id}', [UserController::class, 'update']);
    $router->post('/{id}/toggle-status', [UserController::class, 'toggleStatus']);
    $router->post('/{id}/reset-password', [UserController::class, 'resetPassword']);
});

// ============================================
// SETTINGS
// ============================================
$router->group('/settings', [AuthMiddleware::class], function ($router) {
    $router->get('', [SettingsController::class, 'index']);
    $router->post('/company', [SettingsController::class, 'updateCompany']);
    $router->post('/logo', [SettingsController::class, 'uploadLogo']);
    $router->post('/cover', [SettingsController::class, 'uploadCover']);
    $router->get('/audit-logs', [SettingsController::class, 'auditLogs']);
    $router->get('/notifications', [SettingsController::class, 'notifications']);
    $router->post('/notifications/{id}/read', [SettingsController::class, 'markRead']);
});

// ============================================
// RANKS MANAGEMENT
// ============================================
$router->group('/settings/ranks', [AuthMiddleware::class], function ($router) {
    $router->get('', [RankController::class, 'index']);
    $router->post('', [RankController::class, 'store']);
    $router->post('/{id}', [RankController::class, 'update']);
    $router->post('/{id}/delete', [RankController::class, 'delete']);
});

// ============================================
// MEMBER PORTAL
// ============================================
$router->group('/portal', [AuthMiddleware::class], function ($router) {
    $router->get('', [MemberPortalController::class, 'dashboard']);
    $router->get('/profile', [MemberPortalController::class, 'profile']);
    $router->get('/attendance', [MemberPortalController::class, 'attendance']);
    $router->get('/dues', [MemberPortalController::class, 'dues']);
    $router->get('/payments', [MemberPortalController::class, 'payments']);
    $router->get('/events', [MemberPortalController::class, 'events']);
    $router->get('/training', [MemberPortalController::class, 'training']);
    $router->get('/badges', [MemberPortalController::class, 'badges']);
    $router->get('/awards', [MemberPortalController::class, 'awards']);
    $router->get('/card', [MemberPortalController::class, 'card']);
    $router->get('/announcements', [MemberPortalController::class, 'announcements']);
    $router->get('/documents', [MemberPortalController::class, 'documents']);
});

// ============================================
// PARENT / GUARDIAN PORTAL
// ============================================
$router->group('/guardian', [AuthMiddleware::class], function ($router) {
    $router->get('', [GuardianPortalController::class, 'dashboard']);
    $router->get('/ward/{id}', [GuardianPortalController::class, 'ward']);
    $router->get('/dues', [GuardianPortalController::class, 'dues']);
    $router->get('/attendance', [GuardianPortalController::class, 'attendance']);
    $router->get('/events', [GuardianPortalController::class, 'events']);
});

