// Получаем корзину из localStorage
function getCart() {
    return JSON.parse(localStorage.getItem('cart')) || [];
}

// Сохраняем корзину
function saveCart(cart) {
    localStorage.setItem('cart', JSON.stringify(cart));
}

// Добавление товара с проверкой авторизации
function addToCartCheckLogin(id, name, price, isLoggedIn, image = '', article = '') {
    if (!isLoggedIn) {
        alert('Сначала нужно войти или создать аккаунт');
        window.location.href = 'login.php';
        return;
    }
    addToCart({id, name, price, image, article});
}

// Добавляем товар в корзину
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
    renderCart();
    alert('Товар добавлен в корзину');
}

// Рендер корзины
function renderCart() {
    const cart = getCart();
    const container = document.getElementById('cartList');
    const totalEl = document.getElementById('cartTotal');

    container.innerHTML = '';

    if (cart.length === 0) {
        container.innerHTML = '<p>Корзина пуста</p>';
        totalEl.textContent = '0';
        return;
    }

    let total = 0;

    cart.forEach((item, index) => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;

        const div = document.createElement('div');
        div.className = 'cart-item';
        div.innerHTML = `
            <img src="${item.image || 'assets/images/placeholder.jpg'}" alt="${item.name}">
            <div class="cart-item-info">
                <h4>${item.name}</h4>
                <p>Артикул: ${item.article || '-'}</p>
                <p>Цена: ${item.price} руб.</p>
            </div>
            <div class="cart-item-actions">
                <input type="number" min="1" value="${item.quantity}" onchange="updateQuantity(${index}, this.value)">
                <p>Итого: ${itemTotal} руб.</p>
                <button onclick="removeItem(${index})">Удалить</button>
            </div>
        `;
        container.appendChild(div);
    });

    totalEl.textContent = total;
}

// Обновление количества
function updateQuantity(index, value) {
    const cart = getCart();
    const qty = parseInt(value);
    if (isNaN(qty) || qty < 1) return;
    cart[index].quantity = qty;
    saveCart(cart);
    renderCart();
}

// Удаление товара
function removeItem(index) {
    const cart = getCart();
    cart.splice(index, 1);
    saveCart(cart);
    renderCart();
}

// Подготовка корзины для отправки на сервер
function prepareCartForSubmit() {
    document.getElementById('cartInput').value = JSON.stringify(getCart());
    localStorage.removeItem('cart'); // очищаем корзину после оформления
}

// Инициализация
document.addEventListener('DOMContentLoaded', renderCart);
