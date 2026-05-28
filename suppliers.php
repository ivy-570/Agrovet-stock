<?php
require_once "config.php";
require_once "auth_check.php";

$msg = "";
$error = "";

/* Handle add/update */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id      = isset($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : 0;
    $name    = trim($_POST['name']);
    $phone   = trim($_POST['phone']);
    $email   = trim($_POST['email']);
    $address = trim($_POST['address']);

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE suppliers SET name=?, phone=?, email=?, address=? WHERE supplier_id=?");
        $stmt->bind_param("ssssi", $name, $phone, $email, $address, $id);
        $ok = $stmt->execute();
        $stmt->close();
        $msg = $ok ? "Supplier updated." : "Error updating supplier.";
    } else {
        $stmt = $conn->prepare("INSERT INTO suppliers (name, phone, email, address) VALUES (?,?,?,?)");
        $stmt->bind_param("ssss", $name, $phone, $email, $address);
        $ok = $stmt->execute();
        $stmt->close();
        $msg = $ok ? "Supplier added." : "Error adding supplier.";
    }
}

/* Handle delete */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM suppliers WHERE supplier_id = $id");
    $msg = "Supplier deleted.";
}

/* Edit data */
$edit_supplier = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = $conn->query("SELECT * FROM suppliers WHERE supplier_id = $id");
    $edit_supplier = $res->fetch_assoc();
}

$suppliers = $conn->query("SELECT * FROM suppliers ORDER BY supplier_id DESC");

include "header.php";
?>
<div class="topbar">
    <div class="topbar-title">Suppliers</div>
</div>

<div class="card">
    <div class="card-title"><?= $edit_supplier ? "Edit Supplier" : "Add Supplier" ?></div>

    <?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <form method="post">
        <input type="hidden" name="supplier_id" value="<?= $edit_supplier['supplier_id'] ?? '' ?>">

        <div class="form-grid">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" required value="<?= htmlspecialchars($edit_supplier['name'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($edit_supplier['phone'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($edit_supplier['email'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" value="<?= htmlspecialchars($edit_supplier['address'] ?? '') ?>">
            </div>
        </div>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit"><?= $edit_supplier ? "Update" : "Save" ?></button>
            <?php if ($edit_supplier): ?>
                <a href="suppliers.php" class="btn btn-secondary">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-title">Supplier List</div>

    <table>
        <thead>
        <tr>
            <th>ID</th><th>Name</th><th>Phone</th><th>Email</th><th>Address</th><th>Actions</th>
        </tr>
        </thead>

        <tbody>
        <?php while ($row = $suppliers->fetch_assoc()): ?>
            <tr>
                <td><?= $row['supplier_id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['address']) ?></td>

                <td>
                    <a class="btn btn-sm btn-secondary" href="suppliers.php?edit=<?= $row['supplier_id'] ?>">Edit</a>
                    <a class="btn btn-sm btn-danger" href="suppliers.php?delete=<?= $row['supplier_id'] ?>" onclick="return confirm('Delete this supplier?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include "footer.php"; ?>
