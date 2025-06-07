<?php
    session_start();
    $email    = $_SESSION['email'];
    $username = ucfirst(strtolower(explode('@', $email)[0]));

    $role   = $_SESSION['role'] ?? 'User';
    $avatar = strtoupper($email[0]);

    // Check if user is logged in, if not redirect to login
    if (! isset($_SESSION['email'])) {
        header("Location: login.php");
        exit;
    }

    // Fetch report data and summary stats
    $conn          = new mysqli('localhost', 'root', '', 'image_encryption');
    $rows          = [];
    $total_encrypt = 0;
    $total_decrypt = 0;
    $total_users   = 0;

    if (! $conn->connect_error) {
        // For all users (both admin and regular), get their own stats
        $user_id = $_SESSION['user_id'] ?? 0;

        // Summary counts for the current user
        $res = $conn->prepare("SELECT COUNT(*) AS c FROM report WHERE action='encrypt' AND user_id=?");
        $res->bind_param("i", $user_id);
        $res->execute();
        $result = $res->get_result();
        if ($row = $result->fetch_assoc()) {
            $total_encrypt = $row['c'];
        }
        $res->close();

        $res = $conn->prepare("SELECT COUNT(*) AS c FROM report WHERE action='decrypt' AND user_id=?");
        $res->bind_param("i", $user_id);
        $res->execute();
        $result = $res->get_result();
        if ($row = $result->fetch_assoc()) {
            $total_decrypt = $row['c'];
        }
        $res->close();

        // Only get total users if admin
        if (strtolower($role) === 'admin') {
            $res = $conn->query("SELECT COUNT(*) AS c FROM users");
            if ($res && $row = $res->fetch_assoc()) {
                $total_users = $row['c'];
            }
        }

        // Table data - different queries for admin vs regular users
        if (strtolower($role) === 'admin') {
            $sql = "SELECT r.id, u.email, r.action, r.details, r.created_at
                    FROM report r JOIN users u ON r.user_id = u.id
                    ORDER BY r.created_at DESC";
        } else {
            $sql = "SELECT r.id, u.email, r.action, r.details, r.created_at
                    FROM report r JOIN users u ON r.user_id = u.id
                    WHERE r.user_id = $user_id
                    ORDER BY r.created_at DESC";
        }

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
    <title>Admin Reports - Secure Encryption</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        :root {
            --primary: #5f2c82;
            --secondary: #6a82fb;
            --accent: #fc5c7d;
            --light: #f8f9fa;
            --dark: #212529;
            --success: #28a745;
            --info: #17a2b8;
            --warning: #ffc107;
            --danger: #dc3545;
        }

        body {
            background: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #4a4a4a;
        }

        /* Sidebar */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary) 0%, #3a1c5a 100%);
            color: #fff;
            padding: 2rem 1.5rem;
            box-shadow: 0 0 30px rgba(0,0,0,0.1);
            position: relative;
            z-index: 10;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            margin-bottom: 2.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-brand-icon {
            font-size: 1.8rem;
            margin-right: 0.75rem;
            color: #fff;
        }

        .sidebar-brand-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0;
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
            padding: 1rem;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
        }

        .sidebar-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #fff 0%, #b3a1e6 100%);
            color: var(--primary);
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .sidebar-user-info {
            display: flex;
            flex-direction: column;
        }

        .sidebar-user-name {
            font-size: 1rem;
            font-weight: 600;
            color: #fff;
            line-height: 1.2;
            margin-bottom: 0.2rem;
        }

        .sidebar-user-role {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.7);
            background: rgba(0,0,0,0.2);
            padding: 0.2rem 0.5rem;
            border-radius: 20px;
            display: inline-block;
            width: fit-content;
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            font-weight: 500;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            font-size: 0.95rem;
            padding: 0.75rem 1rem;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .sidebar .nav-link i {
            font-size: 1.2rem;
            margin-right: 0.75rem;
            color: rgba(255,255,255,0.8);
            width: 24px;
            text-align: center;
        }

        .sidebar .nav-link.active i {
            color: #fff;
        }

        /* Main Content */
        .main-content {
            padding: 2rem;
            background: #f5f7fa;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e0e0e0;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 0;
        }

        /* Summary Cards */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
            justify-content: center;
        }

        .summary-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 1.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-left: 4px solid var(--primary);
            position: relative;
            overflow: hidden;
        }

        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .summary-card.encrypt {
            border-left-color: var(--success);
        }

        .summary-card.decrypt {
            border-left-color: var(--info);
        }

        .summary-card.users {
            border-left-color: var(--warning);
        }

        .summary-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .summary-card-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #fff;
        }

        .summary-card.encrypt .summary-card-icon {
            background: linear-gradient(135deg, var(--success) 0%, #1e7e34 100%);
        }

        .summary-card.decrypt .summary-card-icon {
            background: linear-gradient(135deg, var(--info) 0%, #117a8b 100%);
        }

        .summary-card.users .summary-card-icon {
            background: linear-gradient(135deg, var(--warning) 0%, #e0a800 100%);
        }

        .summary-card-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .summary-card-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0;
            line-height: 1;
        }

        .summary-card-footer {
            margin-top: 1rem;
            font-size: 0.85rem;
            color: #6c757d;
            display: flex;
            align-items: center;
        }

        .summary-card-footer i {
            margin-right: 0.5rem;
        }

        /* Data Table */
        .data-table-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .data-table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .data-table-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 0;
        }

        .table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }

        .table thead th {
            background: var(--primary);
            color: #fff;
            font-weight: 600;
            padding: 1rem 1.25rem;
            border: none;
            position: sticky;
            top: 0;
        }

        .table tbody tr {
            background: #fff;
            transition: background 0.2s ease;
        }

        .table tbody tr:hover {
            background: #f8f9fa;
        }

        .table td {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }

        .badge-encrypt {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success);
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .badge-decrypt {
            background: rgba(23, 162, 184, 0.1);
            color: var(--info);
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 0;
            color: #6c757d;
        }

        .empty-state-icon {
            font-size: 3rem;
            color: #dee2e6;
            margin-bottom: 1rem;
        }

        .empty-state-title {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }

        /* Filter active state */
        .filter-option.active {
            background-color: rgba(106, 130, 251, 0.1);
            color: var(--primary);
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .sidebar {
                min-height: auto;
                padding: 1.5rem;
                border-radius: 0;
            }

            .main-content {
                padding: 1.5rem;
            }
        }

        @media (max-width: 767.98px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .breadcrumb {
                margin-top: 0.5rem;
            }

            .summary-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar - Keep the same sidebar as before -->
           <nav class="col-lg-3 col-xl-2 sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-shield-lock sidebar-brand-icon"></i>
        <h1 class="sidebar-brand-title">Secure Encryption</h1>
    </div>

    <div class="sidebar-user">
        <div class="sidebar-avatar"><?php echo htmlspecialchars($avatar); ?></div>
        <div class="sidebar-user-info">
            <span class="sidebar-user-name"><?php echo htmlspecialchars($username); ?></span>
            <span class="sidebar-user-role"><?php echo htmlspecialchars(ucfirst($role)); ?></span>
        </div>
    </div>

    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link"><i class="bi bi-house-door"></i> Dashboard</a>
        </li>
        <li class="nav-item">
            <a href="encrypt.php" class="nav-link"><i class="bi bi-lock"></i> Encrypt Files</a>
        </li>
        <li class="nav-item">
            <a href="verify.php" class="nav-link"><i class="bi bi-shield-check"></i> Verify Integrity</a>
        </li>
        <li class="nav-item">
            <a href="decrypt.php" class="nav-link"><i class="bi bi-unlock"></i> Decrypt Files</a>
        </li>
        <li class="nav-item">
            <a href="profile.php" class="nav-link"><i class="bi bi-person"></i> Profile</a>
        </li>

        <?php if (strtolower($role) === 'admin'): ?>
            <!-- Admin-only menu items -->
            <li class="nav-item">
                <a href="report.php" class="nav-link active"><i class="bi bi-bar-chart"></i> System Reports</a>
            </li>
        
        <?php else: ?>
            <!-- Regular user menu items -->
            <li class="nav-item">
                <a href="report.php" class="nav-link active"><i class="bi bi-activity"></i> My Activity</a>
            </li>
        <?php endif; ?>

        <li class="nav-item mt-4">
            <a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </li>
    </ul>
</nav>

            <!-- Main Content -->
            <main class="col-lg-9 col-xl-10 main-content">
                <div class="page-header">
                    <h1 class="page-title">
                        <?php echo strtolower($role) === 'admin' ? 'System Reports' : 'My Activity'; ?>
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <?php echo strtolower($role) === 'admin' ? 'Reports' : 'My Activity'; ?>
                            </li>
                        </ol>
                    </nav>
                </div>

                <!-- Summary Cards -->
                <div class="summary-cards">
                    <div class="summary-card encrypt">
                        <div class="summary-card-header">
                            <div>
                                <h3 class="summary-card-title">My Encrypted Files</h3>
                                <p class="summary-card-value"><?php echo number_format($total_encrypt); ?></p>
                            </div>
                            <div class="summary-card-icon">
                                <i class="bi bi-lock"></i>
                            </div>
                        </div>
                        <div class="summary-card-footer">
                            <i class="bi bi-arrow-up-circle text-success"></i>
                            <span>Files I've encrypted</span>
                        </div>
                    </div>

                    <div class="summary-card decrypt">
                        <div class="summary-card-header">
                            <div>
                                <h3 class="summary-card-title">My Decrypted Files</h3>
                                <p class="summary-card-value"><?php echo number_format($total_decrypt); ?></p>
                            </div>
                            <div class="summary-card-icon">
                                <i class="bi bi-unlock"></i>
                            </div>
                        </div>
                        <div class="summary-card-footer">
                            <i class="bi bi-arrow-down-circle text-info"></i>
                            <span>Files I've decrypted</span>
                        </div>
                    </div>

                    <?php if (strtolower($role) === 'admin'): ?>
                    <div class="summary-card users">
                        <div class="summary-card-header">
                            <div>
                                <h3 class="summary-card-title">Active Users</h3>
                                <p class="summary-card-value"><?php echo number_format($total_users); ?></p>
                            </div>
                            <div class="summary-card-icon">
                                <i class="bi bi-people"></i>
                            </div>
                        </div>
                        <div class="summary-card-footer">
                            <i class="bi bi-person-plus text-warning"></i>
                            <span>Total registered users</span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>


                  <!-- Activity Log Table -->
                <div class="data-table-container">
                    <div class="data-table-header">
                          <h2 class="data-table-title">
                            <?php echo strtolower($role) === 'admin' ? 'All User Activities' : 'My Activities'; ?>
                        </h2>
                        <?php if (strtolower($role) === 'admin'): ?>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-funnel"></i> Filter
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                                <li><a class="dropdown-item filter-option active" href="#" data-filter="all">All Activities</a></li>
                                <li><a class="dropdown-item filter-option" href="#" data-filter="encrypt">Encryption Only</a></li>
                                <li><a class="dropdown-item filter-option" href="#" data-filter="decrypt">Decryption Only</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item filter-option" href="#" data-filter="7days">Last 7 Days</a></li>
                                <li><a class="dropdown-item filter-option" href="#" data-filter="30days">Last 30 Days</a></li>
                            </ul>
                        </div>
                          <?php endif; ?>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover" id="activityTable">
                            <thead>
                                <tr>
                            <th>ID</th>
                                                                <?php if (strtolower($role) === 'admin'): ?>
                                                                <th>User</th>
                                                                <?php endif; ?>
                                                                <th>Action</th>
                                                                <th>Details</th>
                                                                <th>Date & Time</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                <?php if (empty($rows)): ?>
                                    <tr>
                                        <td colspan="<?php echo strtolower($role) === 'admin' ? '5' : '4'; ?>">
                                            <div class="empty-state">
                                                <div class="empty-state-icon">
                                                    <i class="bi bi-database-exclamation"></i>
                                                </div>
                                                <h3 class="empty-state-title">No Activity Found</h3>
                                                <p>There are no activity logs to display at this time.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else:foreach ($rows as $row): ?>
		                                    <tr>
		                                        <td><?php echo htmlspecialchars($row['id']); ?></td>
	                                            <?php if (strtolower($role) === 'admin'): ?>

		                                        <td>
		                                            <div class="d-flex align-items-center">
		                                                <div class="avatar-sm me-2">
		                                                    <div class="avatar-title bg-light rounded-circle text-primary">
		                                                        <?php echo strtoupper(substr($row['email'], 0, 1)); ?>
		                                                    </div>
		                                                </div>
		                                                <div>
		                                                    <div class="fw-semibold"><?php echo htmlspecialchars(explode('@', $row['email'])[0]); ?></div>
		                                                    <div class="text-muted small"><?php echo htmlspecialchars($row['email']); ?></div>
		                                                </div>
		                                            </div>
		                                        </td>
	                                            <?php endif; ?>
	                                        <td>
	                                            <span class="<?php echo $row['action'] === 'encrypt' ? 'badge-encrypt' : 'badge-decrypt'; ?>">
	                                                <i class="bi	                                                            	                                                             <?php echo $row['action'] === 'encrypt' ? 'bi-lock' : 'bi-unlock'; ?> me-1"></i>
	                                                <?php echo htmlspecialchars(ucfirst($row['action'])); ?>
	                                            </span>
	                                        </td>
	                                        <td><?php echo htmlspecialchars($row['details']); ?></td>
	                                        <td>
	                                            <div class="text-nowrap">
	                                                <?php
                                                        $date = new DateTime($row['created_at']);
                                                        echo $date->format('M j, Y');
                                                    ?>
	                                                <div class="text-muted small">
	                                                    <?php echo $date->format('h:i A'); ?>
	                                                </div>
	                                            </div>
	                                        </td>
	                                    </tr>
	                                <?php endforeach;endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!--filter make   -->
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#activityTable').DataTable({
            responsive: true,
            order: [[0, 'desc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search activities...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
                zeroRecords: "No matching records found",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        });

        // Filter functionality
         <?php if (strtolower($role) === 'admin'): ?>
        $('.filter-option').on('click', function(e) {
            e.preventDefault();
            var filterType = $(this).data('filter');

            // Update active state
            $('.filter-option').removeClass('active');
            $(this).addClass('active');

            // Update dropdown button text
            $('#filterDropdown').html('<i class="bi bi-funnel"></i> ' + $(this).text());

            // Clear all filters first
            $.fn.dataTable.ext.search = [];
            table.search('').columns().search('').draw();

            // Apply the selected filter
            switch(filterType) {
                case 'all':
                    // No additional filtering needed
                    break;

                case 'encrypt':
                    table.column(2).search('encrypt').draw();
                    break;

                case 'decrypt':
                    table.column(2).search('decrypt').draw();
                    break;

                case '7days':
                    filterByDate(7);
                    break;

                case '30days':
                    filterByDate(30);
                    break;
            }
        });

        // Helper function for date filtering
        function filterByDate(days) {
            // Clear any existing filters
            $.fn.dataTable.ext.search = [];

            // Calculate cutoff date
            var cutoffDate = new Date();
            cutoffDate.setDate(cutoffDate.getDate() - days);
            cutoffDate.setHours(0, 0, 0, 0); // Set to start of day

            // Add custom filtering function
            $.fn.dataTable.ext.search.push(

                function(settings, data, dataIndex) {
                    // Get the date string from the cell (5th column, index 4)
                    // var dateStr = table.cell(dataIndex, 4).data();
                    var dateStr = table.cell(dataIndex,                                                        <?php echo strtolower($role) === 'admin' ? '4' : '3'; ?>).data();

                    // Parse the date from the table cell
                    // Expected format: "Jun 2, 2023 02:30 PM"
                    var dateParts = dateStr.trim().split(/\s+/);
                    if (dateParts.length < 4) return false;

                    var month = dateParts[0];
                    var day = dateParts[1].replace(',', '');
                    var year = dateParts[2];
                    var time = dateParts[3] + ' ' + dateParts[4];

                    // Create a Date object from the parsed values
                    var rowDate = new Date(month + ' ' + day + ', ' + year + ' ' + time);

                    // Compare dates
                    return rowDate >= cutoffDate;
                }
            );

            // Apply the filter
            table.draw();
        }
          <?php endif; ?>
    });
</script>
</body>
</html>