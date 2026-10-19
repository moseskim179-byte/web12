<?php
$base = '../'; $pageTitle = 'Order Details';
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/auth.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT o.*, c.full_name, c.email FROM orders o JOIN customers c ON c.customer_id = o.customer_id WHERE o.order_id = ?');
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) {
    http_response_code(404);
    exit('Order not found. Return to <a href="index.php">orders</a>.');
}

$error   = '';
$allowed = ['Pending', 'Confirmed', 'Shipped', 'Delivered', 'Cancelled'];

// Update status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'] ?? '';
    if (!in_array($status, $allowed, true)) {
        $error = 'Invalid status selected.';
    } else {
        $stmt = $pdo->prepare('UPDATE orders SET status = ? WHERE order_id = ?');
        $stmt->execute([$status, $id]);
        redirect('view.php?id=' . $id . '&updated=1');
    }
}

$stmt = $pdo->prepare('SELECT oi.*, p.product_name FROM order_items oi JOIN products p ON p.product_id = oi.product_id WHERE oi.order_id = ?');
$stmt->execute([$id]);
$items = $stmt->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<?php if (isset($_GET['updated'])): ?><div class="alert alert-success">Order status updated.</div><?php endif; ?>

<div class="row">
  <div class="col-md-4">
    <div class="card">
      <div class="card-header"><h3 class="card-title">Order #<?= (int)$order['order_id'] ?></h3></div>
      <div class="card-body">
        <p><strong>Customer:</strong> <?= e($order['full_name']) ?></p>
        <p><strong>Email:</strong> <?= e($order['email']) ?></p>
        <p><strong>Date:</strong> <?= e($order['order_date']) ?></p>
        <p><strong>Shipping:</strong> <?= e($order['shipping_address']) ?></p>
        <p><strong>Total:</strong> <?= money($order['total_amount']) ?></p>
        <form method="post">
          <label>Update Status</label>
          <select class="form-control mb-2" name="status">
            <?php foreach ($allowed as $st): ?>
              <option value="<?= e($st) ?>" <?= $order['status'] === $st ? 'selected' : '' ?>><?= e($st) ?></option>
            <?php endforeach; ?>
          </select>
          <button class="btn btn-primary">Update Status</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-md-8">
    <div class="card">
      <div class="card-header"><h3 class="card-title">Items in this Order</h3></div>
      <div class="card-body table-responsive">
        <table class="table table-bordered">
          <thead><tr><th>Product</th><th>Quantity</th><th>Unit Price</th><th>Subtotal</th></tr></thead>
          <tbody>
            <?php foreach ($items as $it): ?>
              <tr>
                <td><?= e($it['product_name']) ?></td>
                <td><?= (int)$it['quantity'] ?></td>
                <td><?= money($it['unit_price']) ?></td>
                <td><?= money($it['subtotal']) ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if (!$items): ?><tr><td colspan="4">No order items.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<a href="index.php" class="btn btn-secondary mb-3">Back to Orders</a>
<?php require __DIR__ . '/../includes/footer.php'; ?>
