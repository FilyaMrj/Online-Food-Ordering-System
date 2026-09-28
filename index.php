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
    header("Location: index.php");
    exit();
}
?>

<section class="hero">
    <div class="hero-content">
        <h1>Delicious Food Delivered To Your Door step</h1>
        
        <form action="menu.php" method="GET" class="search-box">
            <input type="text" name="search" placeholder="Search for MoMo, Chowmein..." required>
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
        </form>
    </div>
</section>

<div class="container">

    <?php if (isset($_SESSION['msg_success'])): ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i> <?php echo $_SESSION['msg_success']; unset($_SESSION['msg_success']); ?>
        </div>
    <?php endif; ?>

    <div class="section-title">
        <span><i class="fa-solid fa-list"></i> Explore Categories</span>
        <a href="menu.php" style="font-size: 0.95rem; color: var(--primary-color); font-weight:600;">View All &rarr;</a>
    </div>

    <div class="category-grid">
        <?php
       $sql_cat = "SELECT * FROM categories WHERE active='Yes' AND featured='Yes' LIMIT 8";
        $res_cat = mysqli_query($conn, $sql_cat);

        if ($res_cat && mysqli_num_rows($res_cat) > 0) {
            while ($cat = mysqli_fetch_assoc($res_cat)) {
                ?>
                <a href="menu.php?category_id=<?php echo $cat['id']; ?>" class="category-card">
                    <img src="<?php echo htmlspecialchars($cat['image_name']); ?>" alt="<?php echo htmlspecialchars($cat['title']); ?>">
                    <h3><?php echo htmlspecialchars($cat['title']); ?></h3>
                </a>
                <?php
            }
        } else {
            echo "<p>No featured categories found.</p>";
        }
        ?>
    </div>

    <div class="section-title">
        <span><i class="fa-solid fa-fire"></i> Popular & Featured Dishes</span>
        <a href="menu.php" style="font-size: 0.95rem; color: var(--primary-color); font-weight:600;">Full Menu &rarr;</a>
    </div>

    <div class="food-grid">
        <?php
        $sql_food = "SELECT * FROM food_items WHERE active='Yes' AND featured='Yes' LIMIT 6";
        $res_food = mysqli_query($conn, $sql_food);

        if ($res_food && mysqli_num_rows($res_food) > 0) {
            while ($food = mysqli_fetch_assoc($res_food)) {
                ?>
                <div class="food-card">
                    <div class="food-img-wrap">
                        <img src="<?php echo htmlspecialchars($food['image_name']); ?>" alt="<?php echo htmlspecialchars($food['title']); ?>">
                        <span class="badge-featured">Featured</span>
                    </div>
                    
                    <div class="food-info">
                        <div class="food-title"><?php echo htmlspecialchars($food['title']); ?></div>
                        <div class="food-desc"><?php echo htmlspecialchars($food['description']); ?></div>
                        
                        <div class="food-bottom">
                            <div class="food-price">Rs. <?php echo number_format($food['price'], 2); ?></div>
                            <form action="index.php" method="POST">
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
            echo "<p>No featured food items available right now.</p>";
        }
        ?>
    </div>

</div>

<?php require_once 'footer.php'; ?>
