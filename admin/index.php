<?php
session_start();
require_once '../config.php';
if (empty($_SESSION['user_id']) || ($_SESSION['is_admin'] ?? 0) != 1) {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Админ-панель</title>
<link rel="stylesheet" href="../public/assets/css/admin.css">
</head>
<body>
<h1>Админ-панель</h1>
<ul>
    <li><a href="categories.php">Категории</a></li>
    <li><a href="products.php">Товары</a></li>
    <li><a href="orders.php">Заказы</a></li>
    <a href="../public/index.php" class="catalog-button">В каталог</a>
</ul>
</body>
</html>
