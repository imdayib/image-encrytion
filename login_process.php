<?php
session_start();
// Database connection
$conn = new mysqli('localhost', 'root', '', 'image_encryption');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Validate POST data
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$metamask = isset($_POST['metamask_address']) ? trim($_POST['metamask_address']) : '';

$errors = [];
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email address.';
}
if (strlen($password) < 6) {
    $errors[] = 'Password must be at least 6 characters.';
}
if (!preg_match('/^0x[a-fA-F0-9]{40}$/', $metamask)) {
    $errors[] = 'Invalid MetaMask address.';
}

if (empty($errors)) {
    $stmt = $conn->prepare('SELECT id, email, password_hash, role, metamask_address FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $db_email, $db_password_hash, $role, $db_metamask);
        $stmt->fetch();
        if (password_verify($password, $db_password_hash)) {
            if (strcasecmp($db_metamask, $metamask) === 0) {
                // Success: set session and redirect
                $_SESSION['user_id'] = $id;
                $_SESSION['email'] = $db_email;
                $_SESSION['role'] = $role;
                $_SESSION['metamask_address'] = $db_metamask;
                header('Location: dashboard.php');
                exit;
            } else {
                $errors[] = 'MetaMask address does not match our records.';
            }
        } else {
            $errors[] = 'Incorrect password.';
        }
    } else {
        $errors[] = 'No account found with that email.';
    }
    $stmt->close();
}

if (!empty($errors)) {
    echo '<div style="max-width:400px;margin:3rem auto;padding:2rem 2.5rem;background:#fff;border-radius:18px;box-shadow:0 2px 12px rgba(90,60,130,0.07);text-align:center;">';
    echo '<h3 style="color:#c00;">Login Error</h3>';
    foreach ($errors as $err) {
        echo '<p style="color:#c00;">' . htmlspecialchars($err) . '</p>';
    }
    echo '<a href="login.php" style="color:#6a82fb;font-weight:600;">Back to Login</a>';
    echo '</div>';
}
$conn->close();
?> 