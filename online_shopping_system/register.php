<?php
require __DIR__ . '/config/db.php';
require __DIR__ . '/includes/functions.php';

// Already logged in? Go to dashboard.
if (!empty($_SESSION['user_id'])) {
    redirect('index.php');
}

$error = '';
$full_name = '';
$username  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';

    if ($full_name === '' || $username === '') {
        $error = 'Full name and username are required.';
    } elseif (!preg_match('/^[A-Za-z0-9_]{3,50}$/', $username)) {
        $error = 'Username must be 3-50 characters: letters, numbers or underscore only.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'The two passwords do not match.';
    } else {
        // Is the username already taken?
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
        $stmt->execute([$username]);

        if ($stmt->fetchColumn() > 0) {
            $error = 'That username is already taken. Choose another.';
        } else {
            // Save the user with a hashed password
            $stmt = $pdo->prepare('INSERT INTO users (username, password_hash, full_name) VALUES (?,?,?)');
            $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT), $full_name]);

            // Log the new user in straight away
            session_regenerate_id(true);
            $_SESSION['user_id']   = (int)$pdo->lastInsertId();
            $_SESSION['full_name'] = $full_name;
            redirect('index.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sign Up | Online Shopping Admin</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="hold-transition register-page">
<div class="register-box">
  <div class="card card-outline card-primary">
    <div class="card-header text-center"><h1 class="h3"><b>Create</b> Account</h1></div>
    <div class="card-body">
      <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
      <form method="post">
        <div class="input-group mb-3">
          <input type="text" name="full_name" class="form-control" placeholder="Full name" required maxlength="100" value="<?= e($full_name) ?>">
          <div class="input-group-append"><div class="input-group-text"><span class="fas fa-id-card"></span></div></div>
        </div>
        <div class="input-group mb-3">
          <input type="text" name="username" class="form-control" placeholder="Choose a username" required maxlength="50" value="<?= e($username) ?>">
          <div class="input-group-append"><div class="input-group-text"><span class="fas fa-user"></span></div></div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Choose a password (min 6)" required>
          <div class="input-group-append"><div class="input-group-text"><span class="fas fa-lock"></span></div></div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="confirm_password" class="form-control" placeholder="Repeat password" required>
          <div class="input-group-append"><div class="input-group-text"><span class="fas fa-lock"></span></div></div>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Sign Up</button>
      </form>
      <p class="mt-3 mb-0 text-center"><a href="login.php">I already have an account</a></p>
    </div>
  </div>
</div>
</body>
</html>
