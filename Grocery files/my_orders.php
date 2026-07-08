<?php
session_start();
require_once 'db.php';

// 1. Security Check: Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Assuming your customer login is login.php
    exit();
}

// 2. Fetch only THIS user's orders
try {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching your orders: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Order History | FreshMarket</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#f8fafc] min-h-screen p-6 md:p-12">

    <div class="max-w-5xl mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12 gap-6">
            <div>
                <a href="index.php" class="inline-flex items-center gap-2 text-slate-400 font-bold text-xs uppercase tracking-widest hover:text-green-600 transition-colors mb-4">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Back to Storefront
                </a>
                <h1 class="text-4xl font-black text-[#1a4d2e] italic tracking-tight">Your <span class="text-slate-900">Orders</span></h1>
            </div>
            
            <div class="bg-white p-4 rounded-3xl shadow-sm border border-slate-50 flex items-center gap-4">
                <div class="w-12 h-12 bg-green-50 rounded-2xl flex items-center justify-center text-green-600">
                    <i data-lucide="user"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Customer Account</p>
                    <p class="font-bold text-slate-800"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Valued Member'); ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[3.5rem] shadow-2xl shadow-slate-200/60 border border-slate-50 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-50/50">
                    <tr class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] border-b border-slate-100">
                        <th class="p-8">Reference</th>
                        <th class="p-8">Purchase Date</th>
                        <th class="p-8 text-center">Amount Paid</th>
                        <th class="p-8">Shipment Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="4" class="p-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                        <i data-lucide="shopping-bag" class="text-slate-200 w-10 h-10"></i>
                                    </div>
                                    <p class="text-slate-400 font-bold italic">You haven't placed any orders yet.</p>
                                    <a href="index.php" class="mt-6 text-green-600 font-black uppercase text-[10px] tracking-widest hover:underline">Start Shopping</a>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($orders as $order): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-8">
                                <span class="font-black text-slate-900 block">#ORD-<?php echo $order['id']; ?></span>
                            </td>
                            <td class="p-8">
                                <span class="text-sm font-bold text-slate-500">
                                    <?php echo date('F d, Y', strtotime($order['order_date'])); ?>
                                </span>
                            </td>
                            <td class="p-8 text-center">
                                <span class="text-lg font-black text-green-600 italic">
                                    $<?php echo number_format($order['total_amount'], 2); ?>
                                </span>
                            </td>
                            <td class="p-8">
                                <?php 
                                    $status = $order['status'] ?? 'Processing';
                                    $badgeStyle = "bg-orange-50 text-orange-500";
                                    if($status == 'Delivered') $badgeStyle = "bg-green-100 text-green-700";
                                    if($status == 'Shipped') $badgeStyle = "bg-blue-50 text-blue-600";
                                    if($status == 'Cancelled') $badgeStyle = "bg-red-50 text-red-500";
                                ?>
                                <span class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest <?php echo $badgeStyle; ?>">
                                    <?php echo $status; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <p class="mt-8 text-center text-slate-300 text-[10px] font-bold uppercase tracking-widest">
            If you have questions about an order, please contact our support team.
        </p>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>