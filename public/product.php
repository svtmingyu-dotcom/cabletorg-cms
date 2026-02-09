<?php
require_once '../config.php';
session_start();

if (empty($_GET['id']) || !ctype_digit($_GET['id'])) {
    die('Товар не найден');
}

$id = (int)$_GET['id'];

// получаем товар
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) die('Товар не найден');

$imgDir = __DIR__ . '/assets/images/';
$imgUrl = 'assets/images/';

$images = [];
if (!empty($product['image']) && file_exists($imgDir . $product['image'])) {
    $images[] = $imgUrl . $product['image'];
}

$stmt = $pdo->prepare("SELECT filename FROM product_images WHERE product_id=? ORDER BY id ASC");
$stmt->execute([$id]);
$extraImages = $stmt->fetchAll(PDO::FETCH_COLUMN);
foreach ($extraImages as $file) {
    if (file_exists($imgDir . $file)) $images[] = $imgUrl . $file;
}

if (!$images) $images[] = $imgUrl . 'placeholder.jpg';

// ===== ХАРАКТЕРИСТИКИ =====
$specs = json_decode($product['specs'] ?? '[]', true);
if (!is_array($specs)) $specs = [];

$inStock = $product['stock'] > 0;
$isLoggedIn = !empty($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($product['name']) ?></title>
<link rel="stylesheet" href="assets/css/product.css">
</head>
<body>

<header>
    <h1><?= htmlspecialchars($product['name']) ?></h1>
    <nav>
        <a href="index.php">Каталог</a>
        <a href="cart.php">Корзина</a>
    </nav>
</header>

<div class="product-container">
    <div class="product-images">
        <div class="slider">
            <?php foreach ($images as $img): ?>
                <img src="<?= $img ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            <?php endforeach; ?>
            <button class="prev">←</button>
            <button class="next">→</button>
        </div>
    </div>

    <div class="product-details">
        <p><b>Артикул:</b> <?= htmlspecialchars($product['article']) ?></p>
        <p><b>Цена:</b> <?= $product['price'] ?> руб.</p>
        <p><b>Наличие:</b>
            <span class="<?= $inStock ? 'in-stock' : 'out-of-stock' ?>">
                <?= $inStock ? 'В наличии' : 'Нет в наличии' ?>
            </span>
        </p>
        <p><b>Остаток:</b> <?= $product['stock'] ?> <?= htmlspecialchars($product['unit']) ?></p>

        <button <?= !$inStock ? 'disabled' : '' ?>
            id="addBtn-<?= $product['id'] ?>"
            onclick="addToCartCheckLogin(
                <?= $product['id'] ?>,
                '<?= addslashes($product['name']) ?>',
                <?= $product['price'] ?>,
                <?= $isLoggedIn ? 'true' : 'false' ?>,
                '<?= $images[0] ?>',
                '<?= addslashes($product['article']) ?>'
            )">
            В корзину
        </button>
        <span id="count-<?= $product['id'] ?>" class="cart-count"></span>

        <h3>Описание</h3>
        <p><?= nl2br(htmlspecialchars($product['full_desc'])) ?></p>

        <?php if ($specs): ?>
        <h3>Характеристики</h3>
        <table class="specs-table">
            <?php foreach ($specs as $key => $value): ?>
            <tr>
                <td><?= htmlspecialchars($key) ?></td>
                <td><?= htmlspecialchars($value) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>

<script src="assets/js/cart.js"></script>
<script>
const track = document.querySelector('.slider');
const slides = track.querySelectorAll('img');
let index = 0;

const prevBtn = track.querySelector('.prev');
const nextBtn = track.querySelector('.next');

function showSlide(i) {
    slides.forEach((s, idx) => s.style.display = idx === i ? 'block' : 'none');
}
showSlide(index);

nextBtn.onclick = () => { index = (index + 1) % slides.length; showSlide(index); };
prevBtn.onclick = () => { index = (index - 1 + slides.length) % slides.length; showSlide(index); };

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

// обновляем счетчик при загрузке
document.addEventListener('DOMContentLoaded', () => {
    const cart = getCart();
    cart.forEach(item => updateCartCount(item.id));
});
</script>
</body>
</html>
