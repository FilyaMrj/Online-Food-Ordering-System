<?php
require_once 'header.php';

$msg = '';
$msg_type = '';

if (isset($_POST['add_food'])) {
    $title = sanitize($_POST['title'], $conn);
    $description = sanitize($_POST['description'], $conn);
    $price = (float)$_POST['price'];
    $category_id = (int)$_POST['category_id'];
    $image_name = sanitize($_POST['image_name'], $conn);
    $featured = sanitize($_POST['featured'], $conn);
    $active = sanitize($_POST['active'], $conn);

    if (empty($image_name)) {
        $image_name = 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&auto=format&fit=crop&q=80';
    }

    if (empty($title) || $price <= 0 || $category_id <= 0) {
        $msg = "Title, Price, and Category are required!";
        $msg_type = "danger";
    } else {
        $sql = "INSERT INTO food_items (title, description, price, image_name, category_id, featured, active) 
                VALUES ('$title', '$description', $price, '$image_name', $category_id, '$featured', '$active')";
        if (mysqli_query($conn, $sql)) {
            $msg = "Food item '$title' added successfully!";
            $msg_type = "success";
        } else {
            $msg = "Failed to add food item: " . mysqli_error($conn);
            $msg_type = "danger";
        }
    }
}

if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    $sql_del = "DELETE FROM food_items WHERE id = $del_id";
    if (mysqli_query($conn, $sql_del)) {
        $msg = "Food item deleted successfully!";
        $msg_type = "success";
    } else {
        $msg = "Failed to delete food item: " . mysqli_error($conn);
        $msg_type = "danger";
    }
}

$edit_mode = false;
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $res_edit = mysqli_query($conn, "SELECT * FROM food_items WHERE id = $edit_id");
    if ($res_edit && mysqli_num_rows($res_edit) == 1) {
        $edit_mode = true;
        $edit_data = mysqli_fetch_assoc($res_edit);
    }
}

if (isset($_POST['update_food'])) {
    $id = (int)$_POST['food_id'];
    $title = sanitize($_POST['title'], $conn);
    $description = sanitize($_POST['description'], $conn);
    $price = (float)$_POST['price'];
    $category_id = (int)$_POST['category_id'];
    $image_name = sanitize($_POST['image_name'], $conn);
    $featured = sanitize($_POST['featured'], $conn);
    $active = sanitize($_POST['active'], $conn);

    $sql_u = "UPDATE food_items 
              SET title='$title', description='$description', price=$price, category_id=$category_id, image_name='$image_name', featured='$featured', active='$active' 
              WHERE id=$id";
    if (mysqli_query($conn, $sql_u)) {
        $_SESSION['food_msg'] = "Food item updated successfully!";
        header("Location: manage-food.php");
        exit();
    } else {
        $msg = "Failed to update food item: " . mysqli_error($conn);
        $msg_type = "danger";
    }
}

if (isset($_SESSION['food_msg'])) {
    $msg = $_SESSION['food_msg'];
    $msg_type = "success";
    unset($_SESSION['food_msg']);
}
?>

<div class="section-title">
    <span><i class="fa-solid fa-bowl-food"></i> Manage Food Items</span>
</div>

<?php if (!empty($msg)): ?>
    <div class="alert alert-<?php echo $msg_type; ?>">
        <i class="fa-solid fa-circle-info"></i> <?php echo $msg; ?>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 350px 1fr; gap: 2rem; align-items: start;">
    
    <div class="form-card" style="margin: 0; max-width: 100%;">
        <h3 style="margin-bottom: 1rem; color: var(--secondary-color);">
            <i class="fa-solid fa-<?php echo $edit_mode ? 'pen-to-square' : 'plus'; ?>"></i> 
            <?php echo $edit_mode ? 'Edit Food Item' : 'Add New Food'; ?>
        </h3>

        <form action="manage-food.php" method="POST">
            <?php if ($edit_mode): ?>
                <input type="hidden" name="food_id" value="<?php echo $edit_data['id']; ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="title">Food Title *</label>
                <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($edit_data['title'] ?? ''); ?>" required placeholder="e.g. Steam Chicken MoMo">
            </div>

            <div class="form-group">
                <label for="category_id">Category *</label>
                <select id="category_id" name="category_id" class="form-control" required>
                    <option value="">-- Select Category --</option>
                    <?php
                    $res_cats = mysqli_query($conn, "SELECT * FROM categories WHERE active='Yes'");
                    while ($cat = mysqli_fetch_assoc($res_cats)) {
                        $selected = (($edit_data['category_id'] ?? 0) == $cat['id']) ? 'selected' : '';
                        echo '<option value="'.$cat['id'].'" '.$selected.'>'.htmlspecialchars($cat['title']).'</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="price">Price (NPR / Rs.) *</label>
                <input type="number" step="0.01" id="price" name="price" class="form-control" value="<?php echo htmlspecialchars($edit_data['price'] ?? ''); ?>" required placeholder="250.00">
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" placeholder="Ingredients and details..."><?php echo htmlspecialchars($edit_data['description'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="image_name">Image URL</label>
                <input type="text" id="image_name" name="image_name" class="form-control" value="<?php echo htmlspecialchars($edit_data['image_name'] ?? ''); ?>" placeholder="https://example.com/food.jpg">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="featured">Featured?</label>
                    <select id="featured" name="featured" class="form-control">
                        <option value="No" <?php echo (($edit_data['featured'] ?? '') === 'No') ? 'selected' : ''; ?>>No</option>
                        <option value="Yes" <?php echo (($edit_data['featured'] ?? '') === 'Yes') ? 'selected' : ''; ?>>Yes</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="active">Active?</label>
                    <select id="active" name="active" class="form-control">
                        <option value="Yes" <?php echo (($edit_data['active'] ?? '') === 'Yes') ? 'selected' : ''; ?>>Yes</option>
                        <option value="No" <?php echo (($edit_data['active'] ?? '') === 'No') ? 'selected' : ''; ?>>No</option>
                    </select>
                </div>
            </div>

            <button type="submit" name="<?php echo $edit_mode ? 'update_food' : 'add_food'; ?>" class="btn-primary" style="width: 100%; padding: 10px;">
                <?php echo $edit_mode ? 'Update Item' : 'Save Item'; ?>
            </button>
            <?php if ($edit_mode): ?>
                <a href="manage-food.php" class="btn-primary" style="background: #64748b; width: 100%; text-align: center; margin-top: 8px; display: block;">Cancel Edit</a>
            <?php endif; ?>
        </form>
    </div>

    <div style="background: white; border-radius: var(--radius); padding: 1.5rem; box-shadow: var(--shadow); overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Title & Category</th>
                    <th>Price</th>
                    <th>Featured</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql_f = "SELECT f.*, c.title as category_title 
                          FROM food_items f 
                          LEFT JOIN categories c ON f.category_id = c.id 
                          ORDER BY f.id DESC";
                $res_f = mysqli_query($conn, $sql_f);

                if ($res_f && mysqli_num_rows($res_f) > 0) {
                    while ($food = mysqli_fetch_assoc($res_f)) {
                        ?>
                        <tr>
                            <td>
                                <img src="<?php echo htmlspecialchars($food['image_name']); ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">
                            </td>
                            <td>
                                <strong style="color: var(--secondary-color);"><?php echo htmlspecialchars($food['title']); ?></strong>
                                <div style="font-size: 0.8rem; color: var(--text-muted);"><?php echo htmlspecialchars($food['category_title'] ?? 'Uncategorized'); ?></div>
                            </td>
                            <td style="font-weight: 700; color: var(--primary-color);">Rs. <?php echo number_format($food['price'], 2); ?></td>
                            <td><?php echo $food['featured']; ?></td>
                            <td><?php echo $food['active']; ?></td>
                            <td>
                                <a href="manage-food.php?edit=<?php echo $food['id']; ?>" class="btn-primary" style="padding: 4px 8px; font-size: 0.8rem;">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <a href="manage-food.php?delete=<?php echo $food['id']; ?>" class="btn-danger" style="padding: 4px 8px; font-size: 0.8rem;" onclick="return confirm('Delete this food item?');">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='6'>No food items found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</div>

<?php require_once 'footer.php'; ?>
