<?php
session_start();

// 1. Security Check - matches your admin_login.php session variable
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

require_once 'db.php';

// 2. Handle Status Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    
    $update_stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $update_stmt->execute([$new_status, $order_id]);
    header("Location: admin_dashboard.php");
    exit();
}

// 3. Fetch Data - We use 'customer_name' and 'order_date' to match your SQL
try {
    $query = "SELECT * FROM orders ORDER BY id DESC";
    $stmt = $pdo->query($query);
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <title>Active Orders | Admin</title>
</head>
<body class="bg-[#f8fafc] flex min-h-screen">

    <main class="flex-grow p-12 font-['Plus_Jakarta_Sans']">
        <div class="flex justify-between items-center mb-10">
            <h1 class="text-4xl font-black text-slate-900 italic">Active <span class="text-green-600">Orders</span></h1>
            <a href="logout.php" class="text-red-500 font-bold hover:underline">Logout</a>
        </div>
        
        <div class="bg-white rounded-[3rem] p-10 shadow-sm border border-slate-50">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] border-b border-slate-50">
                        <th class="pb-6">Order ID</th>
                        <th class="pb-6">Customer</th>
                        <th class="pb-6">Amount</th>
                        <th class="pb-6">Current Status</th>
                        <th class="pb-6">Change Status</th>
                        <th class="pb-6 text-right">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php foreach($orders as $o): ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-6 font-bold text-slate-400">#<?php echo $o['id']; ?></td>
                        
                        <td class="py-6 font-black text-slate-800"><?php echo htmlspecialchars($o['customer_name']); ?></td>
                        
                        <td class="py-6 font-black text-green-600">$<?php echo number_format($o['total_amount'], 2); ?></td>
                        
                        <td class="py-6">
                            <?php 
                                $status = $o['status'] ?? 'Processing';
                                $color = ($status == 'Delivered') ? 'text-green-500 bg-green-50' : 'text-orange-500 bg-orange-50';
                            ?>
                            <span class="px-3 py-1 <?php echo $color; ?> text-[10px] font-black rounded-lg uppercase tracking-widest">
                                <?php echo $status; ?>
                            </span>
                        </td>

                        <td class="py-6">
                            <form method="POST" class="flex gap-2">
                                <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                <select name="status" class="text-xs font-bold border rounded-lg p-1">
                                    <option value="Processing">Processing</option>
                                    <option value="Shipped">Shipped</option>
                                    <option value="Delivered">Delivered</option>
                                </select>
                                <button type="submit" name="update_status" class="bg-slate-900 text-white p-1 rounded">
                                    <i data-lucide="check" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </td>

                        <td class="py-6 text-right text-slate-400 text-sm font-bold">
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