<?php
require_once "config.php";
require_once "auth_check.php";

$products_count   = $conn->query("SELECT COUNT(*) AS c FROM products")->fetch_assoc()['c'];
$suppliers_count  = $conn->query("SELECT COUNT(*) AS c FROM suppliers")->fetch_assoc()['c'];
$customers_count  = $conn->query("SELECT COUNT(*) AS c FROM customers")->fetch_assoc()['c'];
$sales_today_row  = $conn->query("SELECT IFNULL(SUM(total_amount),0) AS total FROM sales WHERE DATE(sale_date)=CURDATE()")->fetch_assoc();
$sales_today      = $sales_today_row['total'];
include "header.php";
?>
<div class="topbar">
    <div class="topbar-title">Dashboard</div>
    <div class="user-badge">Logged in as <?= htmlspecialchars($_SESSION['name'] ?? '') ?> (<?= htmlspecialchars($_SESSION['role'] ?? '') ?>)</div>
</div>

<div class="card">
    <div class="card-title">Overview</div>
    <div class="form-grid">
        <div>
            <strong>Total Products</strong><br><?= $products_count ?>
        </div>
        <div>
            <strong>Total Suppliers</strong><br><?= $suppliers_count ?>
        </div>
        <div>
            <strong>Total Customers</strong><br><?= $customers_count ?>
        </div>
        <div>
            <strong>Sales Today</strong><br>KES <?= number_format($sales_today, 2) ?>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
