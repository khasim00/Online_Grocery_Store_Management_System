<?php
session_start();
require_once 'db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    try {
        // Query to match your admins table
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? AND password = ?");
        $stmt->execute([$username, $password]);
        $admin = $stmt->fetch();

        if ($admin) {
            // THIS IS THE FIX: Set the exact variable admin.php is looking for
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_user'] = $admin['username'];
            
            header("Location: admin.php");
            exit();
        } else {
            $error = "Invalid username or password. Please try again.";
        }
    } catch (PDOException $e) {
        $error = "Database Connection Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login | FreshMarket</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#f8fafc] min-h-screen flex items-center justify-center p-6">

    <div class="max-w-md w-full">
        <div class="bg-white p-10 rounded-[3rem] shadow-2xl shadow-green-900/10 border border-slate-50">
            <div class="flex flex-col items-center mb-10">
                <div class="w-16 h-16 bg-green-50 rounded-2xl flex items-center justify-center text-green-600 mb-4">
                    <i data-lucide="shield-check" class="w-8 h-8"></i>
                </div>
                <h1 class="text-2xl font-black text-slate-900 italic">Admin <span class="text-green-600">Portal</span></h1>
            </div>

            <?php if($error): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-500 text-[10px] font-black uppercase tracking-widest rounded-2xl text-center">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div class="relative">
                    <i data-lucide="user" class="absolute left-5 top-5 text-slate-300 w-5 h-5"></i>
                    <input type="text" name="username" placeholder="Username" required 
                           class="w-full pl-14 p-5 rounded-2xl bg-slate-50 border border-slate-100 outline-none focus:ring-4 focus:ring-green-500/10 transition-all font-bold text-slate-700">
                </div>
                
                <div class="relative">
                    <i data-lucide="lock" class="absolute left-5 top-5 text-slate-300 w-5 h-5"></i>
                    <input type="password" name="password" placeholder="Password" required 
                           class="w-full pl-14 p-5 rounded-2xl bg-slate-50 border border-slate-100 outline-none focus:ring-4 focus:ring-green-500/10 transition-all font-bold text-slate-700">
                </div>

                <button type="submit" class="w-full py-5 bg-[#1a4d2e] text-white font-black rounded-2xl hover:bg-green-800 transition-all shadow-xl shadow-green-900/20 uppercase tracking-widest text-sm">
                    Login to Dashboard
                </button>
            </form>
        </div>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>