<?php
require_once "config.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT user_id, name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($uid, $name, $hash, $role);

    if ($stmt->fetch() && password_verify($password, $hash)) {
        $_SESSION['user_id'] = $uid;
        $_SESSION['name']    = $name;
        $_SESSION['role']    = $role;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Agrovet</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { background:#0f172a; display:flex; align-items:center; justify-content:center; min-height:100vh; font-family:"Segoe UI",sans-serif; }
        .login-card {
            background:#ffffff;
            padding:24px 26px;
            border-radius:10px;
            width:100%;
            max-width:360px;
            box-shadow:0 10px 30px rgba(15,23,42,0.4);
        }
        h2 { margin-bottom:16px; }
        label { display:block; font-size:13px; margin-bottom:4px; color:#6b7280; }
        input { width:100%; padding:8px 10px; border-radius:6px; border:1px solid #d1d5db; margin-bottom:10px; }
        .btn { width:100%; padding:9px 10px; border-radius:6px; border:none; background:#1b8f5a; color:#fff; cursor:pointer; font-size:14px; }
        .btn:hover { background:#146843; }
        .error { background:#fee2e2; color:#b91c1c; padding:8px 10px; border-radius:6px; font-size:13px; margin-bottom:10px; }
        .note { font-size:12px; color:#6b7280; margin-top:8px; }
        a { color:#1b8f5a; text-decoration:none; }
    </style>
</head>
<body>
<div class="login-card">
    <h2>Agrovet Login</h2>
    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
        <label>Email</label>
        <input type="email" name="email" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <button class="btn" type="submit">Login</button>
    </form>
    <p class="note">No account? <a href="register.php">Register admin</a></p>
</div>
</body>
</html>
