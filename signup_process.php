<?php
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

// Check for duplicates
if (empty($errors)) {
    $stmt = $conn->prepare('SELECT id FROM users WHERE email = ? OR metamask_address = ?');
    $stmt->bind_param('ss', $email, $metamask);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = 'Email or MetaMask address already registered.';
    }
    $stmt->close();
}

if (empty($errors)) {
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $role = 'user';
    $stmt = $conn->prepare('INSERT INTO users (username, email, password_hash, role, metamask_address) VALUES (?, ?, ?, ?, ?)');
    $username = strstr($email, '@', true);
    $stmt->bind_param('sssss', $username, $email, $password_hash, $role, $metamask);
    if ($stmt->execute()) {
        echo '<div style="max-width:400px;margin:3rem auto;padding:2rem 2.5rem;background:#fff;border-radius:18px;box-shadow:0 2px 12px rgba(90,60,130,0.07);text-align:center;">';
        echo '<h3 style="color:#5f2c82;">Signup Successful!</h3>';
        echo '<p>You can now <a href="login.php" style="color:#6a82fb;font-weight:600;">login</a> to your account.</p>';
        echo '</div>';
    } else {
        echo '<div style="max-width:400px;margin:3rem auto;padding:2rem 2.5rem;background:#fff;border-radius:18px;box-shadow:0 2px 12px rgba(90,60,130,0.07);text-align:center;">';
        echo '<h3 style="color:#c00;">Signup Failed</h3>';
        echo '<p>There was an error creating your account. Please try again later.</p>';
        echo '</div>';
    }
    $stmt->close();
} else {
    echo '<div style="max-width:400px;margin:3rem auto;padding:2rem 2.5rem;background:#fff;border-radius:18px;box-shadow:0 2px 12px rgba(90,60,130,0.07);text-align:center;">';
    echo '<h3 style="color:#c00;">Signup Error</h3>';
    foreach ($errors as $err) {
        echo '<p style="color:#c00;">' . htmlspecialchars($err) . '</p>';
    }
    echo '<a href="signup.php" style="color:#6a82fb;font-weight:600;">Back to Sign Up</a>';
    echo '</div>';
}
$conn->close();
?> 