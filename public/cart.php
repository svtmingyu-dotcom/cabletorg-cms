<?php
require_once '../config.php';
session_start();

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_name  = $_POST['name'] ?? '';
    $user_phone = $_POST['phone'] ?? '';
    $user_email = $_POST['email'] ?? '';
    $delivery   = $_POST['delivery'] ?? '';
    $cart_json  = $_POST['cart'] ?? '[]';
    $cart       = json_decode($cart_json, true);

    if ($user_name && $user_phone && !empty($cart)) {
        $total_price = 0;
        foreach ($cart as $item) $total_price += $item['price'] * $item['quantity'];

        $user_id = $_SESSION['user_id'] ?? null;

        $stmt = $pdo->prepare("
            INSERT INTO orders 
            (user_id, user_name, user_phone, user_email, total_price, status, delivery, created_at, items)
            VALUES (?, ?, ?, ?, ?, 'новый', ?, NOW(), ?)
        ");
        $stmt->execute([
            $user_id,
            $user_name,
            $user_phone,
            $user_email,
            $total_price,
            $delivery, // <- сохраняем доставку
            json_encode($cart, JSON_UNESCAPED_UNICODE)
        ]);

        $message = "Заказ успешно оформлен!";
    } else {
        $message = "Заполните все поля и добавьте товары в корзину.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Корзина</title>
<link rel="stylesheet" href="assets/css/catalog.css">
<link rel="stylesheet" href="assets/css/cart.css">
</head>
<body>

<header>
    <h1>Корзина</h1>
    <nav>
        <a href="index.php">Каталог</a>
            <?php if (!empty($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
        <a href="../admin/index.php">Админ-панель</a>
    <?php endif; ?>
        <a href="account.php">Аккаунт</a>
    </nav>
</header>

<section class="cart-container">
    <div id="cartList" class="cart-items"></div>
    <div class="cart-total">
        <h3>Итого: <span id="cartTotal">0</span> руб.</h3>
    </div>

    <form method="post" class="cart-form" onsubmit="prepareCartForSubmit()">
        <h3>Данные для заказа</h3>
        <label>Имя:<br><input type="text" name="name" required></label>
        <label>Телефон:<br><input type="tel" name="phone" required></label>
        <label>Email:<br><input type="email" name="email"></label>
        <label>Доставка:<br>
            <select name="delivery" required>
                <option value="самовывоз">Самовывоз</option>
                <option value="курьер">Курьер</option>
            </select>
        </label>
        <input type="hidden" name="cart" id="cartInput">
        <button type="submit">Подтвердить заказ</button>
    </form>

    <?php if ($message): ?>
        <p class="cart-message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
</section>

<script src="assets/js/cart.js"></script>
</body>
</html>
