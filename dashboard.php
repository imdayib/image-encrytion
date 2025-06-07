<?php
    session_start();

    // Check if user is logged in
    if (! isset($_SESSION['email'])) {
        header("Location: index.php");
        exit();
    }

    // Get user information from session
    $email = $_SESSION['email'];

    $username = ucfirst(strtolower(explode('@', $email)[0]));

    $role   = $_SESSION['role'];
    $avatar = strtoupper($email[0]); // Get first letter of email for avatar
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Secure Encryption</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f8f9fa;
            min-height: 100vh;
        }
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
        .main-content {
            padding: 2.5rem 2rem;
        }
        .user-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 2px 12px rgba(90, 60, 130, 0.07);
            padding: 1.2rem 1.5rem;
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
        }
        .user-avatar {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6a82fb 0%, #5f2c82 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 2rem;
            font-weight: 700;
            margin-right: 1.2rem;
        }
        .user-info-email {
            font-weight: 600;
            color: #5f2c82;
            font-size: 1.1rem;
        }
        .user-info-role {
            color: #888;
            font-size: 0.98rem;
        }
        .welcome-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 2px 12px rgba(90, 60, 130, 0.07);
            padding: 2rem 2.5rem;
            margin-bottom: 2.2rem;
        }
        .action-cards {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        .action-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 2px 12px rgba(90, 60, 130, 0.07);
            padding: 2.2rem 2rem 1.7rem 2rem;
            flex: 1 1 260px;
            min-width: 260px;
            max-width: 350px;
            text-align: center;
        }
        .action-card i {
            font-size: 2.5rem;
            color: #6a82fb;
            margin-bottom: 1rem;
        }
        .action-card .btn-gradient {
            margin-top: 1.2rem;
            width: 70%;
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
            .action-cards {
                flex-direction: column;
                gap: 1.2rem;
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
                <a href="profile.php" class="nav-link"><i class="bi bi-person"></i><span>Profile</span></a>
<?php if (strtolower($role) === 'admin'): ?>

        <a href="report.php" class="nav-link"><i class="bi bi-bar-chart"></i><span>System Reports</span></a>

<?php else: ?>

        <a href="report.php" class="nav-link"><i class="bi bi-activity"></i><span>My Activity</span></a>

<?php endif; ?>
                <a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i><span>Logout</span></a>
            </nav>
            <!-- Main Content -->
            <main class="col-lg-9 col-md-8 main-content">
                <!-- User Card -->
                <div class="user-card mb-4">
                    <div class="user-avatar"><?php echo htmlspecialchars($avatar); ?></div>
                    <div>
                        <div class="user-info-email"><?php echo htmlspecialchars($username); ?></div>
                        <div class="user-info-role"><?php echo htmlspecialchars(ucfirst($role)); ?></div>
                    </div>
                </div>
                <!-- Welcome Card -->
                <div class="welcome-card mb-5">
                    <h2 class="mb-2" style="color:#5f2c82;font-weight:700;"><i class="bi bi-hand-thumbs-up-fill"></i>Welcome,                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         <?php echo htmlspecialchars($username); ?>!</h2>
                    <div class="text-muted">Manage your encrypted files securely</div>
                </div>
                <!-- Action Cards -->
                <div class="action-cards">
                    <div class="action-card">
                        <i class="bi bi-lock"></i>
                        <h4 class="mb-2">Encrypt Files</h4>
                        <div class="mb-2 text-muted">Secure your files with advanced encryption</div>
                        <a href="encrypt.php" class="btn btn-gradient ">Encrypt Now</a>
                    </div>
                    <div class="action-card">
                        <i class="bi bi-shield-check"></i>
                        <h4 class="mb-2">Verify Integrity</h4>
                        <div class="mb-2 text-muted">Check if your encryted files have been modified</div>
                        <a href="verify.php" class="btn btn-gradient">Verify Now</a>
                    </div>
                    <div class="action-card">
                        <i class="bi bi-unlock"></i>
                        <h4 class="mb-2">Decrypt Files</h4>
                        <div class="mb-2 text-muted">Access your encrypted files securely</div>
                        <a href="decrypt.php" class="btn btn-gradient">Decrypt Now</a>
                    </div>


     </main>
        </div>
    </div>
</body>
</html>