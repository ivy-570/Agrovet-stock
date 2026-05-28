<?php require_once "config.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Agrovet System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root {
            --primary: #1b8f5a;
            --primary-dark: #146843;
            --bg: #f4f6f9;
            --card: #ffffff;
            --text: #222;
            --muted: #777;
            --danger: #e74c3c;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: "Segoe UI", sans-serif; }
        body { background: var(--bg); color: var(--text); }
        .layout { display: flex; min-height: 100vh; }
        .sidebar {
            width: 230px;
            background: #0f172a;
            color: #e5e7eb;
            padding: 20px 15px;
        }
        .sidebar h2 { font-size: 20px; margin-bottom: 20px; color: #a5f3fc; }
        .nav-link {
            display: block;
            padding: 10px 12px;
            margin-bottom: 6px;
            border-radius: 6px;
            color: #e5e7eb;
            text-decoration: none;
            font-size: 14px;
        }
        .nav-link:hover { background: #1e293b; }
        .nav-link.active { background: var(--primary); }
        .main {
            flex: 1;
            padding: 20px 24px;
        }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
        }
        .topbar-title { font-size: 22px; font-weight: 600; }
        .user-badge { font-size: 14px; color: var(--muted); }
        .card {
            background: var(--card);
            border-radius: 10px;
            padding: 18px 20px;
            box-shadow: 0 4px 10px rgba(15, 23, 42, 0.06);
            margin-bottom: 18px;
        }
        .card-title { font-size: 18px; margin-bottom: 12px; }
        .btn {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
        }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-danger { background: var(--danger); color: #fff; }
        .btn-secondary { background: #e5e7eb; color: #111827; }
        .btn-sm { padding: 5px 10px; font-size: 13px; }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        th, td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }
        th { background: #f9fafb; font-weight: 600; }
        tr:hover { background: #f9fafb; }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
        }
        .form-group label { display: block; font-size: 13px; margin-bottom: 4px; color: var(--muted); }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 7px 9px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            font-size: 14px;
        }
        .form-actions { margin-top: 12px; }
        .alert { padding: 8px 10px; border-radius: 6px; font-size: 13px; margin-bottom: 10px; }
        .alert-success { background: #dcfce7; color: #166534; }
        .alert-error { background: #fee2e2; color: #b91c1c; }
        @media (max-width: 768px) {
            .layout { flex-direction: column; }
            .sidebar { width: 100%; display: flex; overflow-x: auto; }
            .nav-link { display: inline-block; margin-right: 6px; }
        }
    </style>
</head>
<body>
<div class="layout">
    <div class="sidebar">
        <h2>Agrovet</h2>
        <a href="dashboard.php" class="nav-link">Dashboard</a>
        <a href="products.php" class="nav-link">Products</a>
        <a href="suppliers.php" class="nav-link">Suppliers</a>
        <a href="customers.php" class="nav-link">Customers</a>
        <a href="sales.php" class="nav-link">Sales</a>
        <a href="purchases.php" class="nav-link">Purchases</a>
        <a href="stock_history.php" class="nav-link">Stock History</a>
        <a href="users.php" class="nav-link">Users</a>
        <a href="logout.php" class="nav-link">Logout</a>
    </div>
    <div class="main">
