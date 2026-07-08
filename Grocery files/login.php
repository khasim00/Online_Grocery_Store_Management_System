<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-3xl shadow-xl w-full max-w-md border border-slate-100">
        <h2 class="text-2xl font-black text-[#1a4d2e] mb-6">Customer Login</h2>
        <?php if(isset($error)): ?>
            <p class="text-red-500 mb-4 text-sm"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" class="space-y-4">
            <input type="email" name="email" placeholder="Email Address" class="w-full p-4 rounded-xl border border-slate-200" required>
            <input type="password" name="password" placeholder="Password" class="w-full p-4 rounded-xl border border-slate-200" required>
            <button type="submit" class="w-full py-4 bg-[#1a4d2e] text-white font-bold rounded-xl hover:bg-green-800 transition-all">Login</button>
        </form>
        <p class="mt-4 text-center text-slate-500 text-sm">Don't have an account? <a href="register.php" class="text-green-600 font-bold">Register</a></p>
    </div>
</body>
</html>