<?php
session_start();
require_once '../config.php';

if (empty($_SESSION['user_id']) || ($_SESSION['is_admin'] ?? 0) != 1) {
    header("Location: ../login.php");
    exit;
}

$uploadDir = __DIR__ . '/../public/assets/images/';
$categories = $pdo->query("SELECT * FROM categories ORDER BY sort_order")->fetchAll();

// ---------- Удаление фото ----------
if (!empty($_GET['delete_image'])) {
    $imgId = (int)$_GET['delete_image'];
    $stmt = $pdo->prepare("SELECT filename, product_id FROM product_images WHERE id=?");
    $stmt->execute([$imgId]);
    $img = $stmt->fetch();
    if ($img) {
        @unlink($uploadDir . $img['filename']);
        $pdo->prepare("DELETE FROM product_images WHERE id=?")->execute([$imgId]);
        header("Location: products.php?edit_id={$img['product_id']}");
        exit;
    }
}

// ---------- Удаление основного фото ----------
if (!empty($_GET['delete_main_image'])) {
    $productId = (int)$_GET['delete_main_image'];
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id=?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    if ($product && $product['image']) {
        @unlink($uploadDir . $product['image']);
        $pdo->prepare("UPDATE products SET image=NULL WHERE id=?")->execute([$productId]);
        header("Location: products.php?edit_id={$productId}");
        exit;
    }
}

// ---------- Редактирование ----------
$editing = null;
$additionalImages = [];
if (!empty($_GET['edit_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
    $stmt->execute([$_GET['edit_id']]);
    $editing = $stmt->fetch();
    if ($editing) {
        $editing['specs'] = !empty($editing['specs'])
        ? json_decode($editing['specs'], true)
        : [];

        // доп фото
        $stmt = $pdo->prepare("SELECT id, filename FROM product_images WHERE product_id=? ORDER BY id ASC");
        $stmt->execute([$editing['id']]);
        $additionalImages = $stmt->fetchAll();
    }
}

// ---------- Сохранение ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name'] ?? '');
    $article = trim($_POST['article'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $unit = trim($_POST['unit'] ?? '');
    $category_id = $_POST['category_id'] ?: null;
    $full_desc = trim($_POST['full_desc'] ?? '');
    $specs = json_decode($_POST['specs'] ?? '', true) ?: [];

    if ($id) {
        $stmt = $pdo->prepare("
            UPDATE products
            SET name=?, article=?, price=?, stock=?, unit=?, category_id=?, full_desc=?, specs=?
            WHERE id=?
        ");
        $stmt->execute([
            $name, $article, $price, $stock, $unit,
            $category_id, $full_desc,
            json_encode($specs, JSON_UNESCAPED_UNICODE),
            $id
        ]);
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO products (name, article, price, stock, unit, category_id, full_desc, specs)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $name, $article, $price, $stock, $unit,
            $category_id, $full_desc,
            json_encode($specs, JSON_UNESCAPED_UNICODE)
        ]);
        $id = $pdo->lastInsertId();
    }

    // Загрузка основного фото
    if (!empty($_FILES['main_image']['tmp_name'])) {
        $mainName = "product_{$id}.jpg";
        move_uploaded_file($_FILES['main_image']['tmp_name'], $uploadDir . $mainName);
        $pdo->prepare("UPDATE products SET image=? WHERE id=?")->execute([$mainName, $id]);
    }

    // Загрузка дополнительных фото
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['name'] as $index => $nameFile) {
            if ($_FILES['images']['error'][$index] === UPLOAD_ERR_OK &&
                is_uploaded_file($_FILES['images']['tmp_name'][$index])) {
                $fileName = "product_{$id}_" . time() . "_{$index}.jpg";
                move_uploaded_file($_FILES['images']['tmp_name'][$index], $uploadDir . $fileName);
                $pdo->prepare("INSERT INTO product_images (product_id, filename) VALUES (?, ?)")->execute([$id, $fileName]);
            }
        }
    }

    header("Location: products.php?edit_id={$id}");
    exit;
}

// ---------- Удаление товара ----------
if (!empty($_GET['delete_id'])) {
    // сначала удаляем все доп. фото
    $stmt = $pdo->prepare("SELECT filename FROM product_images WHERE product_id=?");
    $stmt->execute([$_GET['delete_id']]);
    foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $f) {
        @unlink($uploadDir . $f);
    }
    $pdo->prepare("DELETE FROM product_images WHERE product_id=?")->execute([$_GET['delete_id']]);

    // удаляем основное фото
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id=?");
    $stmt->execute([$_GET['delete_id']]);
    $img = $stmt->fetchColumn();
    if ($img) @unlink($uploadDir . $img);

    // удаляем сам товар
    $stmt = $pdo->prepare("DELETE FROM products WHERE id=?");
    $stmt->execute([$_GET['delete_id']]);

    header("Location: products.php");
    exit;
}

// ---------- Список товаров ----------
$products = $pdo->query("
    SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.id DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Товары</title>
<link rel="stylesheet" href="../public/assets/css/product_admin.css">
</head>
<body>

<h1>Товары</h1>
<a href="index.php">← Назад</a>
<hr>

<h2><?= $editing ? 'Редактировать' : 'Добавить' ?> товар</h2>

<form method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?= $editing['id'] ?? '' ?>">

Название:<br>
<input type="text" name="name" required value="<?= htmlspecialchars($editing['name'] ?? '') ?>"><br>

Артикул:<br>
<input type="text" name="article" value="<?= htmlspecialchars($editing['article'] ?? '') ?>"><br>

Цена:<br>
<input type="number" name="price" value="<?= $editing['price'] ?? 0 ?>"><br>

Остаток:<br>
<input type="number" name="stock" value="<?= $editing['stock'] ?? 0 ?>"><br>

Единица:<br>
<input type="text" name="unit" value="<?= htmlspecialchars($editing['unit'] ?? '') ?>"><br>

Категория:<br>
<select name="category_id">
<option value="">—</option>
<?php foreach ($categories as $c): ?>
<option value="<?= $c['id'] ?>" <?= ($editing['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
<?= htmlspecialchars($c['name']) ?>
</option>
<?php endforeach; ?>
</select><br>

Описание:<br>
<textarea name="full_desc"><?= htmlspecialchars($editing['full_desc'] ?? '') ?></textarea><br>

<h3>Характеристики</h3>
<div id="specs-list">
<?php foreach (($editing['specs'] ?? []) as $k => $v): ?>
<div class="spec-row">
<input class="spec-key" value="<?= htmlspecialchars($k) ?>">
<input class="spec-value" value="<?= htmlspecialchars($v) ?>">
<button type="button" onclick="this.parentElement.remove()">✕</button>
</div>
<?php endforeach; ?>
</div>
<button type="button" onclick="addSpec()">+ характеристика</button>
<input type="hidden" name="specs" id="specsInput">

<h3>Фото</h3>
Основное фото:<br>
<?php if (!empty($editing['image'])): ?>
    <img src="../public/assets/images/<?= $editing['image'] ?>" width="100">
    <a href="?delete_main_image=<?= $editing['id'] ?>" onclick="return confirm('Удалить основное фото?')">✕ удалить</a><br>
<?php endif; ?>
<input type="file" name="main_image"><br><br>

Дополнительные фото:<br>
<?php foreach ($additionalImages as $img): ?>
    <img src="../public/assets/images/<?= $img['filename'] ?>" width="80">
    <a href="?delete_image=<?= $img['id'] ?>" onclick="return confirm('Удалить это фото?')">✕</a><br>
<?php endforeach; ?>
<input type="file" name="images[]" multiple><br><br>

<button type="submit">Сохранить</button>
</form>

<hr>

<h2>Список товаров</h2>

<?php if (!$products): ?>
<p>Товаров нет</p>
<?php else: ?>
<table border="1" cellpadding="6">
<tr>
<th>ID</th><th>Название</th><th>Категория</th><th>Цена</th><th>Остаток</th><th>Действия</th>
</tr>
<?php foreach ($products as $p): ?>
<tr>
<td><?= $p['id'] ?></td>
<td><?= htmlspecialchars($p['name']) ?></td>
<td><?= htmlspecialchars($p['category_name']) ?></td>
<td><?= $p['price'] ?></td>
<td><?= $p['stock'] ?></td>
<td>
<a href="?edit_id=<?= $p['id'] ?>">Редактировать</a> |
<a href="?delete_id=<?= $p['id'] ?>" onclick="return confirm('Удалить?')">✕</a>
</td>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>

<script>
function addSpec() {
    const d = document.createElement('div');
    d.className = 'spec-row';
    d.innerHTML = `
        <input class="spec-key">
        <input class="spec-value">
        <button type="button" onclick="this.parentElement.remove()">✕</button>
    `;
    document.getElementById('specs-list').appendChild(d);
}

document.querySelector('form').addEventListener('submit', () => {
    const specs = {};
    document.querySelectorAll('.spec-row').forEach(r => {
        const k = r.querySelector('.spec-key').value.trim();
        const v = r.querySelector('.spec-value').value.trim();
        if (k) specs[k] = v;
    });
    document.getElementById('specsInput').value = JSON.stringify(specs);
});
</script>

</body>
</html>
