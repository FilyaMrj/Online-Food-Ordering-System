<?php
require_once 'header.php';


$res_orders = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders");
$total_orders = mysqli_fetch_assoc($res_orders)['total'] ?? 0;

$res_rev = mysqli_query($conn, "SELECT SUM(total_amount) as total FROM orders WHERE status != 'Cancelled'");
$total_revenue = mysqli_fetch_assoc($res_rev)['total'] ?? 0;

$res_food = mysqli_query($conn, "SELECT COUNT(*) as total FROM food_items");
$total_food = mysqli_fetch_assoc($res_food)['total'] ?? 0;

$res_users = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='customer'");
$total_users = mysqli_fetch_assoc($res_users)['total'] ?? 0;
?>

<div class="section-title">
    <span><i class="fa-solid fa-gauge-high"></i> Dashboard Overview</span>
</div>

<div class="stats-grid">
    <div class="stat-card" style="border-left-color: #3b82f6;">
        <h4>TOTAL ORDERS</h4>
        <div class="number"><?php echo $total_orders; ?></div>
    </div>
    
    <div class="stat-card" style="border-left-color: #10b981;">
        <h4>TOTAL REVENUE</h4>
        <div class="number">Rs. <?php echo number_format($total_revenue, 2); ?></div>
    </div>
    
    <div class="stat-card" style="border-left-color: #f59e0b;">
        <h4>FOOD ITEMS</h4>
        <div class="number"><?php echo $total_food; ?></div>
    </div>
    
    <div class="stat-card" style="border-left-color: #8b5cf6;">
        <h4>CUSTOMERS</h4>
        <div class="number"><?php echo $total_users; ?></div>
    </div>
</div>

<div style="background: white; border-radius: var(--radius); padding: 1.5rem; box-shadow: var(--shadow);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
        <h3 style="color: var(--secondary-color);"><i class="fa-solid fa-receipt"></i> Recent Orders</h3>
        <a href="manage-orders.php" class="btn-primary" style="font-size: 0.85rem; padding: 6px 12px;">View All Orders &rarr;</a>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Phone</th>
                <th>Amount</th>
                <th>Payment</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql_recent = "SELECT * FROM orders ORDER BY id DESC LIMIT 5";
            $res_recent = mysqli_query($conn, $sql_recent);

            if ($res_recent && mysqli_num_rows($res_recent) > 0) {
                while ($ord = mysqli_fetch_assoc($res_recent)) {
                    $st = $ord['status'];
                    $b_class = 'badge-pending';
                    if ($st === 'Preparing') $b_class = 'badge-preparing';
                    if ($st === 'Out for Delivery') $b_class = 'badge-delivering';
                    if ($st === 'Delivered') $b_class = 'badge-delivered';
                    if ($st === 'Cancelled') $b_class = 'badge-cancelled';
                    ?>
                    <tr>
                        <td><strong>#<?php echo sprintf('%04d', $ord['id']); ?></strong></td>
                        <td><?php echo htmlspecialchars($ord['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($ord['customer_phone']); ?></td>
                        <td style="font-weight: 700; color: var(--primary-color);">Rs. <?php echo number_format($ord['total_amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($ord['payment_method']); ?></td>
                        <td><span class="badge <?php echo $b_class; ?>"><?php echo $st; ?></span></td>
                        <td><?php echo date('M d, h:i A', strtotime($ord['order_date'])); ?></td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='7'>No recent orders found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php require_once 'footer.php'; ?>
