<?php
$base = '../'; $pageTitle = 'Orders';
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/auth.php';

$rows = $pdo->query(
    'SELECT o.*, c.full_name, p.payment_status
     FROM orders o
     JOIN customers c ON c.customer_id = o.customer_id
     LEFT JOIN payments p ON p.order_id = o.order_id
     ORDER BY o.order_date DESC'
)->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<div class="card">
  <div class="card-header"><h3 class="card-title">Order Records</h3></div>
  <div class="card-body table-responsive">
    <table class="table table-bordered table-hover">
      <thead><tr><th>Order ID</th><th>Customer</th><th>Date</th><th>Total</th><th>Status</th><th>Payment</th><th>Action</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td>#<?= (int)$r['order_id'] ?></td>
            <td><?= e($r['full_name']) ?></td>
            <td><?= e($r['order_date']) ?></td>
            <td><?= money($r['total_amount']) ?></td>
            <td><span class="badge badge-info"><?= e($r['status']) ?></span></td>
            <td><?= e($r['payment_status'] ?? 'No record') ?></td>
            <td><a class="btn btn-sm btn-primary" href="view.php?id=<?= (int)$r['order_id'] ?>">View / Update</a></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="7">No orders found.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
