<?php
$base = '../'; $pageTitle = 'Categories';
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/auth.php';

$error = '';

// DELETE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    try {
        $stmt = $pdo->prepare('DELETE FROM categories WHERE category_id = ?');
        $stmt->execute([(int)$_POST['delete_id']]);
        redirect('index.php?deleted=1');
    } catch (PDOException $e) {
        $error = 'This category has products, so it cannot be deleted. Move the products first.';
    }
}

// ADD / UPDATE
$form = ['category_id' => 0, 'category_name' => '', 'description' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_id'])) {
    $form = [
        'category_id'   => (int)($_POST['category_id'] ?? 0),
        'category_name' => trim($_POST['category_name'] ?? ''),
        'description'   => trim($_POST['description'] ?? ''),
    ];
    if ($form['category_name'] === '') {
        $error = 'Category name is required.';
    } else {
        try {
            if ($form['category_id'] > 0) {
                $stmt = $pdo->prepare('UPDATE categories SET category_name=?, description=? WHERE category_id=?');
                $stmt->execute([$form['category_name'], $form['description'] ?: null, $form['category_id']]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO categories (category_name, description) VALUES (?,?)');
                $stmt->execute([$form['category_name'], $form['description'] ?: null]);
            }
            redirect('index.php?saved=1');
        } catch (PDOException $e) {
            $error = 'Could not save. The category name may already exist.';
        }
    }
}

// EDIT
if (isset($_GET['edit']) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE category_id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $found = $stmt->fetch();
    if ($found) { $form = $found; }
}

$rows = $pdo->query(
    'SELECT c.*, COUNT(p.product_id) AS product_count
     FROM categories c LEFT JOIN products p ON p.category_id = c.category_id
     GROUP BY c.category_id ORDER BY c.category_name'
)->fetchAll();

require __DIR__ . '/../includes/header.php';
?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<?php if (isset($_GET['saved'])): ?><div class="alert alert-success">Category saved.</div><?php endif; ?>
<?php if (isset($_GET['deleted'])): ?><div class="alert alert-success">Category deleted.</div><?php endif; ?>

<div class="row">
  <div class="col-md-4">
    <div class="card card-primary">
      <div class="card-header"><h3 class="card-title"><?= $form['category_id'] ? 'Edit' : 'Add' ?> Category</h3></div>
      <form method="post">
        <div class="card-body">
          <input type="hidden" name="category_id" value="<?= (int)$form['category_id'] ?>">
          <div class="form-group"><label>Category Name *</label>
            <input class="form-control" name="category_name" required maxlength="100" value="<?= e($form['category_name']) ?>"></div>
          <div class="form-group"><label>Description</label>
            <textarea class="form-control" name="description" maxlength="255"><?= e($form['description']) ?></textarea></div>
        </div>
        <div class="card-footer">
          <button class="btn btn-primary">Save Category</button>
          <a href="index.php" class="btn btn-secondary">Clear</a>
        </div>
      </form>
    </div>
  </div>

  <div class="col-md-8">
    <div class="card">
      <div class="card-header"><h3 class="card-title">Category List</h3></div>
      <div class="card-body table-responsive">
        <table class="table table-bordered">
          <thead><tr><th>ID</th><th>Name</th><th>Description</th><th>Products</th><th>Actions</th></tr></thead>
          <tbody>
            <?php foreach ($rows as $r): ?>
              <tr>
                <td><?= (int)$r['category_id'] ?></td>
                <td><?= e($r['category_name']) ?></td>
                <td><?= e($r['description']) ?></td>
                <td><?= (int)$r['product_count'] ?></td>
                <td class="text-nowrap">
                  <a class="btn btn-sm btn-warning" href="?edit=<?= (int)$r['category_id'] ?>">Edit</a>
                  <form method="post" class="d-inline" onsubmit="return confirm('Delete this category?')">
                    <input type="hidden" name="delete_id" value="<?= (int)$r['category_id'] ?>">
                    <button class="btn btn-sm btn-danger">Delete</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
