<?php
session_start();

// 1. Security: Block unauthorized access
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

require_once 'db.php';

// 2. Fetch Live Data for the Inventory Table
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
    $inventory = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching inventory: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | Grocery Store</title>
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
            <a href="admin.php" class="flex items-center gap-4 p-4 bg-green-50 text-[#1a4d2e] rounded-2xl font-black shadow-sm shadow-green-900/5 transition-all">
                <i data-lucide="layout-dashboard"></i> Dashboard
            </a>
            <a href="orders.php" class="flex items-center gap-4 p-4 text-slate-400 font-bold hover:text-[#1a4d2e] hover:bg-slate-50 rounded-2xl transition-all">
                <i data-lucide="package"></i> Active Orders
            </a>
            <a href="customers.php" class="flex items-center gap-4 p-4 text-slate-400 font-bold hover:text-[#1a4d2e] hover:bg-slate-50 rounded-2xl transition-all">
                <i data-lucide="users"></i> Customers
            </a>
        </nav>

        <div class="pt-6 border-t border-slate-50">
            <a href="logout.php" class="flex items-center gap-4 p-4 text-red-400 font-bold hover:bg-red-50 rounded-2xl transition-all group">
                <i data-lucide="log-out" class="group-hover:translate-x-1 transition-transform"></i> 
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <main class="flex-grow p-12">
        <header class="flex justify-between items-end mb-12">
            <div>
                <h1 class="text-4xl font-black text-slate-900 tracking-tight italic">Store <span class="text-green-600 not-italic">Management</span></h1>
                <p class="text-slate-400 font-bold mt-1 uppercase text-[10px] tracking-[0.2em]">Grocery Store Admin Console</p>
            </div>
            
            <a href="add_product.php" class="bg-[#1a4d2e] text-white px-8 py-4 rounded-2xl font-black flex items-center gap-3 shadow-xl shadow-green-900/20 hover:scale-105 transition-all text-xs uppercase tracking-widest">
                <i data-lucide="plus-circle" class="w-5 h-5"></i> Add Product
            </a>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <div class="bg-white p-8 rounded-[3rem] border border-slate-50 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-4">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Revenue</p>
                    <span class="bg-green-100 text-green-600 px-2 py-0.5 rounded-lg text-[10px] font-bold">+12%</span>
                </div>
                <h2 class="text-4xl font-black text-slate-900 tracking-tighter italic leading-none">$12,345</h2>
            </div>

            <div class="bg-white p-8 rounded-[3rem] border border-slate-50 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-4">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Daily Orders</p>
                    <span class="bg-green-100 text-green-600 px-2 py-0.5 rounded-lg text-[10px] font-bold">+5 new</span>
                </div>
                <h2 class="text-4xl font-black text-slate-900 tracking-tighter italic leading-none">24</h2>
            </div>

            <div class="bg-white p-8 rounded-[3rem] border border-slate-50 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-4">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Clients</p>
                    <span class="bg-green-100 text-green-600 px-2 py-0.5 rounded-lg text-[10px] font-bold">+18%</span>
                </div>
                <h2 class="text-4xl font-black text-slate-900 tracking-tighter italic leading-none">1,203</h2>
            </div>
        </div>

        <div class="bg-white rounded-[3.5rem] border border-slate-50 p-10 shadow-sm">
            <div class="flex justify-between items-center mb-10 px-4">
                <h3 class="text-xl font-black text-[#1a4d2e] italic uppercase tracking-tighter">Inventory List</h3>
                <div class="text-slate-300">
                    <i data-lucide="filter" class="w-5 h-5 cursor-pointer hover:text-slate-500 transition-colors"></i>
                </div>
            </div>
            
            <table class="w-full text-left">
                <thead>
                    <tr class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] border-b border-slate-50">
                        <th class="pb-6 px-4">Preview</th>
                        <th class="pb-6">Product Info</th>
                        <th class="pb-6">Category</th>
                        <th class="pb-6 text-center">Price</th>
                        <th class="pb-6 text-right px-4">Manage</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if(empty($inventory)): ?>
                        <tr>
                            <td colspan="5" class="py-20 text-center text-slate-400 font-bold italic">No products found. Add one to get started!</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($inventory as $item): ?>
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="py-6 px-4">
                                <img src="<?php echo $item['image_url']; ?>" class="w-14 h-14 rounded-2xl object-cover shadow-sm border border-slate-100 group-hover:scale-105 transition-transform" onerror="this.src='https://placehold.co/100x100?text=None'">
                            </td>
                            <td class="py-6">
                                <p class="font-bold text-slate-800"><?php echo htmlspecialchars($item['name']); ?></p>
                                <p class="text-[10px] text-slate-300 font-black uppercase italic tracking-tighter"><?php echo htmlspecialchars($item['unit']); ?></p>
                            </td>
                            <td class="py-6">
                                <span class="bg-slate-100 text-slate-500 text-[10px] font-black px-3 py-1 rounded-lg uppercase tracking-widest group-hover:bg-green-100 group-hover:text-green-700 transition-colors">
                                    <?php echo htmlspecialchars($item['category']); ?>
                                </span>
                            </td>
                            <td class="py-6 text-center font-black text-[#1a4d2e] italic tracking-tight">$<?php echo number_format($item['price'], 2); ?></td>
                            <td class="py-6 text-right px-4">
                                <div class="flex justify-end gap-3">
                                    <button class="w-9 h-9 bg-slate-50 rounded-xl flex items-center justify-center text-slate-300 hover:text-green-600 hover:bg-green-50 transition-all border border-transparent hover:border-green-100">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </button>
                                    <button class="w-9 h-9 bg-slate-50 rounded-xl flex items-center justify-center text-slate-300 hover:text-red-500 hover:bg-red-50 transition-all border border-transparent hover:border-red-100">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>lucide.createIcons();</script>
</body>
</html>