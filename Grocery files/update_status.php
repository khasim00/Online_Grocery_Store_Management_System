<?php
session_start();
require_once 'db.php';

// Security: Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    try {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $order_id]);
        
        // Return to dashboard with a success message
        header("Location: admin_dashboard.php?msg=updated");
        exit();
    } catch (PDOException $e) {
        die("Error updating status: " . $e->getMessage());
    }
} else {
    header("Location: admin_dashboard.php");
    exit();
}