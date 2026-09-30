<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Login') ?> - 21st & 24th Accra Boys & Girls Brigade</title>
    <?= favicon_tags() ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <style>
        :root {
            --auth-navy: #0a0f1d;
            --auth-navy-light: #162445;
            --auth-gold: #f5c211;
            --auth-gold-light: #ffd700;
            --auth-red: #e61924;
            --auth-cream: #faf8f5;
            --auth-text: #2d3748;
            --auth-muted: #6b7280;
            --auth-border: #e5e7eb;
        }
        *, *::before, *::after { box-sizing: border-box; }
        body {
            font-family: 'Poppins';
            margin: 0;
            background: var(--auth-cream);
            color: var(--auth-text);
            min-height: 100vh;
        }
        .auth-wrapper {
            min-height: 100vh;
            display: flex;
        }
        .auth-branding {
            flex: 0 0 45%;
            background: linear-gradient(160deg, #050812 0%, #0a0f1d 50%, #b9121b 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px;
            position: relative;
            overflow: hidden;
        }
        .auth-branding::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 30% 70%, rgba(201,168,76,0.08) 0%, transparent 50%);
        }
        .auth-branding .brand-content {
            position: relative;
            z-index: 1;
            text-align: center;
            color: #fff;
        }
        .auth-branding .brand-crest {
            width: 100%;
            max-width: 300px;
            height: auto;
            margin-bottom: 28px;
            filter: drop-shadow(0 8px 24px rgba(0,0,0,0.35));
        }
        .auth-branding .brand-shield {
            width: 80px;
            height: 80px;
            background: rgba(201,168,76,0.15);
            border: 2px solid var(--auth-gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 28px;
        }
        .auth-branding .brand-shield i {
            font-size: 2.2rem;
            color: var(--auth-gold);
        }
        .auth-branding h1 {
            font-family: 'Poppins';
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }
        .auth-branding .brand-subtitle {
            font-size: 1rem;
            color: var(--auth-gold-light);
            font-weight: 300;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .auth-branding .brand-divider {
            width: 60px;
            height: 2px;
            background: var(--auth-gold);
            margin: 24px auto;
        }
        .auth-branding .brand-desc {
            font-size: 0.95rem;
            color: rgba(255,255,255,0.6);
            max-width: 320px;
            line-height: 1.6;
        }
        .auth-form-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: var(--auth-cream);
        }
        .auth-form-container {
            width: 100%;
            max-width: 440px;
        }
        .auth-form-header {
            margin-bottom: 36px;
        }
        .auth-form-header h2 {
            font-family: 'Poppins';
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--auth-navy);
            margin-bottom: 8px;
        }
        .auth-form-header p {
            color: var(--auth-muted);
            font-size: 0.95rem;
        }
        .auth-form .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--auth-text);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .auth-form .form-control {
            border: 1.5px solid var(--auth-border);
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 0.95rem;
            transition: border-color 0.2s, box-shadow 0.2s;
            background: #fff;
        }
        .auth-form .form-control:focus {
            border-color: var(--auth-navy);
            box-shadow: 0 0 0 3px rgba(26,39,68,0.1);
        }
        .auth-form .input-group .input-group-text {
            background: var(--auth-cream);
            border: 1.5px solid var(--auth-border);
            border-right: none;
            border-radius: 8px 0 0 8px;
            color: var(--auth-muted);
        }
        .auth-form .input-group .form-control {
            border-left: none;
            border-radius: 0 8px 8px 0;
        }
        .auth-form .input-group:focus-within .input-group-text,
        .auth-form .input-group:focus-within .form-control {
            border-color: var(--auth-navy);
        }
        .auth-form .input-group:focus-within .input-group-text {
            box-shadow: 0 0 0 3px rgba(26,39,68,0.1);
        }
        .auth-form .btn-auth {
            background: var(--auth-navy);
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.2s;
        }
        .auth-form .btn-auth:hover {
            background: var(--auth-navy-light);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(26,39,68,0.3);
        }
        .auth-form .btn-auth:active {
            transform: translateY(0);
        }
        .auth-footer {
            margin-top: 32px;
            text-align: center;
            padding-top: 24px;
            border-top: 1px solid var(--auth-border);
        }
        .auth-footer a {
            color: var(--auth-muted);
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.2s;
        }
        .auth-footer a:hover {
            color: var(--auth-navy);
        }
        @media (max-width: 991.98px) {
            .auth-branding { display: none; }
            .auth-form-side { padding: 24px; }
        }
    </style>
</head>
<body>
<?= $content ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
