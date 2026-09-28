<?php
require_once 'header.php';

if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
        header("Location: admin/index.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

$error = '';
if (isset($_POST['submit_login'])) {
    $email = sanitize($_POST['email'], $conn);
    $password = md5($_POST['password']); // Standard MD5 for BCA simplicity

    if (empty($email) || empty($_POST['password'])) {
        $error = "Please enter both Email and Password!";
    } else {
        $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
        $res = mysqli_query($conn, $sql);

        if ($res && mysqli_num_rows($res) == 1) {
            $user = mysqli_fetch_assoc($res);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            $_SESSION['msg_success'] = "Welcome back, " . htmlspecialchars($user['full_name']) . "!";

            if ($user['role'] === 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $error = "Invalid Email or Password!";
        }
    }
}
?>

<div class="container">
    <div class="form-card">
        <h2 style="text-align: center; margin-bottom: 1.5rem; color: var(--secondary-color);">
            <i class="fa-solid fa-right-to-bracket"></i> Customer Login
        </h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['msg_success'])): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i> <?php echo $_SESSION['msg_success']; unset($_SESSION['msg_success']); ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="e.g. laila@gmail.com" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="laila123" required>
            </div>

            <button type="submit" name="submit_login" class="btn-primary" style="width: 100%; padding: 12px; font-size: 1.05rem;">
                Login to Account
            </button>
        </form>

        <p style="text-align: center; margin-top: 1.5rem; color: var(--text-muted);">
            Don't have an account? <a href="register.php" style="color: var(--primary-color); font-weight:700;">Register Here</a>
        </p>

        <!-- <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px dashed var(--border-color); font-size: 0.85rem; color: var(--text-muted);">
            <strong>Demo Customer Account:</strong><br>
            Email: <code>laila@gmail.com</code> | Password: <code>user123</code><br>
            <strong>Demo Admin Account:</strong><br>
            Email: <code>admin@gmail.com</code> | Password: <code>admin123</code>
        </div> -->
    </div>
</div>

<?php require_once 'footer.php'; ?>
