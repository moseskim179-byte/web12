<?php
$base = ''; $pageTitle = 'Dashboard';
require __DIR__ . '/config/db.php';
require __DIR__ . '/includes/auth.php';

$products  = (int)$pdo->query("SELECT COUNT(*) FROM products WHERE is_active=1")->fetchColumn();
$customers = (int)$pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();
$orders    = (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$pending   = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status='Pending'")->fetchColumn();
$lowStock  = (int)$pdo->query("SELECT COUNT(*) FROM products WHERE is_active=1 AND stock_quantity <= 5")->fetchColumn();

$recent = $pdo->query(
    "SELECT o.order_id, o.order_date, o.status, o.total_amount, c.full_name
     FROM orders o JOIN customers c ON c.customer_id = o.customer_id
     ORDER BY o.order_date DESC LIMIT 5"
)->fetchAll();

// [label, number, icon, colour, link]
$boxes = [
    ['Products',           $products,  'fa-box',                  'bg-info',    'products/index.php'],
    ['Customers',          $customers, 'fa-users',                'bg-primary', 'customers/index.php'],
    ['Pending Orders',     $pending,   'fa-clock',                'bg-warning', 'orders/index.php'],
    ['Low-stock Products', $lowStock,  'fa-triangle-exclamation', 'bg-danger',  'products/index.php'],
];

require __DIR__ . '/includes/header.php';
?>
<div class="row">
  <?php foreach ($boxes as [$label, $value, $icon, $color, $link]): ?>
    <div class="col-lg-3 col-6">
      <div class="small-box <?= $color ?>">
        <div class="inner"><h3><?= $value ?></h3><p><?= e($label) ?></p></div>
        <div class="icon"><i class="fas <?= $icon ?>"></i></div>
        <a href="<?= $link ?>" class="small-box-footer">Manage <i class="fas fa-arrow-circle-right"></i></a>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<div class="card">
  <div class="card-header"><h3 class="card-title">Recent Orders (<?= $orders ?> total)</h3></div>
  <div class="card-body table-responsive p-0">
    <table class="table table-hover text-nowrap">
      <thead><tr><th>Order ID</th><th>Customer</th><th>Date</th><th>Total</th><th>Status</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($recent as $o): ?>
          <tr>
            <td>#<?= (int)$o['order_id'] ?></td>
            <td><?= e($o['full_name']) ?></td>
            <td><?= e($o['order_date']) ?></td>
            <td><?= money($o['total_amount']) ?></td>
            <td><span class="badge badge-info"><?= e($o['status']) ?></span></td>
            <td><a class="btn btn-sm btn-primary" href="orders/view.php?id=<?= (int)$o['order_id'] ?>">View</a></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$recent): ?><tr><td colspan="6">No orders found.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
