<?php
require_once "config.php";

$msg = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass1 = $_POST['password'];
    $pass2 = $_POST['password_confirm'];

    if ($pass1 !== $pass2) {
        $error = "Passwords do not match.";
    } else {
        $hash = password_hash($pass1, PASSWORD_DEFAULT);
        $role = 'admin';
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?,?,?,?)");
        $stmt->bind_param("ssss", $name, $email, $hash, $role);
        if ($stmt->execute()) {
            $msg = "Admin registered. You can now login.";
        } else {
            $error = "Error: " . $conn->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Agrovet</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { background:#0f172a; display:flex; align-items:center; justify-content:center; min-height:100vh; font-family:"Segoe UI",sans-serif; }
        .card { background:#ffffff; padding:24px 26px; border-radius:10px; width:100%; max-width:380px; box-shadow:0 10px 30px rgba(15,23,42,0.4); }
        h2 { margin-bottom:16px; }
        label { display:block; font-size:13px; margin-bottom:4px; color:#6b7280; }
        input { width:100%; padding:8px 10px; border-radius:6px; border:1px solid #d1d5db; margin-bottom:10px; }
        .btn { width:100%; padding:9px 10px; border-radius:6px; border:none; background:#1b8f5a; color:#fff; cursor:pointer; font-size:14px; }
        .btn:hover { background:#146843; }
        .alert { padding:8px 10px; border-radius:6px; font-size:13px; margin-bottom:10px; }
        .alert-success { background:#dcfce7; color:#166534; }
        .alert-error { background:#fee2e2; color:#b91c1c; }
        a { color:#1b8f5a; text-decoration:none; font-size:13px; }
    </style>
</head>
<body>
<div class="card">
    <h2>Register Admin</h2>
    <?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post">
        <label>Name</label>
        <input type="text" name="name" required>
        <label>Email</label>
        <input type="email" name="email" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <label>Confirm Password</label>
        <input type="password" name="password_confirm" required>
        <button class="btn" type="submit">Register</button>
    </form>
    <p><a href="login.php">Back to login</a></p>
</div>
</body>
</html>
