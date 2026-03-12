<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f6f9; }

        .navbar {
            background: #2d3748;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar a { color: white; text-decoration: none; }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .welcome-card {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .welcome-card h1 { color: #2d3748; margin-bottom: 0.5rem; }
        .welcome-card p  { color: #718096; }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-card h3 { color: #718096; font-size: 0.9rem; margin-bottom: 0.5rem; }
        .stat-card p  { color: #2d3748; font-size: 2rem; font-weight: bold; }

        .logout-btn {
            background: #e53e3e;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .logout-btn:hover { background: #c53030; }
    </style>
</head>
<body>

<nav class="navbar">
    <span>MyApp</span>
    <div style="display:flex; align-items:center; gap:1rem;">
        <span>👤 <?= esc($user['email']) ?></span>
        <a href="/logout" class="logout-btn">Logout</a>
    </div>
</nav>

<div class="container">
    <div class="welcome-card">
        <h1>Welcome back 👋</h1>
        <p>Logged in as <strong><?= esc($user['email']) ?></strong></p>
        <p style="margin-top:0.5rem; font-size:0.85rem; color:#a0aec0;">
            Last sign in: <?= esc($user['last_sign_in_at'] ?? 'N/A') ?>
        </p>
    </div>

    <div class="stats">
        <div class="stat-card">
            <h3>User ID</h3>
            <p style="font-size:0.8rem; word-break:break-all;"><?= esc($user['id']) ?></p>
        </div>
        <div class="stat-card">
            <h3>Provider</h3>
            <p><?= esc($user['app_metadata']['provider'] ?? 'email') ?></p>
        </div>
        <div class="stat-card">
            <h3>Account Created</h3>
            <p style="font-size:0.85rem;"><?= esc(date('d M Y', strtotime($user['created_at']))) ?></p>
        </div>
    </div>
</div>

</body>
</html>