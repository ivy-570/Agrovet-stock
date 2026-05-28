<?php
require_once "config.php";
require_once "auth_check.php";

/* Fetch stock history */
$history = $conn->query("
    SELECT h.*, p.name AS product_name, u.name AS staff
    FROM stock_history h
    LEFT JOIN products p ON h.product_id = p.product_id
    LEFT JOIN users u ON h.changed_by = u.user_id
    ORDER BY h.history_id DESC
");

include "header.php";
?>

<div class="topbar">
    <div class="topbar-title">Stock History</div>
</div>

<div class="card">
    <div class="card-title">All Stock Movements</div>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Product</th>
            <th>Type</th>
            <th>Qty Changed</th>
            <th>Previous Qty</th>
            <th>New Qty</th>
            <th>Changed By</th>
            <th>Date</th>
        </tr>
        </thead>

        <tbody>
        <?php while ($h = $history->fetch_assoc()): ?>
            <tr>
                <td><?= $h['history_id'] ?></td>
                <td><?= htmlspecialchars($h['product_name']) ?></td>

                <td>
                    <?php if ($h['change_type'] === 'added'): ?>
                        <span style="color:green; font-weight:600;">Added</span>
                    <?php elseif ($h['change_type'] === 'sold'): ?>
                        <span style="color:red; font-weight:600;">Sold</span>
                    <?php else: ?>
                        <span style="color:#555; font-weight:600;">Adjusted</span>
                    <?php endif; ?>
                </td>

                <td><?= $h['quantity_changed'] ?></td>
                <td><?= $h['previous_quantity'] ?></td>
                <td><?= $h['new_quantity'] ?></td>
                <td><?= htmlspecialchars($h['staff']) ?></td>
                <td><?= $h['change_date'] ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include "footer.php"; ?>
