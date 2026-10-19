<?php
require_once __DIR__ . '/functions.php';
$pageTitle = $pageTitle ?? 'Dashboard';
$base      = $base ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?> | Online Shopping Admin</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <ul class="navbar-nav">
    <li class="nav-item"><span class="nav-link"><strong>Online Shopping Management System</strong></span></li>
  </ul>
  <ul class="navbar-nav ml-auto">
    <li class="nav-item"><span class="nav-link"><i class="fas fa-user"></i> <?= e($_SESSION['full_name'] ?? '') ?></span></li>
    <li class="nav-item"><a class="nav-link text-danger" href="<?= $base ?>logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
  </ul>
</nav>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="<?= $base ?>index.php" class="brand-link"><span class="brand-text font-weight-light ml-2">Online Shopping</span></a>
  <div class="sidebar"><?php require __DIR__ . '/sidebar.php'; ?></div>
</aside>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1><?= e($pageTitle) ?></h1></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= $base ?>index.php">Home</a></li>
            <li class="breadcrumb-item active"><?= e($pageTitle) ?></li>
          </ol>
        </div>
      </div>
    </div>
  </section>
  <section class="content"><div class="container-fluid">
