<?php
require_once "config.php";
require_once "auth_check.php";

/* Only admin can manage users */
if ($_SESSION['role'] !== 'admin') {
    die("<h2 style='padding:20px;'>Access denied. Admins only.</h2>");
}

$msg = "";
$error = "";

/* Handle add/update */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id    = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role  = trim($_POST['role']);

    if ($id > 0) {
        // Update user (password optional)
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET name=?, email=?, role=?, password=? WHERE user_id=?");
            $stmt->bind_param("ssssi", $name, $email, $role, $password, $id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET name=?, email=?, role=? WHERE user_id=?");
            $stmt->bind_param("sssi", $name, $email, $role, $id);
        }

        $ok = $stmt->execute();
        $stmt->close();
        $msg = $ok ? "User updated." : "Error updating user.";

    } else {
        // Add new user
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?,?,?,?)");
        $stmt->bind_param("ssss", $name, $email, $password, $role);
        $ok = $stmt->execute();
        $stmt->close();
        $msg = $ok ? "User added." : "Error adding user.";
    }
}

/* Handle delete */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    if ($id == $_SESSION['user_id']) {
        $error = "You cannot delete your own account.";
    } else {
        $conn->query("DELETE FROM users WHERE user_id = $id");
        $msg = "User deleted.";
    }
}

/* Edit data */
$edit_user = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = $conn->query("SELECT * FROM users WHERE user_id = $id");
    $edit_user = $res->fetch_assoc();
}

$users = $conn->query("SELECT * FROM users ORDER BY user_id DESC");

include "header.php";
?>

<div class="topbar">
    <div class="topbar-title">Users</div>
</div>

<div class="card">
    <div class="card-title"><?= $edit_user ? "Edit User" : "Add User" ?></div>

    <?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>

    <form method="post">
        <input type="hidden" name="user_id" value="<?= $edit_user['user_id'] ?? '' ?>">

        <div class="form-grid">

            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" required value="<?= htmlspecialchars($edit_user['name'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required value="<?= htmlspecialchars($edit_user['email'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Role</label>
                <select name="role" required>
                    <option value="admin" <?= isset($edit_user['role']) && $edit_user['role']=='admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="staff" <?= isset($edit_user['role']) && $edit_user['role']=='staff' ? 'selected' : '' ?>>Staff</option>
                </select>
            </div>

            <div class="form-group">
                <label>Password <?= $edit_user ? "(leave blank to keep current)" : "" ?></label>
                <input type="password" name="password" <?= $edit_user ? "" : "required" ?>>
            </div>

        </div>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit"><?= $edit_user ? "Update" : "Save" ?></button>
            <?php if ($edit_user): ?>
                <a href="users.php" class="btn btn-secondary">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-title">User List</div>

    <table>
        <thead>
        <tr>
            <th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Created</th><th>Actions</th>
        </tr>
        </thead>

        <tbody>
        <?php while ($u = $users->fetch_assoc()): ?>
            <tr>
                <td><?= $u['user_id'] ?></td>
                <td><?= htmlspecialchars($u['name']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['role']) ?></td>
                <td><?= $u['created_at'] ?></td>

                <td>
                    <a class="btn btn-sm btn-secondary" href="users.php?edit=<?= $u['user_id'] ?>">Edit</a>

                    <?php if ($u['user_id'] != $_SESSION['user_id']): ?>
                        <a class="btn btn-sm btn-danger" href="users.php?delete=<?= $u['user_id'] ?>" onclick="return confirm('Delete this user?')">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include "footer.php"; ?>
