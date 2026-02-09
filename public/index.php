<?php
require_once '../config.php';
session_start();

/* ---------- КАТЕГОРИИ ---------- */
$catStmt = $pdo->query("SELECT * FROM categories ORDER BY sort_order");
$categories = $catStmt->fetchAll();

/* ---------- ФИЛЬТРЫ ---------- */
$where = [];
$params = [];

if (!empty($_GET['category'])) {
    $where[] = "category_id = ?";
    $params[] = $_GET['category'];
}
if (isset($_GET['min_price']) && $_GET['min_price'] !== '') {
    $where[] = "price >= ?";
    $params[] = $_GET['min_price'];
}
if (isset($_GET['max_price']) && $_GET['max_price'] !== '') {
    $where[] = "price <= ?";
    $params[] = $_GET['max_price'];
}
if (!empty($_GET['in_stock'])) {
    $where[] = "stock > 0";
}
if (!empty($_GET['search'])) {
    $where[] = "(name LIKE ? OR article LIKE ?)";
    $params[] = "%" . $_GET['search'] . "%";
    $params[] = "%" . $_GET['search'] . "%";
}

/* ---------- SQL ---------- */
$sql = "SELECT * FROM products";
if ($where) $sql .= " WHERE " . implode(" AND ", $where);
if (!empty($_GET['sort'])) {
    if ($_GET['sort'] === 'asc') $sql .= " ORDER BY price ASC";
    elseif ($_GET['sort'] === 'desc') $sql .= " ORDER BY price DESC";
    elseif ($_GET['sort'] === 'new') $sql .= " ORDER BY id DESC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Каталог кабельной продукции</title>
<link rel="stylesheet" href="assets/css/catalog.css">
<style>
/* маленький счетчик рядом с кнопкой */
.cart-count {
    margin-left: 5px;
    font-weight: bold;
    color: #0066cc;
}
</style>
</head>
<body>

<header>
    <h1>Каталог кабельной продукции</h1>
<nav>
    <a href="index.php">Каталог</a>
    <a href="cart.php">Корзина</a>

    <?php if (!empty($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
        <a href="../admin/index.php">Админ-панель</a>
    <?php endif; ?>

    <?php if (!empty($_SESSION['user_id'])): ?>
        <a href="account.php">Аккаунт</a>
    <?php else: ?>
        <a href="login.php">Войти</a>
    <?php endif; ?>
</nav>
</header>

<section class="filters">
    <form method="GET">
        <input type="text" name="search" placeholder="Поиск" value="<?= $_GET['search'] ?? '' ?>">
        <select name="category">
            <option value="">Все категории</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= $c['id'] ?>" <?= (!empty($_GET['category']) && $_GET['category']==$c['id'])?'selected':'' ?>>
                    <?= htmlspecialchars($c['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="number" name="min_price" placeholder="Цена от" value="<?= $_GET['min_price'] ?? '' ?>">
        <input type="number" name="max_price" placeholder="до" value="<?= $_GET['max_price'] ?? '' ?>">

        <label>
            <input type="checkbox" name="in_stock" value="1" <?= !empty($_GET['in_stock'])?'checked':'' ?>>
            В наличии
        </label>

        <select name="sort">
            <option value="">Сортировка</option>
            <option value="asc" <?= ($_GET['sort'] ?? '')=='asc'?'selected':'' ?>>Цена ↑</option>
            <option value="desc" <?= ($_GET['sort'] ?? '')=='desc'?'selected':'' ?>>Цена ↓</option>
            <option value="new" <?= ($_GET['sort'] ?? '')=='new'?'selected':'' ?>>Новизна</option>
        </select>


        <button type="submit">Применить</button>
    </form>
</section>

<section class="products-grid">
    <?php if (!$products): ?>
        <p class="no-products">Товары не найдены</p>
    <?php else: ?>
        <?php foreach ($products as $p): 
            $img = "assets/images/" . $p['image'];
            if (empty($p['image']) || !file_exists(__DIR__ . '/' . $img)) {
                $img = "assets/images/placeholder.jpg";
            }
        ?>
        <div class="product-card">
            <img src="<?= $img ?>" alt="<?= htmlspecialchars($p['name']) ?>">
            <div class="product-info">
                <h3><?= htmlspecialchars($p['name']) ?></h3>
                <p>Артикул: <?= htmlspecialchars($p['article']) ?></p>
                <p>Цена: <?= $p['price'] ?> руб.</p>
                <span class="<?= $p['stock'] > 0 ? 'in-stock' : 'out-of-stock' ?>">
                    <?= $p['stock'] > 0 ? 'В наличии' : 'Нет в наличии' ?>
                </span>
            </div>
            <div class="product-actions">
                <button <?= $p['stock'] <= 0 ? 'disabled' : '' ?>
                    id="addBtn-<?= $p['id'] ?>"
                    onclick="addToCartCheckLogin(
                        <?= $p['id'] ?>, 
                        '<?= addslashes($p['name']) ?>', 
                        <?= $p['price'] ?>, 
                        <?= !empty($_SESSION['user_id']) ? 'true' : 'false' ?>,
                        '<?= $img ?>',
                        '<?= addslashes($p['article']) ?>'
                    )">
                    В корзину
                </button>
                <span id="count-<?= $p['id'] ?>" class="cart-count"></span>
                <a href="product.php?id=<?= $p['id'] ?>">Подробнее</a>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<script>
function getCart() {
    return JSON.parse(localStorage.getItem('cart')) || [];
}

function saveCart(cart) {
    localStorage.setItem('cart', JSON.stringify(cart));
}

function addToCartCheckLogin(id, name, price, isLoggedIn, image = '', article = '') {
    if (!isLoggedIn) {
        alert('Сначала нужно войти или создать аккаунт');
        window.location.href = 'login.php';
        return;
    }
    addToCart({id, name, price, image, article});
}

function addToCart(product) {
    let cart = getCart();
    let existing = cart.find(item => item.id === product.id);
    if (existing) {
        existing.quantity += 1;
    } else {
        product.quantity = 1;
        cart.push(product);
    }
    saveCart(cart);
    updateCartCount(product.id);
    alert('Товар добавлен в корзину');
}

function updateCartCount(productId) {
    const cart = getCart();
    const countEl = document.getElementById('count-' + productId);
    const item = cart.find(i => i.id === productId);
    countEl.textContent = item ? `(${item.quantity})` : '';
}

// обновляем все счетчики при загрузке страницы
document.addEventListener('DOMContentLoaded', () => {
    const cart = getCart();
    cart.forEach(item => updateCartCount(item.id));
});
</script>
</body>
</html>
