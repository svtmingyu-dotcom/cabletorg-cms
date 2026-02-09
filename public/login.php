<html>
<?php
session_start();
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR phone = ?");
    $stmt->execute([$login, $login]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Сохраняем сессию
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['is_admin'] = $user['is_admin'];

        header("Location: account.php");
        exit;
    } else {
        $error = "Неверный логин или пароль";
    }
}
?>

</html>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Вход</title>
<link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

<div class="login-wrapper">
    <h1>Вход</h1>

    <?php if (!empty($error)): ?>
        <p class="error-message"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="login" placeholder="Email или телефон" required>
        <input type="password" name="password" placeholder="Пароль" required>
        <button type="submit">Войти</button>
    </form>

    <p class="register-link">Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
</div>

</body>
</html>

