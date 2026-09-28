<?php
require_once 'header.php';

if (isset($_POST['add_to_cart'])) {
    $food_id = (int)$_POST['food_id'];
    $food_title = $_POST['food_title'];
    $food_price = (float)$_POST['food_price'];
    $food_image = $_POST['food_image'];
    $qty = 1;

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    if (isset($_SESSION['cart'][$food_id])) {
        $_SESSION['cart'][$food_id]['qty'] += 1;
    } else {
        $_SESSION['cart'][$food_id] = array(
            'id' => $food_id,
            'title' => $food_title,
            'price' => $food_price,
            'image' => $food_image,
            'qty' => $qty
        );
    }

    $_SESSION['msg_success'] = "Added '$food_title' to your cart!";
    
    $redirect = "menu.php";
    if (isset($_GET['category_id'])) {
        $redirect .= "?category_id=" . (int)$_GET['category_id'];
    } elseif (isset($_GET['search'])) {
        $redirect .= "?search=" . urlencode($_GET['search']);
    }
    
    header("Location: " . $redirect);
    exit();
}

$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$search = isset($_GET['search']) ? sanitize($_GET['search'], $conn) : '';
?>

<div class="container">

    <?php if (isset($_SESSION['msg_success'])): ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i> <?php echo $_SESSION['msg_success']; unset($_SESSION['msg_success']); ?>
        </div>
    <?php endif; ?>

    <div style="margin-bottom: 2rem; display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
        <span style="font-weight: 700; color: var(--secondary-color);"><i class="fa-solid fa-filter"></i> Categories:</span>
        <a href="menu.php" class="nav-link <?php echo ($category_id == 0 && empty($search)) ? 'active' : ''; ?>" style="border: 1px solid var(--border-color);">All Dishes</a>
        
        <?php
        $sql_all_cat = "SELECT * FROM categories WHERE active='Yes'";
        $res_all_cat = mysqli_query($conn, $sql_all_cat);
        if ($res_all_cat && mysqli_num_rows($res_all_cat) > 0) {
            while ($cat_item = mysqli_fetch_assoc($res_all_cat)) {
                $is_active = ($category_id == $cat_item['id']) ? 'active' : '';
                echo '<a href="menu.php?category_id='.$cat_item['id'].'" class="nav-link '.$is_active.'" style="border: 1px solid var(--border-color);">'.htmlspecialchars($cat_item['title']).'</a>';
            }
        }
        ?>
    </div>

    <div class="section-title">
        <?php
        if ($category_id > 0) {
            $cat_query = "SELECT title FROM categories WHERE id=$category_id";
            $cat_res = mysqli_query($conn, $cat_query);
            $cat_name = mysqli_fetch_assoc($cat_res)['title'] ?? 'Category';
            echo "<span>Category: " . htmlspecialchars($cat_name) . "</span>";
        } elseif (!empty($search)) {
            echo "<span>Search Results for: \"" . htmlspecialchars($search) . "\"</span>";
        } else {
            echo "<span>Full Food Menu</span>";
        }
        ?>
    </div>

    <div class="food-grid">
        <?php
        $sql = "SELECT * FROM food_items WHERE active='Yes'";

        if ($category_id > 0) {
            $sql .= " AND category_id = $category_id";
        }

        if (!empty($search)) {
            $sql .= " AND (title LIKE '%$search%' OR description LIKE '%$search%')";
        }

        $sql .= " ORDER BY id DESC";
        $res = mysqli_query($conn, $sql);

        if ($res && mysqli_num_rows($res) > 0) {
            while ($food = mysqli_fetch_assoc($res)) {
                ?>
                <div class="food-card">
                    <div class="food-img-wrap">
                        <img src="<?php echo htmlspecialchars($food['image_name']); ?>" alt="<?php echo htmlspecialchars($food['title']); ?>">
                        <?php if ($food['featured'] === 'Yes'): ?>
                            <span class="badge-featured">Popular</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="food-info">
                        <div class="food-title"><?php echo htmlspecialchars($food['title']); ?></div>
                        <div class="food-desc"><?php echo htmlspecialchars($food['description']); ?></div>
                        
                        <div class="food-bottom">
                            <div class="food-price">Rs. <?php echo number_format($food['price'], 2); ?></div>
                            
                            <form action="" method="POST">
                                <input type="hidden" name="food_id" value="<?php echo $food['id']; ?>">
                                <input type="hidden" name="food_title" value="<?php echo htmlspecialchars($food['title']); ?>">
                                <input type="hidden" name="food_price" value="<?php echo $food['price']; ?>">
                                <input type="hidden" name="food_image" value="<?php echo htmlspecialchars($food['image_name']); ?>">
                                <button type="submit" name="add_to_cart" class="btn-add-cart">
                                    <i class="fa-solid fa-cart-plus"></i> Add
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<div style='grid-column: 1/-1; padding: 2rem; text-align: center; background: white; border-radius: var(--radius); shadow: var(--shadow);'>
                    <i class='fa-solid fa-face-frown' style='font-size: 3rem; color: var(--text-muted); margin-bottom: 1rem;'></i>
                    <h3>No food items found!</h3>
                    <p style='color: var(--text-muted);'>Try searching with a different keyword or selecting another category.</p>
                  </div>";
        }
        ?>
    </div>

</div>

<?php require_once 'footer.php'; ?>
