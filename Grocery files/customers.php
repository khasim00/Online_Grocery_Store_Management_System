<?php
session_start();

// 1. Security: Block unauthorized access
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

require_once 'db.php';

// 2. Fetch Unique Customers from the Orders Table
// In a simple system, we can group orders by customer name to see our "Customer Base"
try {
    $stmt = $pdo->query("SELECT customer_name, COUNT(id) as total_orders, SUM(total_amount) as total_spent 
                         FROM orders 
                         GROUP BY customer_name 
                         ORDER BY total_spent DESC");
    $customers = $stmt->fetchAll();
} catch (PDOException $e) {
    $customers = []; 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Directory | Grocery Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#f8fafc] flex min-h-screen">

    <aside class="w-72 bg-white border-r border-slate-100 flex flex-col p-8 sticky top-0 h-screen">
        <div class="flex items-center gap-3 mb-12">
            <div class="w-10 h-10 bg-[#1a4d2e] rounded-xl flex items-center justify-center">
                <i data-lucide="shopping-basket" class="text-white w-6 h-6"></i>
            </div>
            <span class="text-xl font-black text-[#1a4d2e] tracking-tighter italic">Grocery<span class="text-green-500">Store</span></span>
        </div>
        
        <nav class="space-y-2 flex-grow">
            <a href="admin.php" class="flex items-center gap-4 p-4 text-slate-400 font-bold hover:text-[#1a4d2e] hover:bg-slate-50 rounded-2xl transition-all">
                <i data-lucide="layout-dashboard"></i> Dashboard
            </a>
            <a href="orders.php" class="flex items-center gap-4 p-4 text-slate-400 font-bold hover:text-[#1a4d2e] hover:bg-slate-50 rounded-2xl transition-all">
                <i data-lucide="package"></i> Active Orders
            </a>
            <a href="customers.php" class="flex items-center gap-4 p-4 bg-green-50 text-[#1a4d2e] rounded-2xl font-black shadow-sm shadow-green-900/5">
                <i data-lucide="users"></i> Customers
            </a>
        </nav>

        <div class="pt-6 border-t border-slate-50">
            <a href="logout.php" class="flex items-center gap-4 p-4 text-red-400 font-bold hover:bg-red-50 rounded-2xl transition-all">
                <i data-lucide="log-out"></i> Logout
            </a>
        </div>
    </aside>

    <main class="flex-grow p-12">
        <header class="mb-12 flex justify-between items-end">
            <div>
                <h1 class="text-4xl font-black text-slate-900 tracking-tight italic">Customer <span class="text-green-600 not-italic">Community</span></h1>
                <p class="text-slate-400 font-bold mt-1 uppercase text-[10px] tracking-[0.2em]">Manage your relationship with shoppers</p>
            </div>
            <div class="bg-white px-6 py-3 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-3">
                <i data-lucide="search" class="text-slate-300 w-4 h-4"></i>
                <input type="text" placeholder="Search customers..." class="bg-transparent outline-none text-sm font-bold text-slate-600">
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if(empty($customers)): ?>
                <div class="bg-white p-8 rounded-[3rem] border border-slate-50 shadow-sm">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-14 h-14 bg-green-50 text-green-600 rounded-full flex items-center justify-center font-black text-xl italic">S</div>
                        <div>
                            <h4 class="font-black text-slate-800 tracking-tight">Shashank</h4>
                            <p class="text-[10px] text-slate-300 font-black uppercase tracking-widest italic">Premium Member</p>
                        </div>
                    </div>
                    <div class="flex justify-between items-center pt-6 border-t border-slate-50">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Orders</p>
                            <p class="text-lg font-black text-slate-900 leading-none">12</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Spent</p>
                            <p class="text-xl font-black text-green-600 leading-none italic">$420.50</p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach($customers as $c): ?>
                <div class="bg-white p-8 rounded-[3rem] border border-slate-50 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-14 h-14 bg-slate-50 text-slate-400 rounded-full flex items-center justify-center font-black text-xl italic">
                            <?php echo substr($c['customer_name'], 0, 1); ?>
                        </div>
                        <div>
                            <h4 class="font-black text-slate-800 tracking-tight"><?php echo htmlspecialchars($c['customer_name']); ?></h4>
                            <p class="text-[10px] text-green-500 font-black uppercase tracking-widest italic">Active Shopper</p>
                        </div>
                    </div>
                    <div class="flex justify-between items-center pt-6 border-t border-slate-50">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Orders</p>
                            <p class="text-lg font-black text-slate-900 leading-none"><?php echo $c['total_orders']; ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Spent</p>
                            <p class="text-xl font-black text-green-600 leading-none italic">$<?php echo number_format($c['total_spent'], 2); ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <script>lucide.createIcons();</script>
</body>
</html>