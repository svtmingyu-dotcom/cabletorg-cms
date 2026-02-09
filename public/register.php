<?php
require_once '../config.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        // проверка, есть ли пользователь с таким email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $message = "Пользователь с таким email уже существует.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (email, phone, password) VALUES (?, ?, ?)");
            $stmt->execute([$email, $phone, $hash]);
            $message = "Регистрация прошла успешно! Можете войти.";
        }
    } else {
        $message = "Введите email и пароль.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Регистрация</title>
<link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

<div class="login-wrapper">
    <h1>Регистрация</h1>

    <?php if (!empty($message)): ?>
        <p class="error-message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="email" name="email" placeholder="Email" required>
        <input type="tel" name="phone" placeholder="Телефон (необязательно)">
        <input type="password" name="password" placeholder="Пароль" required>
        <button type="submit">Зарегистрироваться</button>
    </form>

    <p class="register-link">Уже есть аккаунт? <a href="login.php">Войти</a></p>
</div>

</body>
</html>
