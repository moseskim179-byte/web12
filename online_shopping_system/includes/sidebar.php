<?php
// Menu items: [link, icon, label]
$items = [
    ['index.php',            'fa-gauge-high',      'Dashboard'],
    ['categories/index.php', 'fa-tags',            'Categories'],
    ['products/index.php',   'fa-box',             'Products'],
    ['customers/index.php',  'fa-users',           'Customers'],
    ['orders/index.php',     'fa-cart-shopping',   'Orders'],
    ['payments/index.php',   'fa-money-bill-wave', 'Payments'],
];
?>
<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" role="menu">
    <?php foreach ($items as [$path, $icon, $label]): ?>
      <li class="nav-item">
        <a href="<?= $base . $path ?>" class="nav-link <?= $pageTitle === $label ? 'active' : '' ?>">
          <i class="nav-icon fas <?= $icon ?>"></i><p><?= e($label) ?></p>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</nav>
