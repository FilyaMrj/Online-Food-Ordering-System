<?php
require_once 'header.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['msg_success'] = "Please login to view your order history.";
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];

$sql = "SELECT * FROM orders WHERE user_id = $user_id ORDER BY id DESC";
$res = mysqli_query($conn, $sql);
?>

<div class="container">
    
    <div class="section-title">
        <span><i class="fa-solid fa-clock-rotate-left"></i> My Order History</span>
    </div>

    <?php if ($res && mysqli_num_rows($res) > 0): ?>

        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <?php while ($order = mysqli_fetch_assoc($res)): ?>
                <?php
                $order_id = $order['id'];
                $status = $order['status'];

                
                $badge_class = 'badge-pending';
                if ($status === 'Preparing') $badge_class = 'badge-preparing';
                if ($status === 'Out for Delivery') $badge_class = 'badge-delivering';
                if ($status === 'Delivered') $badge_class = 'badge-delivered';
                if ($status === 'Cancelled') $badge_class = 'badge-cancelled';

                $sql_items = "SELECT * FROM order_items WHERE order_id = $order_id";
                $res_items = mysqli_query($conn, $sql_items);
                ?>

                <div style="background: white; border-radius: var(--radius); padding: 1.5rem; box-shadow: var(--shadow);">
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 12px; margin-bottom: 12px; flex-wrap: wrap; gap: 10px;">
                        <div>
                            <span style="font-weight: 800; font-size: 1.1rem; color: var(--secondary-color);">Order #<?php echo sprintf('%04d', $order_id); ?></span>
                            <span style="font-size: 0.88rem; color: var(--text-muted); margin-left: 12px;">
                                <i class="fa-regular fa-calendar"></i> <?php echo date('M d, Y - h:i A', strtotime($order['order_date'])); ?>
                            </span>
                        </div>
                        <div>
                            <span class="badge <?php echo $badge_class; ?>" style="font-size: 0.9rem; padding: 6px 14px;">
                                Status: <?php echo $status; ?>
                            </span>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem; font-size: 0.95rem;">
                        <div><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></div>
                        <div><strong>Delivery Address:</strong> <?php echo htmlspecialchars($order['delivery_address']); ?></div>
                        <div><strong>Total Amount:</strong> <span style="color: var(--primary-color); font-weight: 800;">Rs. <?php echo number_format($order['total_amount'], 2); ?></span></div>
                    </div>

                    <div style="background: var(--bg-light); border-radius: 8px; padding: 1rem;">
                        <strong style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: var(--secondary-color);">Items Ordered:</strong>
                        <ul style="list-style: none; padding: 0;">
                            <?php while ($item = mysqli_fetch_assoc($res_items)): ?>
                                <li style="display: flex; justify-content: space-between; font-size: 0.9rem; margin-bottom: 4px;">
                                    <span><?php echo htmlspecialchars($item['food_title']); ?> (×<?php echo $item['qty']; ?>)</span>
                                    <span style="font-weight: 600;">Rs. <?php echo number_format($item['total'], 2); ?></span>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>

                </div>
            <?php endwhile; ?>
        </div>

    <?php else: ?>
        <div style="text-align: center; padding: 4rem 2rem; background: white; border-radius: var(--radius); box-shadow: var(--shadow);">
            <i class="fa-solid fa-receipt" style="font-size: 4rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
            <h2>No orders placed yet!</h2>
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">When you place orders, they will appear here along with live delivery updates.</p>
            <a href="menu.php" class="btn-primary"><i class="fa-solid fa-utensils"></i> Explore Menu</a>
        </div>
    <?php endif; ?>

</div>

<?php require_once 'footer.php'; ?>
