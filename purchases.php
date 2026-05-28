<?php
require_once "config.php";
require_once "auth_check.php";

$msg = "";
$error = "";

/* Handle new purchase */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $product_id  = (int)$_POST['product_id'];
    $supplier_id = (int)$_POST['supplier_id'];
    $quantity    = (int)$_POST['quantity'];
    $cost        = (float)$_POST['cost'];
    $user_id     = $_SESSION['user_id'];

    // Get current stock
    $res = $conn->query("SELECT quantity FROM products WHERE product_id = $product_id");
    $row = $res->fetch_assoc();
    $current_stock = $row['quantity'];

    // 1. Insert into purchases
    $stmt = $conn->prepare("INSERT INTO purchases (product_id, supplier_id, quantity, cost) VALUES (?,?,?,?)");
    $stmt->bind_param("iiid", $product_id, $supplier_id, $quantity, $cost);
    $stmt->execute();
    $stmt->close();

    // 2. Increase stock
    $new_stock = $current_stock + $quantity;
    $conn->query("UPDATE products SET quantity = $new_stock WHERE product_id = $product_id");

    // 3. Insert into stock history
    $stmt2 = $conn->prepare("INSERT INTO stock_history (product_id, change_type, quantity_changed, previous_quantity, new_quantity, changed_by) VALUES (?,?,?,?,?,?)");
    $type = "added";
    $stmt2->bind_param("isiiii", $product_id, $type, $quantity, $current_stock, $new_stock, $user_id);
    $stmt2->execute();
    $stmt2->close();

    $msg = "Purchase recorded and stock updated.";
}

/* Fetch dropdown data */
$products  = $conn->query("SELECT * FROM products ORDER BY name ASC");
$suppliers = $conn->query("SELECT * FROM suppliers ORDER BY name ASC");

/* Fetch purchase list */
$purchases = $conn->query("
    SELECT p.*, pr.name AS product_name, s.name AS supplier_name
    FROM purchases p
    LEFT JOIN products pr ON p.product_id = pr.product_id
    LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
    ORDER BY p.purchase_id DESC
");

include "header.php";
?>

<div class="topbar">
    <div class="topbar-title">Purchases</div>
</div>

<div class="card">
    <div class="card-title">Record New Purchase</div>

    <?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>

    <form method="post">
        <div class="form-grid">

            <div class="form-group">
                <label>Product</label>
                <select name="product_id" required>
                    <option value="">Select product</option>
                    <?php while ($p = $products->fetch_assoc()): ?>
                        <option value="<?= $p['product_id'] ?>">
                            <?= htmlspecialchars($p['name']) ?> (Current: <?= $p['quantity'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Supplier</label>
                <select name="supplier_id" required>
                    <option value="">Select supplier</option>
                    <?php while ($s = $suppliers->fetch_assoc()): ?>
                        <option value="<?= $s['supplier_id'] ?>">
                            <?= htmlspecialchars($s['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Quantity Purchased</label>
                <input type="number" name="quantity" required min="1">
            </div>

            <div class="form-group">
                <label>Total Cost</label>
                <input type="number" step="0.01" name="cost" required>
            </div>

        </div>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Record Purchase</button>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-title">Purchase History</div>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Product</th>
            <th>Supplier</th>
            <th>Qty</th>
            <th>Cost</th>
            <th>Date</th>
        </tr>
        </thead>

        <tbody>
        <?php while ($p = $purchases->fetch_assoc()): ?>
            <tr>
                <td><?= $p['purchase_id'] ?></td>
                <td><?= htmlspecialchars($p['product_name']) ?></td>
                <td><?= htmlspecialchars($p['supplier_name']) ?></td>
                <td><?= $p['quantity'] ?></td>
                <td><?= number_format($p['cost'],2) ?></td>
                <td><?= $p['purchase_date'] ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include "footer.php"; ?>
