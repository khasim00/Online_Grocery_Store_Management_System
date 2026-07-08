<?php
session_start();
require_once 'db.php';

// 1. Check if the cart is empty or user is not logged in
if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_SESSION['user_id'])) {
    // Redirect to login if they try to checkout without an account
    header("Location: login.php");
    exit();
}

// 2. Calculate Total
$total_amount = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_amount += ($item['price'] * $item['qty']);
}

// 3. Prepare Data from Session
$user_id = $_SESSION['user_id'];
$customer_name = $_SESSION['user_name'] ?? "Registered Customer"; 
$status = "Processing";

try {
    // 4. Insert Order (Make sure these column names match your DB exactly)
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, customer_name, total_amount, status) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $customer_name, $total_amount, $status]);
    
    // 5. Clear the cart
    unset($_SESSION['cart']);

} catch (PDOException $e) {
    // Instead of a white screen, this will show the error
    die("Checkout Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmed | FreshMarket</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#f8fafc] h-screen flex items-center justify-center">

    <div class="max-w-md w-full p-8 text-center">
        <div class="bg-white p-12 rounded-[3.5rem] shadow-2xl shadow-green-900/10 border border-slate-50 flex flex-col items-center">
            
            <div class="w-24 h-24 bg-green-50 rounded-full flex items-center justify-center mb-8">
                <i data-lucide="check-circle" class="text-green-600 w-12 h-12"></i>
            </div>
            
            <h1 class="text-3xl font-black text-[#1a4d2e] mb-4 tracking-tight">Success!</h1>
            <p class="text-slate-400 font-medium mb-12 leading-relaxed">
                Your order has been placed successfully. You can track it in your profile.
            </p>
            
            <div class="w-full space-y-4">
                <a href="my_orders.php" class="block w-full py-5 bg-[#1a4d2e] text-white font-black rounded-[2rem] shadow-xl shadow-green-900/20 hover:scale-[1.02] transition-all uppercase tracking-widest text-sm">
                    View My Orders
                </a>
                <a href="index.php" class="block w-full py-5 bg-white text-slate-400 font-black rounded-[2rem] border border-slate-100 hover:bg-slate-50 transition-all uppercase tracking-widest text-sm">
                    Continue Shopping
                </a>
            </div>
        </div>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>