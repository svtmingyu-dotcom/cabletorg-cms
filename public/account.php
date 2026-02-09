<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Получаем заказы пользователя
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Личный кабинет</title>
<link rel="stylesheet" href="assets/css/account.css">
</head>
<body>

<header>
    <h1>Личный кабинет</h1>
    <nav>
        <a href="index.php">Каталог</a>
        <a href="cart.php">Корзина</a>
                   <?php if (!empty($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
        <a href="../admin/index.php">Админ-панель</a>
    <?php endif; ?>
        <a href="logout.php">Выйти</a>
    </nav>
    <p class="greeting">Привет, <span><?= htmlspecialchars($user_name) ?></span>!</p>
</header>

<section class="orders">
    <h2>История заказов</h2>

    <?php if (empty($orders)): ?>
        <p class="no-orders">Заказы отсутствуют</p>
    <?php else: ?>
        <div class="orders-grid">
            <?php foreach ($orders as $order): ?>
            <div class="order-card">
                <div class="order-header">
                    <div class="order-number">Заказ #<?= $order['id'] ?></div>
                    <div class="order-date"><?= date("d.m.Y H:i", strtotime($order['created_at'])) ?></div>
                </div>
                <div class="order-status <?= strtolower($order['status']) ?>">
                    <?= htmlspecialchars($order['status']) ?>
                </div>
                <div class="order-total">
                    <b>Сумма:</b> <?= $order['total_price'] ?> руб.
                </div>
                <div class="order-items">
                    <b>Товары:</b>
                    <ul>
                        <?php 
                        $items = json_decode($order['items'], true);
                        foreach ($items as $item): ?>
                        <li><?= htmlspecialchars($item['name']) ?> — <?= $item['price'] ?> руб. × <?= $item['quantity'] ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

</body>
</html>
