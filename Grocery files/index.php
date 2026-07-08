<?php
session_start();
require_once 'db.php';

// 1. Category Filtering with Trimmed Logic
$category = isset($_GET['cat']) ? trim($_GET['cat']) : 'All Products';

if ($category == 'All Products') {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
} else {
    // Using LIKE to handle potential "&" symbol encoding issues in URLs
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category LIKE ? ORDER BY id DESC");
    $stmt->execute(["%$category%"]);
}
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grocery Store | Organic Goodness</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#f8fafc]">

    <nav class="max-w-7xl mx-auto p-8 flex justify-between items-center">
        <div class="flex items-center gap-2 text-[#1a4d2e]">
            <i data-lucide="shopping-basket" class="w-8 h-8"></i>
            <h2 class="text-2xl font-black italic tracking-tighter">Grocery<span class="text-green-500">Store</span></h2>
        </div>

        <div class="flex gap-6 items-center">
            <a href="admin_login.php" class="font-bold text-slate-400 hover:text-green-600 text-sm transition-colors">Admin Portal</a>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <div class="flex items-center gap-4">
                    <a href="my_orders.php" class="text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-green-600 transition-colors bg-white px-4 py-2 rounded-xl border border-slate-100 shadow-sm">
                        My Orders
                    </a>
                    <a href="logout.php" class="w-10 h-10 bg-slate-50 text-slate-400 rounded-xl flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition-all">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </a>
                </div>
            <?php else: ?>
                <a href="login.php" class="flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-100 rounded-2xl shadow-sm hover:shadow-md transition-all group">
                    <i data-lucide="user" class="w-4 h-4 text-slate-400 group-hover:text-green-600"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-600 group-hover:text-green-600">Login</span>
                </a>
            <?php endif; ?>

            <a href="cart.php" class="relative p-3 bg-[#1a4d2e] text-white rounded-2xl shadow-xl shadow-green-900/20 hover:scale-105 transition-all">
                <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                <?php if(!empty($_SESSION['cart'])): ?>
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-[9px] w-5 h-5 rounded-full flex items-center justify-center font-black border-2 border-[#f8fafc]">
                        <?php echo count($_SESSION['cart']); ?>
                    </span>
                <?php endif; ?>
            </a>
        </div>
    </nav>

    <header class="max-w-7xl mx-auto px-8 grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-20">
        <div>
            <span class="bg-orange-100 text-orange-600 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest mb-6 inline-block">Free Delivery on First Order</span>
            <h1 class="text-7xl font-black text-[#1a4d2e] leading-tight mb-8">Organic Goodness,<br>Delivered Fresh.</h1>
            <p class="text-slate-500 text-lg mb-10 max-w-md font-medium">Shop the freshest local produce, dairy, and bakery items.</p>
        </div>
        <div class="rounded-[4rem] overflow-hidden shadow-2xl h-[500px]">
            <img src="images/hero.jpg" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/800x600?text=Hero+Image'">
        </div>
    </header>

    <section class="max-w-7xl mx-auto px-8 mb-12">
        <h3 class="text-2xl font-black mb-8 italic">Categories</h3>
        <div class="flex flex-wrap gap-4">
            <?php 
            $cats = ['All Products', 'Vegetables', 'Fruits', 'Dairy & Eggs', 'Bakery', 'Pantry'];
            foreach($cats as $c): 
                $active = ($category == $c) ? 'bg-[#1a4d2e] text-white shadow-lg' : 'bg-white text-slate-500 border border-slate-200';
            ?>
                <a href="?cat=<?php echo urlencode($c); ?>" class="px-8 py-3 rounded-full font-bold text-sm transition-all hover:scale-105 <?php echo $active; ?>"><?php echo $c; ?></a>
            <?php endforeach; ?>
        </div>
    </section>

    <main class="max-w-7xl mx-auto px-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 pb-24">
        <?php if (empty($products)): ?>
            <div class="col-span-full py-20 text-center">
                <i data-lucide="package-search" class="w-16 h-16 text-slate-200 mx-auto mb-4"></i>
                <p class="text-slate-400 font-bold italic">No items found in <?php echo htmlspecialchars($category); ?>.</p>
            </div>
        <?php else: ?>
            <?php foreach($products as $p): ?>
                <div class="bg-white p-6 rounded-[3rem] border border-slate-50 hover:shadow-2xl transition-all group overflow-hidden">
                    <div class="rounded-[2rem] overflow-hidden mb-6 h-48">
                        <img src="<?php echo $p['image_url']; ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" onerror="this.src='https://placehold.co/400x400?text=No+Image'">
                    </div>
                    <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest mb-1"><?php echo $p['category']; ?></p>
                    <h4 class="text-xl font-bold text-slate-900 mb-6 tracking-tight"><?php echo $p['name']; ?></h4>
                    <form action="add_to_cart.php" method="POST" class="flex justify-between items-end">
                        <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                        <input type="hidden" name="name" value="<?php echo htmlspecialchars($p['name']); ?>">
                        <input type="hidden" name="price" value="<?php echo $p['price']; ?>">
                        <input type="hidden" name="image" value="<?php echo $p['image_url']; ?>">
                        <input type="hidden" name="add_to_cart" value="1">
                        <div>
                            <p class="text-2xl font-black text-slate-900">$<?php echo number_format($p['price'], 2); ?></p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase italic mt-1">/ <?php echo $p['unit']; ?></p>
                        </div>
                        <button type="submit" class="w-12 h-12 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center hover:bg-[#1a4d2e] hover:text-white transition-all">
                            <i data-lucide="plus"></i>
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>

    <script>lucide.createIcons();</script>
</body>
</html>