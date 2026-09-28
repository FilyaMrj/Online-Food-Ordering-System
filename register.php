<?php
require_once 'header.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
if (isset($_POST['submit_register'])) {
    $full_name = sanitize($_POST['full_name'], $conn);
    $email = sanitize($_POST['email'], $conn);
    $phone = sanitize($_POST['phone'], $conn);
    $address = sanitize($_POST['address'], $conn);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($full_name) || empty($email) || empty($phone) || empty($address) || empty($password)) {
        $error = "All fields are required!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {

    $check_sql = "SELECT id FROM users WHERE email='$email'";
        $check_res = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($check_res) > 0) {
            $error = "Email address is already registered! Please login instead.";
        } else {
            $hashed_pass = md5($password);
            $sql = "INSERT INTO users (full_name, email, password, phone, address, role) 
                    VALUES ('$full_name', '$email', '$hashed_pass', '$phone', '$address', 'customer')";

            if (mysqli_query($conn, $sql)) {
                $new_id = mysqli_insert_id($conn);
                $_SESSION['user_id'] = $new_id;
                $_SESSION['user_name'] = $full_name;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = 'customer';

                $_SESSION['msg_success'] = "Account created successfully! Welcome to QuickBite.";
                header("Location: index.php");
                exit();
            } else {
                $error = "Registration failed! Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<div class="container">
    <div class="form-card">
        <h2 style="text-align: center; margin-bottom: 1.5rem; color: var(--secondary-color);">
            <i class="fa-solid fa-user-plus"></i> Create Customer Account
        </h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="full_name">Full Name *</label>
                <input type="text" id="full_name" name="full_name" class="form-control" placeholder="e.g. Hari Sharma" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="e.g. hari@gmail.com" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number *</label>
                <input type="text" id="phone" name="phone" class="form-control" placeholder="e.g. 9841000000" required>
            </div>

            <div class="form-group">
                <label for="address">Delivery Address *</label>
                <textarea id="address" name="address" class="form-control" placeholder="Tole / City / Area" required></textarea>
            </div>

            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Create a password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password *</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Re-enter password" required>
            </div>

            <button type="submit" name="submit_register" class="btn-primary" style="width: 100%; padding: 12px; font-size: 1.05rem;">
                Register Now
            </button>
        </form>

        <p style="text-align: center; margin-top: 1.5rem; color: var(--text-muted);">
            Already have an account? <a href="login.php" style="color: var(--primary-color); font-weight:700;">Login Here</a>
        </p>
    </div>
</div>

<?php require_once 'footer.php'; ?>
