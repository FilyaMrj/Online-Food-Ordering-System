<?php
require_once '../config.php';

$current_page = basename($_SERVER['PHP_SELF']);
if ($current_page !== 'login.php') {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        $_SESSION['msg_error'] = "Admin access required! Please login with admin credentials.";
        header("Location: login.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | NepBites Food System</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <nav class="navbar" style="border-bottom: 2px solid var(--primary-color);">
        <div class="nav-container" style="max-width: 100%; padding: 0.8rem 2rem;">
            <a href="index.php" class="logo">
                <i class="fa-solid fa-utensils"></i> Nep<span>Bites Admin</span>
            </a>
            <div style="display: flex; align-items: center; gap: 1.5rem;">
                <a href="../index.php" target="_blank" style="color: var(--text-muted); font-weight:600; font-size: 0.9rem;">
                    <i class="fa-solid fa-globe"></i> View Front Website <i class="fa-solid fa-arrow-up-right-from-square"></i>
                </a>
                <span style="font-weight: 700; color: var(--secondary-color);">
                    <i class="fa-solid fa-user-gear"></i> <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?>
                </span>
                <a href="logout.php" class="btn-danger" style="text-decoration: none; padding: 6px 14px;">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <ul class="admin-menu">
                <li>
                    <a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                        <i class="fa-solid fa-chart-line"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="manage-orders.php" class="<?php echo ($current_page == 'manage-orders.php') ? 'active' : ''; ?>">
                        <i class="fa-solid fa-receipt"></i> Customer Orders
                    </a>
                </li>
                <li>
                    <a href="manage-food.php" class="<?php echo ($current_page == 'manage-food.php') ? 'active' : ''; ?>">
                        <i class="fa-solid fa-bowl-food"></i> Food Items
                    </a>
                </li>
                <li>
                    <a href="manage-categories.php" class="<?php echo ($current_page == 'manage-categories.php') ? 'active' : ''; ?>">
                        <i class="fa-solid fa-list-check"></i> Categories
                    </a>
                </li>
            </ul>
        </aside>

        <main class="admin-content">
