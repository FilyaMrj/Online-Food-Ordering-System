<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'food_order_db');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("<div style='color: red; padding: 20px; font-family: sans-serif;'>
            <h2>Database Connection Failed!</h2>
            <p>Please check your XAMPP Control Panel:</p>
            <ul>
                <li>Ensure <strong>Apache</strong> and <strong>MySQL</strong> services are started.</li>
                <li>Ensure you have created a database named <code>food_order_db</code> in phpMyAdmin and imported <code>database.sql</code>.</li>
            </ul>
            <p>Error details: " . mysqli_connect_error() . "</p>
         </div>");
}

mysqli_set_charset($conn, "utf8mb4");

function sanitize($data, $conn) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$script = $_SERVER['SCRIPT_NAME'];
$dir = dirname($script);
$site_url = $protocol . "://" . $host . rtrim(str_replace('/admin', '', $dir), '/\\') . '/';
define('SITEURL', $site_url);
?>
