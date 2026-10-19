<?php
$base = '../'; $pageTitle = 'Products';
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/auth.php';

$error = '';

// DEACTIVATE (hide the product instead of deleting it, so old orders stay safe)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deactivate_id'])) {
    $stmt = $pdo->prepare('UPDATE products SET is_active = 0 WHERE product_id = ?');
    $stmt->execute([(int)$_POST['deactivate_id']]);
    redirect('index.php?deactivated=1');
}

// ADD / UPDATE
$form = ['product_id' => 0, 'category_id' => 0, 'product_name' => '', 'description' => '',
         'price' => '', 'stock_quantity' => 0, 'image_url' => '', 'is_active' => 1];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['deactivate_id'])) {
    $form = [
        'product_id'     => (int)($_POST['product_id'] ?? 0),
        'category_id'    => (int)($_POST['category_id'] ?? 0),
        'product_name'   => trim($_POST['product_name'] ?? ''),
        'description'    => trim($_POST['description'] ?? ''),
        'price'          => $_POST['price'] ?? '',
        'stock_quantity' => $_POST['stock_quantity'] ?? '',
        'image_url'      => trim($_POST['image_url'] ?? ''),
        'is_active'      => isset($_POST['is_active']) ? 1 : 0,
    ];

    if ($form['product_name'] === '' || $form['category_id'] < 1
        || !is_numeric($form['price']) || (float)$form['price'] < 0
        || filter_var($form['stock_quantity'], FILTER_VALIDATE_INT) === false || (int)$form['stock_quantity'] < 0) {
        $error = 'Enter a name, category, non-negative price and whole-number stock quantity.';
    } else {
        try {
            $values = [$form['category_id'], $form['product_name'], $form['description'] ?: null,
                       $form['price'], $form['stock_quantity'], $form['image_url'] ?: null, $form['is_active']];
            if ($form['product_id'] > 0) {
                $stmt = $pdo->prepare('UPDATE products SET category_id=?, product_name=?, description=?, price=?, stock_quantity=?, image_url=?, is_active=? WHERE product_id=?');
                $stmt->execute(array_merge($values, [$form['product_id']]));
            } else {
                $stmt = $pdo->prepare('INSERT INTO products (category_id, product_name, description, price, stock_quantity, image_url, is_active) VALUES (?,?,?,?,?,?,?)');
                $stmt->execute($values);
            }
            redirect('index.php?saved=1');
        } catch (PDOException $e) {
            $error = 'Could not save product. Check the selected category and values.';
        }
    }
}

// EDIT
if (isset($_GET['edit']) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE product_id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $found = $stmt->fetch();
    if ($found) { $form = $found; }
}

$categories = $pdo->query('SELECT category_id, category_name FROM categories ORDER BY category_name')->fetchAll();

$q = trim($_GET['q'] ?? '');
$stmt = $pdo->prepare(
    'SELECT p.*, c.category_name FROM products p
     JOIN categories c ON c.category_id = p.category_id
     WHERE p.product_name LIKE ? ORDER BY p.product_id DESC'
);
$stmt->execute(['%' . $q . '%']);
$rows = $stmt->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<?php if (isset($_GET['saved'])): ?><div class="alert alert-success">Product saved.</div><?php endif; ?>
<?php if (isset($_GET['deactivated'])): ?><div class="alert alert-success">Product deactivated (not permanently deleted).</div><?php endif; ?>

<div class="row">
  <div class="col-md-4">
    <div class="card card-primary">
      <div class="card-header"><h3 class="card-title"><?= $form['product_id'] ? 'Edit' : 'Add' ?> Product</h3></div>
      <form method="post">
        <div class="card-body">
          <input type="hidden" name="product_id" value="<?= (int)$form['product_id'] ?>">
          <div class="form-group"><label>Product Name *</label>
            <input class="form-control" name="product_name" required value="<?= e($form['product_name']) ?>"></div>
          <div class="form-group"><label>Category *</label>
            <select class="form-control" name="category_id" required>
              <option value="">Choose category</option>
              <?php foreach ($categories as $c): ?>
                <option value="<?= (int)$c['category_id'] ?>" <?= (int)$form['category_id'] === (int)$c['category_id'] ? 'selected' : '' ?>><?= e($c['category_name']) ?></option>
              <?php endforeach; ?>
            </select></div>
          <div class="form-group"><label>Description</label>
            <textarea class="form-control" name="description"><?= e($form['description']) ?></textarea></div>
          <div class="form-group"><label>Price (TSh) *</label>
            <input type="number" min="0" step="0.01" class="form-control" name="price" required value="<?= e($form['price']) ?>"></div>
          <div class="form-group"><label>Stock Quantity *</label>
            <input type="number" min="0" step="1" class="form-control" name="stock_quantity" required value="<?= e($form['stock_quantity']) ?>"></div>
          <div class="form-group"><label>Image URL</label>
            <input class="form-control" name="image_url" value="<?= e($form['image_url']) ?>"></div>
          <div class="form-check">
            <input type="checkbox" class="form-check-input" id="active" name="is_active" <?= (int)$form['is_active'] === 1 ? 'checked' : '' ?>>
            <label for="active" class="form-check-label">Active product</label>
          </div>
        </div>
        <div class="card-footer">
          <button class="btn btn-primary">Save Product</button>
          <a href="index.php" class="btn btn-secondary">Clear</a>
        </div>
      </form>
    </div>
  </div>

  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <form method="get" class="form-inline">
          <input class="form-control mr-2" name="q" placeholder="Search product name" value="<?= e($q) ?>">
          <button class="btn btn-primary">Search</button>
        </form>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover">
          <thead><tr><th>ID</th><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th>Action</th></tr></thead>
          <tbody>
            <?php foreach ($rows as $r): ?>
              <tr>
                <td><?= (int)$r['product_id'] ?></td>
                <td><?= e($r['product_name']) ?></td>
                <td><?= e($r['category_name']) ?></td>
                <td><?= money($r['price']) ?></td>
                <td><?= (int)$r['stock_quantity'] ?></td>
                <td>
                  <?php if (!$r['is_active']): ?><span class="badge badge-secondary">Inactive</span>
                  <?php elseif ($r['stock_quantity'] <= 0): ?><span class="badge badge-danger">Out of stock</span>
                  <?php elseif ($r['stock_quantity'] <= 5): ?><span class="badge badge-warning">Low stock</span>
                  <?php else: ?><span class="badge badge-success">In stock</span><?php endif; ?>
                </td>
                <td class="text-nowrap">
                  <a class="btn btn-sm btn-warning" href="?edit=<?= (int)$r['product_id'] ?>">Edit</a>
                  <form method="post" class="d-inline" onsubmit="return confirm('Deactivate this product?')">
                    <input type="hidden" name="deactivate_id" value="<?= (int)$r['product_id'] ?>">
                    <button class="btn btn-sm btn-danger">Deactivate</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (!$rows): ?><tr><td colspan="7">No products found.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
