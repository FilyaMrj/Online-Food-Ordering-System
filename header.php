<?php
require_once 'config.php';

$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['qty'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NepBites | Online Food Ordering System</title>
    <link rel="stylesheet" href="style.css">
    <!-- FontAwesome icons via CDN for simple clean icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">
                <i class="fa-solid => fa-utensils"></i> Nep<span>Bites</span>
            </a>
            
            <ul class="nav-menu">
                <li><a href="index.php" class="nav-link"><i class="fa-solid fa-house"></i> Home</a></li>
                <li><a href="menu.php" class="nav-link"><i class="fa-solid fa-book-open"></i> Food Menu</a></li>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'customer'): ?>
                        <li><a href="my-orders.php" class="nav-link"><i class="fa-solid fa-clock-rotate-left"></i> My Orders</a></li>
                    <?php endif; ?>
                <?php endif; ?>

                <li>
                    <a href="cart.php" class="nav-link">
                        <i class="fa-solid fa-cart-shopping"></i> Cart
                        <span class="cart-badge"><?php echo $cart_count; ?></span>
                    </a>
                </li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                        <li><a href="admin/index.php" class="nav-link" style="color: var(--primary-color); font-weight:700;"><i class="fa-solid fa-user-shield"></i> Admin Panel</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php" class="nav-link" style="color: #ef4444;"><i class="fa-solid fa-right-from-bracket"></i> Logout (<?php echo htmlspecialchars($_SESSION['user_name']); ?>)</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="nav-link btn-nav-login"><i class="fa-solid fa-user"></i> Login / Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
