<?php
require_once "config.php";
require_once "auth_check.php";

$msg = "";
$error = "";

/* Handle add/update */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id           = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $name         = trim($_POST['name']);
    $buying_price = (float)$_POST['buying_price'];
    $selling_price= (float)$_POST['selling_price'];
    $quantity     = (int)$_POST['quantity'];

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE products SET name=?, buying_price=?, selling_price=?, quantity=? WHERE product_id=?");
        $stmt->bind_param("sddii", $name, $buying_price, $selling_price, $quantity, $id);
        $ok = $stmt->execute();
        $stmt->close();
        $msg = $ok ? "Product updated." : "Error updating product.";
    } else {
        $stmt = $conn->prepare("INSERT INTO products (name, buying_price, selling_price, quantity) VALUES (?,?,?,?)");
        $stmt->bind_param("sddi", $name, $buying_price, $selling_price, $quantity);
        $ok = $stmt->execute();
        $stmt->close();
        $msg = $ok ? "Product added." : "Error adding product.";
    }
}

/* Handle delete */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM products WHERE product_id = $id");
    $msg = "Product deleted.";
}

/* Edit data */
$edit_product = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = $conn->query("SELECT * FROM products WHERE product_id = $id");
    $edit_product = $res->fetch_assoc();
}

$products = $conn->query("SELECT * FROM products ORDER BY product_id DESC");

include "header.php";
?>
<div class="topbar">
    <div class="topbar-title">Products</div>
</div>

<div class="card">
    <div class="card-title"><?= $edit_product ? "Edit Product" : "Add Product" ?></div>
    <?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <form method="post">
        <input type="hidden" name="product_id" value="<?= $edit_product['product_id'] ?? '' ?>">
        <div class="form-grid">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" required value="<?= htmlspecialchars($edit_product['name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Buying Price</label>
                <input type="number" step="0.01" name="buying_price" required value="<?= htmlspecialchars($edit_product['buying_price'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Selling Price</label>
                <input type="number" step="0.01" name="selling_price" required value="<?= htmlspecialchars($edit_product['selling_price'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Quantity</label>
                <input type="number" name="quantity" required value="<?= htmlspecialchars($edit_product['quantity'] ?? 0) ?>">
            </div>
        </div>
        <div class="form-actions">
            <button class="btn btn-primary" type="submit"><?= $edit_product ? "Update" : "Save" ?></button>
            <?php if ($edit_product): ?>
                <a href="products.php" class="btn btn-secondary">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-title">Product List</div>
    <table>
        <thead>
        <tr>
            <th>ID</th><th>Name</th><th>Buying</th><th>Selling</th><th>Qty</th><th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $products->fetch_assoc()): ?>
            <tr>
                <td><?= $row['product_id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= number_format($row['buying_price'],2) ?></td>
                <td><?= number_format($row['selling_price'],2) ?></td>
                <td><?= $row['quantity'] ?></td>
                <td>
                    <a class="btn btn-sm btn-secondary" href="products.php?edit=<?= $row['product_id'] ?>">Edit</a>
                    <a class="btn btn-sm btn-danger" href="products.php?delete=<?= $row['product_id'] ?>" onclick="return confirm('Delete this product?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include "footer.php"; ?>
