<?php
session_start();
$email = $_SESSION['email'];
$username = ucfirst(strtolower(explode('@', $email)[0]));

$role = $_SESSION['role'] ?? 'User';
$avatar = strtoupper($email[0]);
if (strtolower($role) !== 'admin') {
    echo '<div style="max-width:500px;margin:4rem auto;padding:2rem 2.5rem;background:#fff;border-radius:18px;box-shadow:0 2px 12px rgba(90,60,130,0.07);text-align:center;">';
    echo '<h3 style="color:#c00;">Access Denied</h3><p>You do not have permission to view this page.</p>';
    echo '<a href="dashboard.php" style="color:#6a82fb;font-weight:600;">Back to Dashboard</a>';
    echo '</div>';
    exit;
}
// Fetch report data and summary stats
$conn = new mysqli('localhost', 'root', '', 'image_encryption');
$rows = [];
$total_encrypt = 0;
$total_decrypt = 0;
$total_users = 0;
if (!$conn->connect_error) {
    // Summary counts
    $res = $conn->query("SELECT COUNT(*) AS c FROM report WHERE action='encrypt'");
    if ($res && $row = $res->fetch_assoc()) $total_encrypt = $row['c'];
    $res = $conn->query("SELECT COUNT(*) AS c FROM report WHERE action='decrypt'");
    if ($res && $row = $res->fetch_assoc()) $total_decrypt = $row['c'];
    $res = $conn->query("SELECT COUNT(*) AS c FROM users");
    if ($res && $row = $res->fetch_assoc()) $total_users = $row['c'];
    // Table data
    $sql = "SELECT r.id, u.email, r.action, r.details, r.created_at FROM report r JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC";
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report - Secure Encryption</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; min-height: 100vh; }
        .sidebar { min-height: 100vh; background: linear-gradient(135deg, #6a82fb 0%, #5f2c82 100%); color: #fff; padding: 2.5rem 1.2rem 1.2rem 1.2rem; border-radius: 0 32px 32px 0; box-shadow: 0 4px 24px rgba(90, 60, 130, 0.10); display: flex; flex-direction: column; align-items: stretch; }
        .sidebar-user { display: flex; align-items: center; margin-bottom: 2.2rem; padding-bottom: 1.2rem; border-bottom: 1px solid rgba(255,255,255,0.13); }
        .sidebar-avatar { width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg, #fff 0%, #b3a1e6 100%); color: #5f2c82; font-size: 1.7rem; font-weight: 700; display: flex; align-items: center; justify-content: center; margin-right: 0.9rem; box-shadow: 0 2px 8px rgba(90,60,130,0.10); }
        .sidebar-user-info { display: flex; flex-direction: column; }
        .sidebar-user-email { font-size: 1.05rem; font-weight: 600; color: #fff; line-height: 1.1; }
        .sidebar-user-role { font-size: 0.92rem; color: #e0e0e0; }
        .sidebar .nav-link { color: #e0e0e0; font-weight: 500; border-radius: 12px; margin-bottom: 0.5rem; transition: background 0.18s, color 0.18s; display: flex; align-items: center; font-size: 1.08rem; padding: 0.7rem 1rem; gap: 0.7rem; }
        .sidebar .nav-link.active, .sidebar .nav-link:hover { background: rgba(255,255,255,0.18); color: #fff; box-shadow: 0 2px 8px rgba(90,60,130,0.10); }
        .sidebar .nav-link i { font-size: 1.3rem; margin-right: 0.8rem; color: #fff; opacity: 0.85; }
        .sidebar .sidebar-title { font-size: 1.5rem; font-weight: 700; margin-bottom: 2.5rem; color: #fff; letter-spacing: 0.5px; text-align: left; }
        .main-content { padding: 2.5rem 2rem; }
        .report-container { max-width: 1000px; margin: 0 auto; }
        .summary-cards { display: flex; gap: 2rem; margin-bottom: 2.5rem; flex-wrap: wrap; }
        .summary-card { background: #fff; border-radius: 18px; box-shadow: 0 2px 12px rgba(90, 60, 130, 0.07); padding: 1.5rem 2rem; flex: 1 1 220px; min-width: 220px; text-align: center; }
        .summary-card .icon { font-size: 2rem; margin-bottom: 0.5rem; color: #6a82fb; }
        .summary-card .count { font-size: 2.1rem; font-weight: 700; color: #5f2c82; }
        .summary-card .label { color: #888; font-size: 1.08rem; font-weight: 500; }
        .table thead th { background: #f3f0ff; color: #5f2c82; font-weight: 600; }
        .table tbody tr { background: #fff; }
        .table tbody tr:nth-child(even) { background: #f8f9fa; }
        .table td, .table th { vertical-align: middle; }
        @media (max-width: 991px) { .sidebar { border-radius: 0 0 32px 32px; min-height: auto; padding: 1.5rem 0.7rem; } .main-content { padding: 1.5rem 0.5rem; } .summary-cards { flex-direction: column; gap: 1.2rem; } }
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
                <a href="decrypt.php" class="nav-link"><i class="bi bi-unlock"></i><span>Decrypt Files</span></a>
                <a href="profile.php" class="nav-link"><i class="bi bi-person"></i><span>Profile</span></a>
                <a href="report.php" class="nav-link active"><i class="bi bi-bar-chart"></i><span>Report</span></a>
                <a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i><span>Logout</span></a>
            </nav>
            <!-- Main Content -->
            <main class="col-lg-9 col-md-8 main-content">
                <div class="report-container">
                    <h2 class="mb-4" style="color:#5f2c82;font-weight:700;">Report Table</h2>
                    <div class="summary-cards">
                        <div class="summary-card">
                            <div class="icon"><i class="bi bi-lock"></i></div>
                            <div class="count"><?php echo $total_encrypt; ?></div>
                            <div class="label">Total Encrypted Files</div>
                        </div>
                        <div class="summary-card">
                            <div class="icon"><i class="bi bi-unlock"></i></div>
                            <div class="count"><?php echo $total_decrypt; ?></div>
                            <div class="label">Total Decrypted Files</div>
                        </div>
                        <div class="summary-card">
                            <div class="icon"><i class="bi bi-people"></i></div>
                            <div class="count"><?php echo $total_users; ?></div>
                            <div class="label">Total Users</div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User Email</th>
                                    <th>Action</th>
                                    <th>Details</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($rows)): ?>
                                    <tr><td colspan="5" class="text-center text-muted">No report data found.</td></tr>
                                <?php else: foreach ($rows as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td><?php echo htmlspecialchars($row['action']); ?></td>
                                        <td><?php echo htmlspecialchars($row['details']); ?></td>
                                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html> 