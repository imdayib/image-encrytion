<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$email = $_SESSION['email'];
$username = ucfirst(strtolower(explode('@', $email)[0]));

$role = $_SESSION['role'] ?? 'User';
$avatar = strtoupper($email[0]);
$metamask = $_SESSION['metamask_address'] ?? '';
// Handle password change
$change_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST['old_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $errors = [];
    if (strlen($new) < 6) {
        $errors[] = 'New password must be at least 6 characters.';
    }
    if ($new !== $confirm) {
        $errors[] = 'New passwords do not match.';
    }
    if (empty($errors)) {
        $conn = new mysqli('localhost', 'root', '', 'image_encryption');
        if ($conn->connect_error) {
            $errors[] = 'Database connection failed.';
        } else {
            $stmt = $conn->prepare('SELECT password_hash FROM users WHERE id = ?');
            $stmt->bind_param('i', $_SESSION['user_id']);
            $stmt->execute();
            $stmt->bind_result($db_hash);
            $stmt->fetch();
            $stmt->close();
            if (!password_verify($old, $db_hash)) {
                $errors[] = 'Old password is incorrect.';
            } else {
                $new_hash = password_hash($new, PASSWORD_DEFAULT);
                $stmt = $conn->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
                $stmt->bind_param('si', $new_hash, $_SESSION['user_id']);
                if ($stmt->execute()) {
                    $change_msg = '<div class="alert alert-success">Password updated successfully.</div>';
                } else {
                    $errors[] = 'Failed to update password.';
                }
                $stmt->close();
            }
            $conn->close();
        }
    }
    if (!empty($errors)) {
        $change_msg = '<div class="alert alert-danger">' . implode('<br>', array_map('htmlspecialchars', $errors)) . '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Secure Encryption</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; min-height: 100vh; }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #6a82fb 0%, #5f2c82 100%);
            color: #fff;
            padding: 2.5rem 1.2rem 1.2rem 1.2rem;
            border-radius: 0 32px 32px 0;
            box-shadow: 0 4px 24px rgba(90, 60, 130, 0.10);
            display: flex;
            flex-direction: column;
            align-items: stretch;
        }
        .sidebar-user {
            display: flex;
            align-items: center;
            margin-bottom: 2.2rem;
            padding-bottom: 1.2rem;
            border-bottom: 1px solid rgba(255,255,255,0.13);
        }
        .sidebar-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #fff 0%, #b3a1e6 100%);
            color: #5f2c82;
            font-size: 1.7rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.9rem;
            box-shadow: 0 2px 8px rgba(90,60,130,0.10);
        }
        .sidebar-user-info {
            display: flex;
            flex-direction: column;
        }
        .sidebar-user-email {
            font-size: 1.05rem;
            font-weight: 600;
            color: #fff;
            line-height: 1.1;
        }
        .sidebar-user-role {
            font-size: 0.92rem;
            color: #e0e0e0;
        }
        .sidebar .nav-link {
            color: #e0e0e0;
            font-weight: 500;
            border-radius: 12px;
            margin-bottom: 0.5rem;
            transition: background 0.18s, color 0.18s;
            display: flex;
            align-items: center;
            font-size: 1.08rem;
            padding: 0.7rem 1rem;
            gap: 0.7rem;
        }
        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.18);
            color: #fff;
            box-shadow: 0 2px 8px rgba(90,60,130,0.10);
        }
        .sidebar .nav-link i {
            font-size: 1.3rem;
            margin-right: 0.8rem;
            color: #fff;
            opacity: 0.85;
        }
        .sidebar .sidebar-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 2.5rem;
            color: #fff;
            letter-spacing: 0.5px;
            text-align: left;
        }
        .main-content {
            padding: 2.5rem 2rem;
        }
        .profile-container { max-width: 480px; margin: 0 auto; }
        .profile-card {
            background: #fff;
            border-radius: 22px;
            box-shadow: 0 6px 32px rgba(90, 60, 130, 0.10);
            padding: 2.5rem 2.2rem 2.2rem 2.2rem;
        }
        .profile-avatar {
            width: 64px; height: 64px; border-radius: 50%;
            background: linear-gradient(135deg, #6a82fb 0%, #5f2c82 100%);
            color: #fff; font-size: 2.2rem; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.1rem auto; box-shadow: 0 2px 12px rgba(106,130,251,0.10);
        }
        .profile-label { color: #5f2c82; font-weight: 500; }
        .readonly-input { background: #f3f0ff; color: #5f2c82; font-weight: 500; }
        .btn-gradient {
            background: linear-gradient(90deg, #6a82fb 0%, #5f2c82 100%);
            border: none; color: #fff; font-weight: 600;
            padding: 0.65rem 2.2rem; border-radius: 30px;
            transition: box-shadow 0.2s, transform 0.2s;
            letter-spacing: 0.5px;
        }
        .btn-gradient:hover {
            box-shadow: 0 4px 24px 0 rgba(90, 60, 130, 0.12);
            color: #fff; transform: translateY(-2px) scale(1.03);
        }
        @media (max-width: 991px) {
            .sidebar {
                border-radius: 0 0 32px 32px;
                min-height: auto;
                padding: 1.5rem 0.7rem;
            }
            .main-content {
                padding: 1.5rem 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-lg-3 col-md-4 sidebar">
                <div class="sidebar-title mb-3">Secure Encryption</div>
                <div class="sidebar-user">
                    <div class="sidebar-avatar"><?php echo htmlspecialchars($avatar); ?></div>
                    <div class="sidebar-user-info">
                        <span class="sidebar-user-email"><?php echo htmlspecialchars($username); ?></span>
                        <span class="sidebar-user-role"><?php echo htmlspecialchars(ucfirst($role)); ?></span>
                    </div>
                </div>
                <a href="dashboard.php" class="nav-link"><i class="bi bi-house-door"></i><span>Dashboard</span></a>
                <a href="encrypt.php" class="nav-link"><i class="bi bi-lock"></i><span>Encrypt Files</span></a>
                 <a href="verify.php" class="nav-link"><i class="bi bi-shield-check"></i><span>Verify Integrity</span></a>
                <a href="decrypt.php" class="nav-link"><i class="bi bi-unlock"></i><span>Decrypt Files</span></a>
                <a href="profile.php" class="nav-link active"><i class="bi bi-person"></i><span>Profile</span></a>
                <a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i><span>Logout</span></a>
            </nav>
            <!-- Main Content -->
            <main class="col-lg-9 col-md-8 main-content">
                <div class="profile-container">
                    <div class="profile-card">
                        <div class="profile-avatar mb-2">
                            <i class="bi bi-person"></i>
                        </div>
                        <h3 class="text-center mb-4" style="color:#5f2c82;font-weight:700;">Profile</h3>
                        <form method="POST" autocomplete="off">
                            <div class="mb-3">
                                <label class="profile-label">Email</label>
                                <input type="email" class="form-control readonly-input" value="<?php echo htmlspecialchars($email); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="profile-label">MetaMask Address</label>
                                <input type="text" class="form-control readonly-input" value="<?php echo htmlspecialchars($metamask); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="profile-label">Role</label>
                                <input type="text" class="form-control readonly-input" value="<?php echo htmlspecialchars(ucfirst($role)); ?>" readonly>
                            </div>
                            <hr class="my-4">
                            <h5 class="mb-3" style="color:#5f2c82;">Change Password</h5>
                            <?php echo $change_msg; ?>
                            <div class="mb-3">
                                <label class="profile-label">Old Password</label>
                                <input type="password" class="form-control" name="old_password" required autocomplete="current-password">
                            </div>
                            <div class="mb-3">
                                <label class="profile-label">New Password</label>
                                <input type="password" class="form-control" name="new_password" required autocomplete="new-password">
                            </div>
                            <div class="mb-3">
                                <label class="profile-label">Confirm New Password</label>
                                <input type="password" class="form-control" name="confirm_password" required autocomplete="new-password">
                            </div>
                            <button type="submit" class="btn btn-gradient w-100">Update Password</button>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html> 