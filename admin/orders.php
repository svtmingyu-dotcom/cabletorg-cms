<?php
session_start();
require_once '../config.php';

// только админ
if (empty($_SESSION['user_id']) || ($_SESSION['is_admin'] ?? 0) != 1) {
    header("Location: ../login.php");
    exit;
}

// смена статуса
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = trim($_POST['status']);

    $valid_statuses = ['новый','в обработке','выполнен','отменен'];
    if (in_array($status, $valid_statuses)) {
        $stmt = $pdo->prepare("UPDATE orders SET status=? WHERE id=?");
        $stmt->execute([$status, $order_id]);
    }

    // редирект после обновления, чтобы избежать повторного submit
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// экспорт CSV
if (!empty($_GET['export'])) {
    $orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll();
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="orders.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['ID','Имя','Телефон','Email','Сумма','Статус','Дата','Доставка','Товары']);
    foreach ($orders as $o) {
        fputcsv($out, [
            $o['id'], $o['user_name'], $o['user_phone'], $o['user_email'], 
            $o['total_price'], $o['status'], $o['created_at'], $o['delivery'], $o['items']
        ]);
    }
    fclose($out);
    exit;
}

// получаем все заказы
$orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Заказы</title>
<link rel="stylesheet" href="../public/assets/css/orders.css">
</head>
<body>
<h1>Заказы</h1>
<a href="index.php">← Назад</a> | <a href="?export=1">Экспорт CSV</a>
<hr>

<table border="1" cellpadding="5">
<tr>
    <th>ID</th><th>Имя</th><th>Телефон</th><th>Email</th>
    <th>Сумма</th><th>Доставка</th><th>Статус</th><th>Дата</th><th>Товары</th>
</tr>

<?php foreach ($orders as $o): 
    $items = json_decode($o['items'], true) ?: [];
?>
<tr>
    <td><?= $o['id'] ?></td>
    <td><?= htmlspecialchars($o['user_name']) ?></td>
    <td><?= htmlspecialchars($o['user_phone']) ?></td>
    <td><?= htmlspecialchars($o['user_email']) ?></td>
    <td><?= $o['total_price'] ?></td>
    <td><?= htmlspecialchars($o['delivery'] ?? '-') ?></td>
    <td>
        <form method="post" style="margin:0;">
            <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
            <select name="status" onchange="this.form.submit()">
                <?php foreach(['новый','в обработке','выполнен','отменен'] as $s): ?>
                    <option value="<?= $s ?>" <?= $o['status']==$s?'selected':'' ?>><?= $s ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </td>
    <td><?= $o['created_at'] ?></td>
    <td>
        <ul>
            <?php foreach($items as $i): ?>
                <li><?= htmlspecialchars($i['name']) ?> × <?= $i['quantity'] ?></li>
            <?php endforeach; ?>
        </ul>
    </td>
</tr>
<?php endforeach; ?>
</table>
</body>
</html>
