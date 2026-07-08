<?php
session_start();
// Remove single item
if (isset($_GET['remove'])) {
    $id = $_GET['remove'];
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Cart | Grocery Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#f8fafc] h-screen flex items-center justify-center">

    <div class="max-w-md w-full p-8">
        <?php if (empty($_SESSION['cart'])): ?>
            <div class="bg-white p-12 rounded-[3.5rem] shadow-2xl border border-slate-50 text-center flex flex-col items-center">
                <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mb-8">
                    <i data-lucide="trash-2" class="text-slate-200 w-12 h-12"></i>
                </div>
                <h1 class="text-3xl font-black text-slate-900 mb-4 tracking-tight">Your cart is empty</h1>
                <p class="text-slate-400 font-medium mb-12 leading-relaxed px-6">Looks like you haven't added anything to your cart yet.</p>
                <a href="index.php" class="w-full py-5 bg-[#1a4d2e] text-white font-black rounded-[2rem] hover:bg-green-800 transition-all shadow-xl shadow-green-900/20 uppercase tracking-widest text-sm text-center">
                    Start Shopping
                </a>
            </div>
        <?php else: ?>
            <div class="bg-white p-10 rounded-[3.5rem] shadow-2xl border border-slate-50">
                <div class="flex justify-between items-center mb-10">
                    <h2 class="text-2xl font-black text-[#1a4d2e] italic">Order Summary</h2>
                    <a href="index.php" class="text-xs font-bold text-slate-400 hover:text-green-600 uppercase tracking-widest">Add More</a>
                </div>

                <div class="space-y-6 mb-12 max-h-[40vh] overflow-y-auto pr-2">
                    <?php $total = 0; foreach($_SESSION['cart'] as $id => $item): $total += ($item['price'] * $item['qty']); ?>
                        <div class="flex justify-between items-center group">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-slate-50 rounded-2xl overflow-hidden border border-slate-100">
                                    <img src="<?php echo $item['image']; ?>" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800 text-sm"><?php echo $item['name']; ?></h4>
                                    <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest"><?php echo $item['qty']; ?> x $<?php echo number_format($item['price'], 2); ?></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <p class="font-black text-slate-900 italic">$<?php echo number_format($item['price'] * $item['qty'], 2); ?></p>
                                <a href="?remove=<?php echo $id; ?>" class="text-slate-200 hover:text-red-500 transition-colors"><i data-lucide="x-circle" class="w-5 h-5"></i></a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="border-t-4 border-dotted border-slate-50 pt-8 mb-10 flex justify-between items-center">
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Grand Total</span>
                    <span class="text-4xl font-black text-green-600 italic">$<?php echo number_format($total, 2); ?></span>
                </div>

                <a href="checkout.php" class="block w-full py-5 bg-[#1a4d2e] text-white font-black rounded-[2rem] shadow-xl shadow-green-900/20 hover:scale-[1.02] transition-all text-center uppercase tracking-widest text-sm">
                    Proceed to Checkout
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>