<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $password]);
        header("Location: login.php?msg=registered");
    } catch (PDOException $e) {
        $error = "Email already exists.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Account</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-3xl shadow-xl w-full max-w-md border border-slate-100">
        <h2 class="text-2xl font-black text-[#1a4d2e] mb-6">Create Account</h2>
        <form method="POST" class="space-y-4">
            <input type="text" name="full_name" placeholder="Full Name" class="w-full p-4 rounded-xl border border-slate-200" required>
            <input type="email" name="email" placeholder="Email Address" class="w-full p-4 rounded-xl border border-slate-200" required>
            <input type="password" name="password" placeholder="Password" class="w-full p-4 rounded-xl border border-slate-200" required>
            <button type="submit" class="w-full py-4 bg-[#1a4d2e] text-white font-bold rounded-xl hover:bg-green-800 transition-all">Sign Up</button>
        </form>
    </div>
</body>
</html>