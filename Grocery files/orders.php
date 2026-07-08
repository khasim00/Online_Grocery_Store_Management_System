<?php
session_start();

// 1. Security Check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

require_once 'db.php';

// 2. Handle Status Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    
    try {
        $update_stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $update_stmt->execute([$new_status, $order_id]);
        header("Location: orders.php");
        exit();
    } catch (PDOException $e) {
        $error = "Update failed: " . $e->getMessage();
    }
}

// 3. Fetch Live Orders
try {
    $stmt = $pdo->query("SELECT * FROM orders ORDER BY id DESC");
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <title>Active Orders | Admin</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#f8fafc] flex min-h-screen">

    <aside class="w-64 bg-white border-r border-slate-100 p-8 hidden lg:block">
        <div class="mb-10 flex items-center gap-2 text-green-600">
            <i data-lucide="shopping-basket" class="w-8 h-8"></i>
            <span class="text-xl font-black italic tracking-tighter">FreshMarket</span>
        </div>
        <nav class="space-y-4">
            <a href="admin.php" class="flex items-center gap-3 text-slate-400 font-bold hover:text-green-600 p-2">
                <i data-lucide="package" class="w-5 h-5"></i> Products
            </a>
            <a href="orders.php" class="flex items-center gap-3 text-green-600 font-bold bg-green-50 p-3 rounded-2xl">
                <i data-lucide="truck" class="w-5 h-5"></i> Orders
            </a>
            <a href="logout.php" class="flex items-center gap-3 text-red-400 font-bold p-2 mt-10">
                <i data-lucide="log-out" class="w-5 h-5"></i> Logout
            </a>
        </nav>
    </aside>

    <main class="flex-grow p-12">
        <h1 class="text-4xl font-black text-slate-900 mb-10 italic">Active <span class="text-green-600">Orders</span></h1>
        
        <div class="bg-white rounded-[3.5rem] p-10 shadow-2xl shadow-slate-200/50 border border-slate-50">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] border-b border-slate-50">
                        <th class="pb-6 px-4">Order ID</th>
                        <th class="pb-6 px-4">Customer</th>
                        <th class="pb-6 px-4">Amount</th>
                        <th class="pb-6 px-4">Status</th>
                        <th class="pb-6 px-4">Action</th>
                        <th class="pb-6 px-4 text-right">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (empty($orders)): ?>
                        <tr><td colspan="6" class="py-10 text-center text-slate-300 italic">No orders found.</td></tr>
                    <?php endif; ?>

                    <?php foreach($orders as $o): ?>
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="py-6 px-4 font-bold text-slate-400">#<?php echo $o['id']; ?></td>
                        <td class="py-6 px-4 font-black text-slate-800"><?php echo htmlspecialchars($o['customer_name']); ?></td>
                        <td class="py-6 px-4 font-black text-green-600 italic">$<?php echo number_format($o['total_amount'], 2); ?></td>
                        <td class="py-6 px-4">
                            <?php 
                                $status = $o['status'] ?? 'Processing';
                                $badgeColor = ($status == 'Delivered') ? 'bg-green-100 text-green-700' : 'bg-orange-50 text-orange-500';
                            ?>
                            <span class="px-3 py-1 <?php echo $badgeColor; ?> text-[10px] font-black rounded-lg uppercase tracking-widest">
                                <?php echo $status; ?>
                            </span>
                        </td>
                        <td class="py-6 px-4">
                            <form method="POST" class="flex items-center gap-2">
                                <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                <select name="status" class="bg-slate-50 border border-slate-100 rounded-xl px-2 py-1 text-[10px] font-bold text-slate-600 outline-none focus:ring-2 focus:ring-green-400">
                                    <option value="Processing" <?php if($status == 'Processing') echo 'selected'; ?>>Processing</option>
                                    <option value="Shipped" <?php if($status == 'Shipped') echo 'selected'; ?>>Shipped</option>
                                    <option value="Delivered" <?php if($status == 'Delivered') echo 'selected'; ?>>Delivered</option>
                                </select>
                                <button type="submit" name="update_status" class="p-2 bg-slate-900 text-white rounded-lg hover:bg-green-600 transition-colors">
                                    <i data-lucide="check" class="w-3 h-3"></i>
                                </button>
                            </form>
                        </td>
                        <td class="py-6 px-4 text-right text-slate-400 text-sm font-bold">
                            <?php echo date('M d, Y', strtotime($o['order_date'])); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
    <script>lucide.createIcons();</script>
</body>
</html>