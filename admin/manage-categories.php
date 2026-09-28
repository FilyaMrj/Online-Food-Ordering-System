<?php
require_once 'header.php';

$msg = '';
$msg_type = '';

if (isset($_POST['add_category'])) {
    $title = sanitize($_POST['title'], $conn);
    $image_name = sanitize($_POST['image_name'], $conn);
    $featured = sanitize($_POST['featured'], $conn);
    $active = sanitize($_POST['active'], $conn);

    if (empty($image_name)) {
        $image_name = 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=500&auto=format&fit=crop&q=80';
    }

    if (empty($title)) {
        $msg = "Category title is required!";
        $msg_type = "danger";
    } else {
        $sql = "INSERT INTO categories (title, image_name, featured, active) VALUES ('$title', '$image_name', '$featured', '$active')";
        if (mysqli_query($conn, $sql)) {
            $msg = "Category '$title' added successfully!";
            $msg_type = "success";
        } else {
            $msg = "Failed to add category: " . mysqli_error($conn);
            $msg_type = "danger";
        }
    }
}

if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    $sql_del = "DELETE FROM categories WHERE id = $del_id";
    if (mysqli_query($conn, $sql_del)) {
        $msg = "Category deleted successfully!";
        $msg_type = "success";
    } else {
        $msg = "Failed to delete category: " . mysqli_error($conn);
        $msg_type = "danger";
    }
}

$edit_mode = false;
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $res_edit = mysqli_query($conn, "SELECT * FROM categories WHERE id = $edit_id");
    if ($res_edit && mysqli_num_rows($res_edit) == 1) {
        $edit_mode = true;
        $edit_data = mysqli_fetch_assoc($res_edit);
    }
}

if (isset($_POST['update_category'])) {
    $id = (int)$_POST['category_id'];
    $title = sanitize($_POST['title'], $conn);
    $image_name = sanitize($_POST['image_name'], $conn);
    $featured = sanitize($_POST['featured'], $conn);
    $active = sanitize($_POST['active'], $conn);

    $sql_u = "UPDATE categories SET title='$title', image_name='$image_name', featured='$featured', active='$active' WHERE id=$id";
    if (mysqli_query($conn, $sql_u)) {
        $_SESSION['cat_msg'] = "Category updated successfully!";
        header("Location: manage-categories.php");
        exit();
    } else {
        $msg = "Failed to update category: " . mysqli_error($conn);
        $msg_type = "danger";
    }
}

if (isset($_SESSION['cat_msg'])) {
    $msg = $_SESSION['cat_msg'];
    $msg_type = "success";
    unset($_SESSION['cat_msg']);
}
?>

<div class="section-title">
    <span><i class="fa-solid fa-list-check"></i> Manage Food Categories</span>
</div>

<?php if (!empty($msg)): ?>
    <div class="alert alert-<?php echo $msg_type; ?>">
        <i class="fa-solid fa-circle-info"></i> <?php echo $msg; ?>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; align-items: start;">
    
    <!-- Add / Edit Category Form -->
    <div class="form-card" style="margin: 0; max-width: 100%;">
        <h3 style="margin-bottom: 1rem; color: var(--secondary-color);">
            <i class="fa-solid fa-<?php echo $edit_mode ? 'pen-to-square' : 'plus'; ?>"></i> 
            <?php echo $edit_mode ? 'Edit Category' : 'Add New Category'; ?>
        </h3>

        <form action="manage-categories.php" method="POST">
            <?php if ($edit_mode): ?>
                <input type="hidden" name="category_id" value="<?php echo $edit_data['id']; ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="title">Category Title *</label>
                <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($edit_data['title'] ?? ''); ?>" required placeholder="e.g. MoMo, Pizza, Drinks">
            </div>

            <div class="form-group">
                <label for="image_name">Image URL</label>
                <input type="text" id="image_name" name="image_name" class="form-control" value="<?php echo htmlspecialchars($edit_data['image_name'] ?? ''); ?>" placeholder="https://example.com/image.jpg">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="featured">Featured on Home?</label>
                    <select id="featured" name="featured" class="form-control">
                        <option value="Yes" <?php echo (($edit_data['featured'] ?? '') === 'Yes') ? 'selected' : ''; ?>>Yes</option>
                        <option value="No" <?php echo (($edit_data['featured'] ?? '') === 'No') ? 'selected' : ''; ?>>No</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="active">Active Status?</label>
                    <select id="active" name="active" class="form-control">
                        <option value="Yes" <?php echo (($edit_data['active'] ?? '') === 'Yes') ? 'selected' : ''; ?>>Yes</option>
                        <option value="No" <?php echo (($edit_data['active'] ?? '') === 'No') ? 'selected' : ''; ?>>No</option>
                    </select>
                </div>
            </div>

            <button type="submit" name="<?php echo $edit_mode ? 'update_category' : 'add_category'; ?>" class="btn-primary" style="width: 100%; padding: 10px;">
                <?php echo $edit_mode ? 'Update Category' : 'Save Category'; ?>
            </button>
            <?php if ($edit_mode): ?>
                <a href="manage-categories.php" class="btn-primary" style="background: #64748b; width: 100%; text-align: center; margin-top: 8px; display: block;">Cancel Edit</a>
            <?php endif; ?>
        </form>
    </div>

    <div style="background: white; border-radius: var(--radius); padding: 1.5rem; box-shadow: var(--shadow);">
        <table class="data-table">
            <thead>
                <tr>
                    <th>S.N.</th>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Featured</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql_cat = "SELECT * FROM categories ORDER BY id DESC";
                $res_cat = mysqli_query($conn, $sql_cat);
                $sn = 1;

                if ($res_cat && mysqli_num_rows($res_cat) > 0) {
                    while ($c = mysqli_fetch_assoc($res_cat)) {
                        ?>
                        <tr>
                            <td><?php echo $sn++; ?></td>
                            <td>
                                <img src="<?php echo htmlspecialchars($c['image_name']); ?>" alt="" style="width: 45px; height: 45px; object-fit: cover; border-radius: 6px;">
                            </td>
                            <td style="font-weight: 700; color: var(--secondary-color);"><?php echo htmlspecialchars($c['title']); ?></td>
                            <td><?php echo $c['featured']; ?></td>
                            <td><?php echo $c['active']; ?></td>
                            <td>
                                <a href="manage-categories.php?edit=<?php echo $c['id']; ?>" class="btn-primary" style="padding: 4px 8px; font-size: 0.8rem;">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <a href="manage-categories.php?delete=<?php echo $c['id']; ?>" class="btn-danger" style="padding: 4px 8px; font-size: 0.8rem;" onclick="return confirm('Delete this category? Associated food items will also be removed!');">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='6'>No categories found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</div>

<?php require_once 'footer.php'; ?>
