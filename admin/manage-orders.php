<?php
require_once 'header.php';

$msg = '';
$msg_type = '';

if (isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = sanitize($_POST['status'], $conn);

    $sql_u = "UPDATE orders SET status = '$new_status' WHERE id = $order_id";
    if (mysqli_query($conn, $sql_u)) {
        $msg = "Order #".sprintf('%04d', $order_id)." status updated to '$new_status'!";
        $msg_type = "success";
    } else {
        $msg = "Failed to update order status: " . mysqli_error($conn);
        $msg_type = "danger";
    }
}

if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    if (mysqli_query($conn, "DELETE FROM orders WHERE id = $del_id")) {
        $msg = "Order deleted successfully!";
        $msg_type = "success";
    }
}

$status_filter = isset($_GET['filter']) ? sanitize($_GET['filter'], $conn) : 'All';
?>

<div class="section-title">
    <span><i class="fa-solid fa-receipt"></i> Manage Customer Orders</span>
</div>

<?php if (!empty($msg)): ?>
    <div class="alert alert-<?php echo $msg_type; ?>">
        <i class="fa-solid fa-circle-info"></i> <?php echo $msg; ?>
    </div>
<?php endif; ?>

<div style="margin-bottom: 1.5rem; display: flex; gap: 8px; flex-wrap: wrap;">
    <a href="manage-orders.php" class="nav-link <?php echo ($status_filter === 'All') ? 'active' : ''; ?>" style="background: white; border: 1px solid var(--border-color);">All Orders</a>
    <a href="manage-orders.php?filter=Pending" class="nav-link <?php echo ($status_filter === 'Pending') ? 'active' : ''; ?>" style="background: white; border: 1px solid var(--border-color);">Pending</a>
    <a href="manage-orders.php?filter=Preparing" class="nav-link <?php echo ($status_filter === 'Preparing') ? 'active' : ''; ?>" style="background: white; border: 1px solid var(--border-color);">Preparing</a>
    <a href="manage-orders.php?filter=Out for Delivery" class="nav-link <?php echo ($status_filter === 'Out for Delivery') ? 'active' : ''; ?>" style="background: white; border: 1px solid var(--border-color);">Out for Delivery</a>
    <a href="manage-orders.php?filter=Delivered" class="nav-link <?php echo ($status_filter === 'Delivered') ? 'active' : ''; ?>" style="background: white; border: 1px solid var(--border-color);">Delivered</a>
    <a href="manage-orders.php?filter=Cancelled" class="nav-link <?php echo ($status_filter === 'Cancelled') ? 'active' : ''; ?>" style="background: white; border: 1px solid var(--border-color);">Cancelled</a>
</div>

<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <?php
    $sql_o = "SELECT * FROM orders";
    if ($status_filter !== 'All') {
        $sql_o .= " WHERE status = '$status_filter'";
    }
    $sql_o .= " ORDER BY id DESC";

    $res_o = mysqli_query($conn, $sql_o);

    if ($res_o && mysqli_num_rows($res_o) > 0) {
        while ($ord = mysqli_fetch_assoc($res_o)) {
            $order_id = $ord['id'];
            $st = $ord['status'];

            $b_class = 'badge-pending';
            if ($st === 'Preparing') $b_class = 'badge-preparing';
            if ($st === 'Out for Delivery') $b_class = 'badge-delivering';
            if ($st === 'Delivered') $b_class = 'badge-delivered';
            if ($st === 'Cancelled') $b_class = 'badge-cancelled';

            $res_items = mysqli_query($conn, "SELECT * FROM order_items WHERE order_id = $order_id");
            ?>
            <div style="background: white; border-radius: var(--radius); padding: 1.5rem; box-shadow: var(--shadow);">
                
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 10px; margin-bottom: 10px; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <strong style="font-size: 1.15rem; color: var(--secondary-color);">Order #<?php echo sprintf('%04d', $order_id); ?></strong>
                        <span style="font-size: 0.85rem; color: var(--text-muted); margin-left: 10px;">
                            <i class="fa-regular fa-clock"></i> <?php echo date('M d, Y - h:i A', strtotime($ord['order_date'])); ?>
                        </span>
                    </div>

                    <form action="manage-orders.php" method="POST" style="display: flex; align-items: center; gap: 8px;">
                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                        <select name="status" class="form-control" style="padding: 4px 8px; font-size: 0.88rem; width: auto;">
                            <option value="Pending" <?php echo ($st === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="Preparing" <?php echo ($st === 'Preparing') ? 'selected' : ''; ?>>Preparing</option>
                            <option value="Out for Delivery" <?php echo ($st === 'Out for Delivery') ? 'selected' : ''; ?>>Out for Delivery</option>
                            <option value="Delivered" <?php echo ($st === 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                            <option value="Cancelled" <?php echo ($st === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                        <button type="submit" name="update_status" class="btn-primary" style="padding: 6px 12px; font-size: 0.85rem;">
                            Update Status
                        </button>
                    </form>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1rem; font-size: 0.92rem;">
                    <div><strong>Customer Name:</strong> <?php echo htmlspecialchars($ord['customer_name']); ?></div>
                    <div><strong>Phone:</strong> <?php echo htmlspecialchars($ord['customer_phone']); ?></div>
                    <div><strong>Email:</strong> <?php echo htmlspecialchars($ord['customer_email']); ?></div>
                    <div><strong>Payment Method:</strong> <?php echo htmlspecialchars($ord['payment_method']); ?></div>
                    <div style="grid-column: 1 / -1;"><strong>Delivery Address:</strong> <?php echo htmlspecialchars($ord['delivery_address']); ?></div>
                </div>

                <div style="background: var(--bg-light); border-radius: 8px; padding: 1rem;">
                    <div style="display: flex; justify-content: space-between; font-weight: 700; margin-bottom: 6px; font-size: 0.9rem;">
                        <span>Ordered Items Breakdown:</span>
                        <span style="color: var(--primary-color);">Total: Rs. <?php echo number_format($ord['total_amount'], 2); ?></span>
                    </div>
                    <ul style="list-style: none; padding: 0;">
                        <?php while ($it = mysqli_fetch_assoc($res_items)): ?>
                            <li style="display: flex; justify-content: space-between; font-size: 0.88rem; margin-bottom: 4px;">
                                <span><?php echo htmlspecialchars($it['food_title']); ?> (×<?php echo $it['qty']; ?>)</span>
                                <span>Rs. <?php echo number_format($it['total'], 2); ?></span>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>

                <div style="margin-top: 10px; text-align: right;">
                    <a href="manage-orders.php?delete=<?php echo $order_id; ?>" class="btn-danger" style="font-size: 0.8rem; padding: 4px 10px;" onclick="return confirm('Permanently delete this order record?');">
                        <i class="fa-solid fa-trash"></i> Delete Order Record
                    </a>
                </div>

            </div>
            <?php
        }
    } else {
        echo "<div style='background: white; padding: 2rem; border-radius: var(--radius); text-align: center; color: var(--text-muted);'>No orders found matching criteria.</div>";
    }
    ?>
</div>

<?php require_once 'footer.php'; ?>
