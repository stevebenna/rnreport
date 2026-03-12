<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'RN Report') ?></title>
    <style>
        :root {
            --brand-dark: #E7411B;
            --brand-mid: #EC6918;
            --brand-light: #F2912E;
            --bg: #f4f6f9;
            --card: #ffffff;
            --text: #1f2937;
            --muted: #4a5568;
            --border: rgba(0,0,0,0.08);
            --radius: 12px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Ubuntu, 'Helvetica Neue', Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.5rem;
            background: linear-gradient(90deg, var(--brand-dark), var(--brand-mid));
            color: white;
            box-shadow: 0 6px 14px rgba(0,0,0,0.12);
        }

        .navbar .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 700;
            font-size: 1.15rem;
        }

        .navbar .brand img {
            width: 40px;
            height: 40px;
            object-fit: contain;
            border-radius: 8px;
            background: white;
            padding: 4px;
        }

        .nav-links {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .nav-links a {
            padding: 0.5rem 0.8rem;
            border-radius: 999px;
            font-size: 0.9rem;
            font-weight: 600;
            transition: background 120ms ease;
        }

        .nav-links a:hover {
            background: rgba(255,255,255,0.15);
        }

        .nav-links a.active {
            background: rgba(255,255,255,0.3);
        }

        .nav-actions {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .nav-actions .user {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
            padding: 0.55rem 1rem;
            border: 1px solid transparent;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 150ms ease, box-shadow 150ms ease, background 150ms ease;
            text-decoration: none;
            line-height: 1;
        }

        .btn.primary {
            background: var(--brand-dark);
            color: white;
            box-shadow: 0 8px 18px rgba(231,65,27,0.22);
        }

        .btn.primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(231,65,27,0.28);
        }

        .btn.secondary {
            background: rgba(255,255,255,0.9);
            color: var(--text);
            border-color: rgba(0,0,0,0.12);
        }

        .btn.danger {
            background: #d32f2f;
            color: white;
        }

        .btn.danger:hover {
            background: #b22b2b;
        }

        .container {
            max-width: 1150px;
            margin: 2.25rem auto;
            padding: 0 1.25rem;
        }

        .card {
            background: var(--card);
            border-radius: var(--radius);
            padding: 1.75rem;
            box-shadow: 0 10px 24px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.04);
        }

        h1, h2, h3 {
            margin-bottom: 0.6rem;
            font-weight: 700;
        }

        p {
            line-height: 1.5;
            color: var(--muted);
        }

        .grid {
            display: grid;
            gap: 1.25rem;
        }

        .grid-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--card);
            border-radius: var(--radius);
            overflow: hidden;
        }

        th, td {
            padding: 0.85rem 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(0,0,0,0.06);
        }

        th {
            background: rgba(236, 105, 24, 0.12);
            color: var(--text);
            font-weight: 700;
            font-size: 0.85rem;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
            background: rgba(0,0,0,0.06);
            color: var(--text);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        label {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
            display: block;
        }

        input[type="text"],
        input[type="email"],
        input[type="number"],
        input[type="password"],
        select,
        textarea {
            width: 100%;
            padding: 0.75rem 0.85rem;
            border-radius: 10px;
            border: 1px solid rgba(0,0,0,0.14);
            background: rgba(255,255,255,0.9);
            color: var(--text);
            transition: border-color 120ms ease, box-shadow 120ms ease;
            font-size: 0.95rem;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--brand-mid);
            box-shadow: 0 0 0 3px rgba(236,105,24,0.18);
        }

        .field-note {
            font-size: 0.85rem;
            color: var(--muted);
            margin-top: 0.25rem;
        }

        .form-actions {
            display: flex;
            gap: 0.75rem;
            align-items: center;
            flex-wrap: wrap;
            margin-top: 1.25rem;
        }

        .alert {
            padding: 0.85rem 1rem;
            border-radius: 10px;
            background: rgba(236, 105, 24, 0.13);
            border: 1px solid rgba(236, 105, 24, 0.25);
            color: #472f12;
            margin-bottom: 1rem;
        }

        .alert.error {
            background: rgba(219, 68, 55, 0.1);
            border-color: rgba(219, 68, 55, 0.25);
            color: #7b1e1e;
        }

        .footer {
            padding: 1rem 1.5rem;
            text-align: center;
            font-size: 0.9rem;
            color: rgba(255,255,255,0.75);
            background: linear-gradient(90deg, var(--brand-mid), var(--brand-dark));
        }

        @media (max-width: 760px) {
            .nav-links {
                display: none;
            }

            .navbar {
                flex-wrap: wrap;
                gap: 0.75rem;
            }

            .container {
                margin: 1.5rem auto;
            }

            .grid-2 {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<header class="navbar">
    <a class="brand" href="/dashboard">
        <img style="background-color: #1f2937;" src="/logo.png" alt="Logo" />
        <span>RN Report</span>
    </a>

    <?php if (session()->get('logged_in')) : ?>
        <div class="nav-links">
            <a href="/dashboard" class="<?= uri_string() === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
            <a href="/report-scf" class="<?= uri_string() === 'report-scf' ? 'active' : '' ?>">Report SCF/ItsRight</a>
            <a href="/canzoni" class="<?= str_starts_with(uri_string(), 'canzoni') ? 'active' : '' ?>">Canzoni</a>
        </div>

        <div class="nav-actions">
            <span class="user">👤 <?= esc(session()->get('user')['email'] ?? '') ?></span>
            <a href="/logout" class="btn secondary">Logout</a>
        </div>
    <?php endif; ?>
</header>

<main class="main">
    <div class="container">
        <?= $this->renderSection('content') ?>
    </div>
</main>

<footer class="footer">
    Built with CodeIgniter 4 • © <?= date('Y') ?>
</footer>

</body>
</html>
