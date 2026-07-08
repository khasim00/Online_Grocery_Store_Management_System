<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}
require_once 'db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $unit = $_POST['unit'];

    // --- FILE UPLOAD LOGIC ---
    $target_dir = "images/";
    // Create the images folder if it doesn't exist
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = basename($_FILES["product_image"]["name"]);
    $target_file = $target_dir . time() . "_" . $file_name; // Add timestamp to avoid duplicate names
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if it's a real image
    $check = getimagesize($_FILES["product_image"]["tmp_name"]);
    if($check !== false) {
        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            try {
                // Save the relative path (images/filename.jpg) to the database
                $stmt = $pdo->prepare("INSERT INTO products (name, category, price, unit, image_url) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $category, $price, $unit, $target_file]);
                header("Location: admin.php?status=success");
                exit();
            } catch (PDOException $e) {
                $message = "Database Error: " . $e->getMessage();
            }
        } else {
            $message = "Error: Failed to upload image.";
        }
    } else {
        $message = "Error: File is not an image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload New Product | FreshMarket</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#f8fafc] min-h-screen flex items-center justify-center p-6">

    <div class="max-w-lg w-full">
        <div class="bg-white p-12 rounded-[3.5rem] shadow-2xl border border-slate-50">
            <h2 class="text-3xl font-black text-[#1a4d2e] mb-8 italic">Upload <span class="text-green-500">Product</span></h2>

            <?php if($message): ?>
                <p class="bg-red-50 text-red-500 p-4 rounded-2xl mb-6 font-bold text-xs border border-red-100 italic"><?php echo $message; ?></p>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="space-y-5">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">General Info</label>
                    <input type="text" name="name" placeholder="Product Name" required 
                           class="w-full p-5 rounded-2xl bg-slate-50 border border-slate-100 outline-none focus:ring-4 focus:ring-green-500/10 font-bold">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <input type="number" step="0.01" name="price" placeholder="Price" required 
                           class="w-full p-5 rounded-2xl bg-slate-50 border border-slate-100 outline-none focus:ring-4 focus:ring-green-500/10 font-bold">
                    <input type="text" name="unit" placeholder="Unit (e.g. 1kg)" required 
                           class="w-full p-5 rounded-2xl bg-slate-50 border border-slate-100 outline-none focus:ring-4 focus:ring-green-500/10 font-bold">
                </div>

                <select name="category" class="w-full p-5 rounded-2xl bg-slate-50 border border-slate-100 outline-none focus:ring-4 focus:ring-green-500/10 font-bold text-slate-500">
                    <option>Vegetables</option>
                    <option>Fruits</option>
                    <option>Dairy & Eggs</option>
                    <option>Bakery</option>
                    <option>Pantry</option>
                </select>

                <div class="relative border-2 border-dashed border-slate-200 rounded-3xl p-8 text-center hover:border-green-400 transition-colors cursor-pointer group">
                    <input type="file" name="product_image" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <div class="flex flex-col items-center">
                        <i data-lucide="image-plus" class="text-slate-300 mb-2 group-hover:text-green-500 transition-colors"></i>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Click to upload Image</p>
                    </div>
                </div>

                <button type="submit" class="w-full py-5 bg-[#1a4d2e] text-white font-black rounded-2xl shadow-xl shadow-green-900/20 hover:bg-green-800 transition-all uppercase tracking-widest text-sm">
                    Add to Inventory
                </button>
                
                <a href="admin.php" class="block text-center text-xs font-black text-slate-300 uppercase tracking-widest mt-6">Cancel</a>
            </form>
        </div>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>