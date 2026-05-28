<?php
require_once "config.php";
require_once "auth_check.php";

$msg = "";
$error = "";

/* Handle add/update */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id      = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0;
    $name    = trim($_POST['name']);
    $phone   = trim($_POST['phone']);
    $email   = trim($_POST['email']);
    $address = trim($_POST['address']);

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE customers SET name=?, phone=?, email=?, address=? WHERE customer_id=?");
        $stmt->bind_param("ssssi", $name, $phone, $email, $address, $id);
        $ok = $stmt->execute();
        $stmt->close();
        $msg = $ok ? "Customer updated." : "Error updating customer.";
    } else {
        $stmt = $conn->prepare("INSERT INTO customers (name, phone, email, address) VALUES (?,?,?,?)");
        $stmt->bind_param("ssss", $name, $phone, $email, $address);
        $ok = $stmt->execute();
        $stmt->close();
        $msg = $ok ? "Customer added." : "Error adding customer.";
    }
}

/* Handle delete */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM customers WHERE customer_id = $id");
    $msg = "Customer deleted.";
}

/* Edit data */
$edit_customer = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = $conn->query("SELECT * FROM customers WHERE customer_id = $id");
    $edit_customer = $res->fetch_assoc();
}

$customers = $conn->query("SELECT * FROM customers ORDER BY customer_id DESC");

include "header.php";
?>
<div class="topbar">
    <div class="topbar-title">Customers</div>
</div>

<div class="card">
    <div class="card-title"><?= $edit_customer ? "Edit Customer" : "Add Customer" ?></div>

    <?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <form method="post">
        <input type="hidden" name="customer_id" value="<?= $edit_customer['customer_id'] ?? '' ?>">

        <div class="form-grid">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" required value="<?= htmlspecialchars($edit_customer['name'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($edit_customer['phone'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($edit_customer['email'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" value="<?= htmlspecialchars($edit_customer['address'] ?? '') ?>">
            </div>
        </div>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit"><?= $edit_customer ? "Update" : "Save" ?></button>
            <?php if ($edit_customer): ?>
                <a href="customers.php" class="btn btn-secondary">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-title">Customer List</div>

    <table>
        <thead>
        <tr>
            <th>ID</th><th>Name</th><th>Phone</th><th>Email</th><th>Address</th><th>Actions</th>
        </tr>
        </thead>

        <tbody>
        <?php while ($row = $customers->fetch_assoc()): ?>
            <tr>
                <td><?= $row['customer_id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['address']) ?></td>

                <td>
                    <a class="btn btn-sm btn-secondary" href="customers.php?edit=<?= $row['customer_id'] ?>">Edit</a>
                    <a class="btn btn-sm btn-danger" href="customers.php?delete=<?= $row['customer_id'] ?>" onclick="return confirm('Delete this customer?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include "footer.php"; ?>
