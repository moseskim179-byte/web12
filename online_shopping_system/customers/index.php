<?php
$base = '../'; $pageTitle = 'Customers';
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/auth.php';

$error = '';

// ---- DELETE (sent by POST from the Delete button) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    try {
        $stmt = $pdo->prepare('DELETE FROM customers WHERE customer_id = ?');
        $stmt->execute([(int)$_POST['delete_id']]);
        redirect('index.php?deleted=1');
    } catch (PDOException $e) {
        $error = 'This customer has orders, so it cannot be deleted.';
    }
}

// ---- ADD or UPDATE (sent by POST from the form) ----
$form = ['customer_id' => 0, 'full_name' => '', 'email' => '', 'phone' => '', 'address' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_id'])) {
    $form = [
        'customer_id' => (int)($_POST['customer_id'] ?? 0),
        'full_name'   => trim($_POST['full_name'] ?? ''),
        'email'       => trim($_POST['email'] ?? ''),
        'phone'       => trim($_POST['phone'] ?? ''),
        'address'     => trim($_POST['address'] ?? ''),
    ];

    if ($form['full_name'] === '') {
        $error = 'Full name is required.';
    } elseif (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter a valid email address.';
    } else {
        try {
            if ($form['customer_id'] > 0) {
                $stmt = $pdo->prepare('UPDATE customers SET full_name=?, email=?, phone=?, address=? WHERE customer_id=?');
                $stmt->execute([$form['full_name'], $form['email'], $form['phone'] ?: null, $form['address'] ?: null, $form['customer_id']]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO customers (full_name, email, phone, address) VALUES (?,?,?,?)');
                $stmt->execute([$form['full_name'], $form['email'], $form['phone'] ?: null, $form['address'] ?: null]);
            }
            redirect('index.php?saved=1');
        } catch (PDOException $e) {
            $error = 'Could not save. This email may already belong to another customer.';
        }
    }
}

// ---- EDIT: load the customer into the form ----
if (isset($_GET['edit']) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare('SELECT * FROM customers WHERE customer_id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $found = $stmt->fetch();
    if ($found) { $form = $found; }
}

// ---- SEARCH + LIST ----
$q = trim($_GET['q'] ?? '');
$stmt = $pdo->prepare(
    'SELECT c.*, COUNT(o.order_id) AS order_count
     FROM customers c LEFT JOIN orders o ON o.customer_id = c.customer_id
     WHERE c.full_name LIKE ? OR c.email LIKE ?
     GROUP BY c.customer_id ORDER BY c.customer_id DESC'
);
$stmt->execute(['%' . $q . '%', '%' . $q . '%']);
$rows = $stmt->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<?php if (isset($_GET['saved'])): ?><div class="alert alert-success">Customer saved.</div><?php endif; ?>
<?php if (isset($_GET['deleted'])): ?><div class="alert alert-success">Customer deleted.</div><?php endif; ?>

<div class="row">
  <div class="col-md-4">
    <div class="card card-primary">
      <div class="card-header"><h3 class="card-title"><?= $form['customer_id'] ? 'Edit' : 'Add' ?> Customer</h3></div>
      <form method="post">
        <div class="card-body">
          <input type="hidden" name="customer_id" value="<?= (int)$form['customer_id'] ?>">
          <div class="form-group"><label>Full Name *</label>
            <input class="form-control" name="full_name" required maxlength="120" value="<?= e($form['full_name']) ?>"></div>
          <div class="form-group"><label>Email *</label>
            <input type="email" class="form-control" name="email" required maxlength="150" value="<?= e($form['email']) ?>"></div>
          <div class="form-group"><label>Phone</label>
            <input class="form-control" name="phone" maxlength="20" value="<?= e($form['phone']) ?>"></div>
          <div class="form-group"><label>Address</label>
            <input class="form-control" name="address" maxlength="255" value="<?= e($form['address']) ?>"></div>
        </div>
        <div class="card-footer">
          <button class="btn btn-primary">Save Customer</button>
          <a href="index.php" class="btn btn-secondary">Clear</a>
        </div>
      </form>
    </div>
  </div>

  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <form method="get" class="form-inline">
          <input class="form-control mr-2" name="q" placeholder="Search name or email" value="<?= e($q) ?>">
          <button class="btn btn-primary">Search</button>
        </form>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover">
          <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Address</th><th>Orders</th><th>Actions</th></tr></thead>
          <tbody>
            <?php foreach ($rows as $r): ?>
              <tr>
                <td><?= (int)$r['customer_id'] ?></td>
                <td><?= e($r['full_name']) ?></td>
                <td><?= e($r['email']) ?></td>
                <td><?= e($r['phone']) ?></td>
                <td><?= e($r['address']) ?></td>
                <td><?= (int)$r['order_count'] ?></td>
                <td class="text-nowrap">
                  <a class="btn btn-sm btn-warning" href="?edit=<?= (int)$r['customer_id'] ?>">Edit</a>
                  <form method="post" class="d-inline" onsubmit="return confirm('Delete this customer?')">
                    <input type="hidden" name="delete_id" value="<?= (int)$r['customer_id'] ?>">
                    <button class="btn btn-sm btn-danger">Delete</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (!$rows): ?><tr><td colspan="7">No customers found.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
