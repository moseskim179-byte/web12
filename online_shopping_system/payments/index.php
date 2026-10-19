<?php
$base = '../'; $pageTitle = 'Payments';
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/auth.php';

$rows = $pdo->query(
    'SELECT p.*, c.full_name
     FROM payments p
     JOIN orders o ON o.order_id = p.order_id
     JOIN customers c ON c.customer_id = o.customer_id
     ORDER BY p.payment_id DESC'
)->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Payment Records</h3>
    <p class="text-muted mb-0">This screen records statuses only; it does not process real payments.</p>
  </div>
  <div class="card-body table-responsive">
    <table class="table table-bordered">
      <thead><tr><th>Payment ID</th><th>Order</th><th>Customer</th><th>Method</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= (int)$r['payment_id'] ?></td>
            <td>#<?= (int)$r['order_id'] ?></td>
            <td><?= e($r['full_name']) ?></td>
            <td><?= e($r['payment_method']) ?></td>
            <td><?= money($r['amount']) ?></td>
            <td><span class="badge badge-info"><?= e($r['payment_status']) ?></span></td>
            <td><?= e($r['payment_date']) ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="7">No payment records found.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
