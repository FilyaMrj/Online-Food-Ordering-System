<?php
require_once '../config.php';

if (isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    header("Location: index.php");
    exit();
}

$error = '';
if (isset($_POST['submit_admin_login'])) {
    $email = sanitize($_POST['email'], $conn);
    $password = md5($_POST['password']);

    if (empty($email) || empty($_POST['password'])) {
        $error = "Please enter both Email and Password!";
    } else {
        $sql = "SELECT * FROM users WHERE email='$email' AND password='$password' AND role='admin'";
        $res = mysqli_query($conn, $sql);

        if ($res && mysqli_num_rows($res) == 1) {
            $admin = mysqli_fetch_assoc($res);
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['user_name'] = $admin['full_name'];
            $_SESSION['user_email'] = $admin['email'];
            $_SESSION['user_role'] = 'admin';

            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid Admin Credentials!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | NepBites System</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background: #1e293b; display: flex; align-items: center; justify-content: center; min-height: 100vh;">

    <div class="form-card" style="width: 100%; max-width: 420px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div style="font-size: 2.5rem; color: var(--primary-color);">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <h2 style="color: var(--secondary-color); font-size: 1.6rem; margin-top: 6px;">Admin Portal</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Sign in to access management dashboard</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['msg_error'])): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-triangle-exclamation"></i> <?php echo $_SESSION['msg_error']; unset($_SESSION['msg_error']); ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Admin Email</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="admin@gmail.com" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" name="submit_admin_login" class="btn-primary" style="width: 100%; padding: 12px; font-size: 1.05rem;">
                Sign In to Dashboard
            </button>
        </form>

        <div style="margin-top: 1.5rem; text-align: center;">
            <a href="../index.php" style="color: var(--text-muted); font-size: 0.88rem;">&larr; Back to Front Website</a>
        </div>
    </div>

</body>
</html>
