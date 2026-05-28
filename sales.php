<?php
require_once "config.php";
require_once "auth_check.php";

$msg = "";
$error = "";

/* Handle new sale */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $product_id  = (int)$_POST['product_id'];
    $customer_id = (int)$_POST['customer_id'];
    $quantity    = (int)$_POST['quantity'];
    $price       = (float)$_POST['price'];
    $total       = $quantity * $price;
    $user_id     = $_SESSION['user_id'];

    // Get current stock
    $res = $conn->query("SELECT quantity FROM products WHERE product_id = $product_id");
    $row = $res->fetch_assoc();
    $current_stock = $row['quantity'];

    if ($quantity > $current_stock) {
        $error = "Not enough stock available.";
    } else {
        // 1. Insert into sales
        $stmt = $conn->prepare("INSERT INTO sales (product_id, customer_id, quantity, price, total_amount, sold_by) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("iiiddi", $product_id, $customer_id, $quantity, $price, $total, $user_id);
        $stmt->execute();
        $stmt->close();

        // 2. Reduce stock
        $new_stock = $current_stock - $quantity;
        $conn->query("UPDATE products SET quantity = $new_stock WHERE product_id = $product_id");

        // 3. Insert into stock history
        $stmt2 = $conn->prepare("INSERT INTO stock_history (product_id, change_type, quantity_changed, previous_quantity, new_quantity, changed_by) VALUES (?,?,?,?,?,?)");
        $type = "sold";
        $stmt2->bind_param("isiiii", $product_id, $type, $quantity, $current_stock, $new_stock, $user_id);
        $stmt2->execute();
        $stmt2->close();

        $msg = "Sale recorded successfully.";
    }
}

/* Fetch dropdown data */
$products  = $conn->query("SELECT * FROM products ORDER BY name ASC");
$customers = $conn->query("SELECT * FROM customers ORDER BY name ASC");

/* Fetch sales list */
$sales = $conn->query("
    SELECT s.*, p.name AS product_name, c.name AS customer_name, u.name AS staff
    FROM sales s
    LEFT JOIN products p ON s.product_id = p.product_id
    LEFT JOIN customers c ON s.customer_id = c.customer_id
    LEFT JOIN users u ON s.sold_by = u.user_id
    ORDER BY s.sale_id DESC
");

include "header.php";
?>

<div class="topbar">
    <div class="topbar-title">Sales</div>
</div>

<div class="card">
    <div class="card-title">Record New Sale</div>

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
                            <?= htmlspecialchars($p['name']) ?> (Stock: <?= $p['quantity'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Customer</label>
                <select name="customer_id" required>
                    <option value="">Select customer</option>
                    <?php while ($c = $customers->fetch_assoc()): ?>
                        <option value="<?= $c['customer_id'] ?>">
                            <?= htmlspecialchars($c['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Quantity</label>
                <input type="number" name="quantity" required min="1">
            </div>

            <div class="form-group">
                <label>Price (Selling Price)</label>
                <input type="number" step="0.01" name="price" required>
            </div>

        </div>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Record Sale</button>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-title">Sales History</div>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Product</th>
            <th>Customer</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Total</th>
            <th>Sold By</th>
            <th>Date</th>
        </tr>
        </thead>

        <tbody>
        <?php while ($s = $sales->fetch_assoc()): ?>
            <tr>
                <td><?= $s['sale_id'] ?></td>
                <td><?= htmlspecialchars($s['product_name']) ?></td>
                <td><?= htmlspecialchars($s['customer_name']) ?></td>
                <td><?= $s['quantity'] ?></td>
                <td><?= number_format($s['price'],2) ?></td>
                <td><?= number_format($s['total_amount'],2) ?></td>
                <td><?= htmlspecialchars($s['staff']) ?></td>
                <td><?= $s['sale_date'] ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include "footer.php"; ?>
