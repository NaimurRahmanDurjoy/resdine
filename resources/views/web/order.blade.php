<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RestoNest | Order</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans">

    <!-- Navbar -->
    <nav class="bg-black text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <a href="index.html" class="text-xl font-bold">🍴 RestoNest</a>
            <a href="index" class="hover:text-yellow-400">Home</a>
        </div>
    </nav>

    <!-- Menu Categories -->
    <div class="mb-1 flex space-x-1 overflow-y-auto">
        <button onclick="showCategory('all')" class="px-4 py-2 bg-yellow-400 text-black rounded hover:bg-yellow-500">All</button>
        <button onclick="showCategory('appetizer')" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Appetizers</button>
        <button onclick="showCategory('main')" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Main Course</button>
        <button onclick="showCategory('dessert')" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Desserts</button>
        <button onclick="showCategory('drinks')" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Drinks</button>
    </div>

    <!-- POS Layout -->
    <div class="container mx-auto py-10 grid md:grid-cols-3 gap-8 max-h-screen   overflow-y-auto">




        <!-- Menu Section -->
        <div class="md:col-span-2 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- Example Menu Item -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <img src="https://source.unsplash.com/400x300/?pizza" class="w-full h-40 object-cover">
                <div class="p-4 text-center">
                    <h3 class="text-lg font-semibold">Classic Pizza</h3>
                    <p class="text-gray-600 mt-1">$12.99</p>
                    <div class="flex justify-center gap-2 mt-2">
                        <button onclick="changeQty('Classic Pizza', -1, 12.99)" class="bg-gray-200 px-3 py-1 rounded">-</button>
                        <span id="qty-Classic Pizza">0</span>
                        <button onclick="changeQty('Classic Pizza', 1, 12.99)" class="bg-yellow-400 px-3 py-1 rounded hover:bg-yellow-500">+</button>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <img src="https://source.unsplash.com/400x300/?burger" class="w-full h-40 object-cover">
                <div class="p-4 text-center">
                    <h3 class="text-lg font-semibold">Cheese Burger</h3>
                    <p class="text-gray-600 mt-1">$9.99</p>
                    <div class="flex justify-center gap-2 mt-2">
                        <button onclick="changeQty('Cheese Burger', -1, 9.99)" class="bg-gray-200 px-3 py-1 rounded">-</button>
                        <span id="qty-Cheese Burger">0</span>
                        <button onclick="changeQty('Cheese Burger', 1, 9.99)" class="bg-yellow-400 px-3 py-1 rounded hover:bg-yellow-500">+</button>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <img src="https://source.unsplash.com/400x300/?pasta" class="w-full h-40 object-cover">
                <div class="p-4 text-center">
                    <h3 class="text-lg font-semibold">Italian Pasta</h3>
                    <p class="text-gray-600 mt-1">$11.49</p>
                    <div class="flex justify-center gap-2 mt-2">
                        <button onclick="changeQty('Italian Pasta', -1, 11.49)" class="bg-gray-200 px-3 py-1 rounded">-</button>
                        <span id="qty-Italian Pasta">0</span>
                        <button onclick="changeQty('Italian Pasta', 1, 11.49)" class="bg-yellow-400 px-3 py-1 rounded hover:bg-yellow-500">+</button>
                    </div>
                </div>
            </div>
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <img src="https://source.unsplash.com/400x300/?burger" class="w-full h-40 object-cover">
                <div class="p-4 text-center">
                    <h3 class="text-lg font-semibold">Cheese Burger</h3>
                    <p class="text-gray-600 mt-1">$9.99</p>
                    <div class="flex justify-center gap-2 mt-2">
                        <button onclick="changeQty('Cheese Burger', -1, 9.99)" class="bg-gray-200 px-3 py-1 rounded">-</button>
                        <span id="qty-Cheese Burger">0</span>
                        <button onclick="changeQty('Cheese Burger', 1, 9.99)" class="bg-yellow-400 px-3 py-1 rounded hover:bg-yellow-500">+</button>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <img src="https://source.unsplash.com/400x300/?pasta" class="w-full h-40 object-cover">
                <div class="p-4 text-center">
                    <h3 class="text-lg font-semibold">Italian Pasta</h3>
                    <p class="text-gray-600 mt-1">$11.49</p>
                    <div class="flex justify-center gap-2 mt-2">
                        <button onclick="changeQty('Italian Pasta', -1, 11.49)" class="bg-gray-200 px-3 py-1 rounded">-</button>
                        <span id="qty-Italian Pasta">0</span>
                        <button onclick="changeQty('Italian Pasta', 1, 11.49)" class="bg-yellow-400 px-3 py-1 rounded hover:bg-yellow-500">+</button>
                    </div>
                </div>
            </div>
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <img src="https://source.unsplash.com/400x300/?burger" class="w-full h-40 object-cover">
                <div class="p-4 text-center">
                    <h3 class="text-lg font-semibold">Cheese Burger</h3>
                    <p class="text-gray-600 mt-1">$9.99</p>
                    <div class="flex justify-center gap-2 mt-2">
                        <button onclick="changeQty('Cheese Burger', -1, 9.99)" class="bg-gray-200 px-3 py-1 rounded">-</button>
                        <span id="qty-Cheese Burger">0</span>
                        <button onclick="changeQty('Cheese Burger', 1, 9.99)" class="bg-yellow-400 px-3 py-1 rounded hover:bg-yellow-500">+</button>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <img src="https://source.unsplash.com/400x300/?pasta" class="w-full h-40 object-cover">
                <div class="p-4 text-center">
                    <h3 class="text-lg font-semibold">Italian Pasta</h3>
                    <p class="text-gray-600 mt-1">$11.49</p>
                    <div class="flex justify-center gap-2 mt-2">
                        <button onclick="changeQty('Italian Pasta', -1, 11.49)" class="bg-gray-200 px-3 py-1 rounded">-</button>
                        <span id="qty-Italian Pasta">0</span>
                        <button onclick="changeQty('Italian Pasta', 1, 11.49)" class="bg-yellow-400 px-3 py-1 rounded hover:bg-yellow-500">+</button>
                    </div>
                </div>
            </div>
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <img src="https://source.unsplash.com/400x300/?burger" class="w-full h-40 object-cover">
                <div class="p-4 text-center">
                    <h3 class="text-lg font-semibold">Cheese Burger</h3>
                    <p class="text-gray-600 mt-1">$9.99</p>
                    <div class="flex justify-center gap-2 mt-2">
                        <button onclick="changeQty('Cheese Burger', -1, 9.99)" class="bg-gray-200 px-3 py-1 rounded">-</button>
                        <span id="qty-Cheese Burger">0</span>
                        <button onclick="changeQty('Cheese Burger', 1, 9.99)" class="bg-yellow-400 px-3 py-1 rounded hover:bg-yellow-500">+</button>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <img src="https://source.unsplash.com/400x300/?pasta" class="w-full h-40 object-cover">
                <div class="p-4 text-center">
                    <h3 class="text-lg font-semibold">Italian Pasta</h3>
                    <p class="text-gray-600 mt-1">$11.49</p>
                    <div class="flex justify-center gap-2 mt-2">
                        <button onclick="changeQty('Italian Pasta', -1, 11.49)" class="bg-gray-200 px-3 py-1 rounded">-</button>
                        <span id="qty-Italian Pasta">0</span>
                        <button onclick="changeQty('Italian Pasta', 1, 11.49)" class="bg-yellow-400 px-3 py-1 rounded hover:bg-yellow-500">+</button>
                    </div>
                </div>
            </div>

        </div>

        <!-- Cart + Table + Membership -->
        <div class="bg-white shadow rounded-lg p-6 flex flex-col">
            <h2 class="text-xl font-bold mb-4">🛒 Your Cart</h2>

            <!-- Table Selection -->
            <div class="mb-4">
                <label class="block font-semibold mb-2">Select Table:</label>
                <select id="table-select" class="w-full border rounded px-3 py-2">
                    <option value="">-- Select Table --</option>
                    <option value="T1">Table 1</option>
                    <option value="T2">Table 2</option>
                    <option value="T3">Table 3</option>
                    <option value="T4">Table 4</option>
                </select>
            </div>

            <!-- Membership Selection -->
            <div class="mb-4">
                <label class="block font-semibold mb-2">Membership:</label>
                <select id="membership-select" class="w-full border rounded px-3 py-2">
                    <option value="none">None</option>
                    <option value="silver">Silver - 5% off</option>
                    <option value="gold">Gold - 10% off</option>
                </select>
            </div>

            <!-- Cart Items -->
            <ul id="cart-items" class="space-y-3 flex-1 overflow-auto">
                <li class="text-gray-500">Cart is empty</li>
            </ul>

            <hr class="my-4">
            <div class="flex justify-between font-bold text-lg">
                <span>Total</span>
                <span id="cart-total">$0.00</span>
            </div>
            <button onclick="checkout()" class="mt-5 w-full bg-green-500 text-white py-2 rounded hover:bg-green-600">Checkout</button>
        </div>

    </div>

    <script>
        let cart = {};

        function changeQty(name, delta, price) {
            if (!cart[name]) cart[name] = {
                qty: 0,
                price: price
            };
            cart[name].qty += delta;
            if (cart[name].qty <= 0) delete cart[name];
            document.getElementById(`qty-${name}`).textContent = cart[name] ? cart[name].qty : 0;
            renderCart();
        }

        function renderCart() {
            let cartItems = document.getElementById('cart-items');
            cartItems.innerHTML = '';
            let total = 0;
            let empty = true;

            for (let item in cart) {
                if (cart[item].qty > 0) {
                    empty = false;
                    let itemTotal = cart[item].qty * cart[item].price;
                    total += itemTotal;
                    cartItems.innerHTML += `
          <li class="flex justify-between">
            <span>${item} (x${cart[item].qty})</span>
            <span>$${itemTotal.toFixed(2)}</span>
          </li>
        `;
                }
            }

            if (empty) cartItems.innerHTML = '<li class="text-gray-500">Cart is empty</li>';

            document.getElementById('cart-total').textContent = `$${total.toFixed(2)}`;
        }

        function checkout() {
            const table = document.getElementById('table-select').value;
            const membership = document.getElementById('membership-select').value;

            if (!table) {
                alert("Please select a table.");
                return;
            }
            if (Object.keys(cart).length === 0) {
                alert("Cart is empty.");
                return;
            }

            let total = 0;
            for (let item in cart) total += cart[item].qty * cart[item].price;

            if (membership === 'silver') total *= 0.95;
            if (membership === 'gold') total *= 0.90;

            alert(`Order Confirmed!\nTable: ${table}\nMembership: ${membership}\nTotal: $${total.toFixed(2)}`);

            // Reset cart
            cart = {};
            renderCart();
            for (let item of document.querySelectorAll('[id^="qty-"]')) item.textContent = 0;
            document.getElementById('table-select').value = '';
            document.getElementById('membership-select').value = 'none';
        }
    </script>

</body>

</html>