<?php
session_start();
require_once '../config.php';
if (empty($_SESSION['user_id']) || ($_SESSION['is_admin'] ?? 0) != 1) {
    header("Location: ../login.php");
    exit;
}

// добавление категории
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['name'])) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, sort_order) VALUES (?, ?)");
        $stmt->execute([$_POST['name'], $_POST['sort_order'] ?? 0]);
    }
}

// удаление
if (!empty($_GET['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id=?");
    $stmt->execute([$_GET['delete_id']]);
}

// все категории
$categories = $pdo->query("SELECT * FROM categories ORDER BY sort_order")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Категории</title>
<link rel="stylesheet" href="../public/assets/css/categories.css">
</head>
<body>
<h1>Категории</h1>
<a href="index.php">← Назад</a>
<hr>
<form method="post">
    Название: <input type="text" name="name" required>
    Порядок сортировки: <input type="number" name="sort_order" value="0">
    <button type="submit">Добавить</button>
</form>
<hr>
<table border="1" cellpadding="5">
<tr><th>ID</th><th>Название</th><th>Сорт</th><th>Действия</th></tr>
<?php foreach ($categories as $c): ?>
<tr>
    <td><?= $c['id'] ?></td>
    <td><?= htmlspecialchars($c['name']) ?></td>
    <td><?= $c['sort_order'] ?></td>
    <td>
        <a href="?delete_id=<?= $c['id'] ?>" onclick="return confirm('Удалить?')">Удалить</a>
    </td>
</tr>
<?php endforeach; ?>
</table>
</body>
</html>
