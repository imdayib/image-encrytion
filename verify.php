<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['email'];
$username = ucfirst(strtolower(explode('@', $email)[0]));
$role = $_SESSION['role'];
$avatar = strtoupper($email[0]);
$success = '';
$error = '';

// Database connection
$conn = new mysqli('localhost', 'root', '', 'image_encryption');
if ($conn->connect_error) {
    $error = "Database connection failed: " . $conn->connect_error;
}

// Get user ID
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$userId = $user['id'];
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $file = $_FILES['file'];
        $tempPath = $file['tmp_name'];
        
        // Calculate current file hash
        $currentHash = hash_file('sha256', $tempPath);
        
        // Get original file info from database
        $originalName = $file['name'];
        $stmt = $conn->prepare("SELECT * FROM files WHERE user_id = ? AND encrypted_name = ?");
        $stmt->bind_param('is', $userId, $originalName);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $fileData = $result->fetch_assoc();
            $originalHash = $fileData['file_hash'];
            
            // Compare hashes
            if ($currentHash === $originalHash) {
                $success = "<h5 class='text-success'>File Integrity Verified</h5>
                           <p class='text-muted'>This file has NOT been tampered with.</p>
                           <div class='mt-3'>
                               <p><strong>Original Name:</strong> {$fileData['original_name']}</p>
                               <p><strong>File Type:</strong> {$fileData['file_type']}</p>
                               <p><strong>Encrypted On:</strong> {$fileData['created_at']}</p>
                           </div>";
                
                // Log verification
                $stmt = $conn->prepare("INSERT INTO report (user_id, action, details) VALUES (?, 'verify', ?)");
                $details = "Verified file integrity: " . $originalName . " (Result: Untampered)";
                $stmt->bind_param('is', $userId, $details);
                $stmt->execute();
                $stmt->close();
            } else {
                $error = "<h5 class='text-danger'>File Tampering Detected!</h5>
                         <p class='text-muted'>This file has been modified since encryption.</p>
                         <div class='mt-3'>
                             <p><strong>Original Hash:</strong> <code>{$originalHash}</code></p>
                             <p><strong>Current Hash:</strong> <code>{$currentHash}</code></p>
                         </div>";
                
                // Log verification
                $stmt = $conn->prepare("INSERT INTO report (user_id, action, details) VALUES (?, 'verify', ?)");
                $details = "Verified file integrity: " . $originalName . " (Result: Tampered)";
                $stmt->bind_param('is', $userId, $details);
                $stmt->execute();
                $stmt->close();
            }
        } else {
            $error = "File not found in database. This file was not encrypted through our system.";
        }
    } else {
        $error = "Please select a valid file to verify.";
    }
}

// Get user's files for dropdown
$stmt = $conn->prepare("SELECT encrypted_name, original_name FROM files WHERE user_id = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$userFiles = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify File Integrity - Secure Encryption</title>
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
        .main-content {
            padding: 2.5rem 2rem;
        }
        .verify-container { max-width: 480px; margin: 0 auto; }
        .verify-card {
            background: #fff;
            border-radius: 22px;
            box-shadow: 0 6px 32px rgba(90, 60, 130, 0.10);
            padding: 2.5rem 2.2rem 2.2rem 2.2rem;
        }
        .verify-icon {
            font-size: 2.5rem;
            color: #6a82fb;
            margin-bottom: 1rem;
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
        .file-selector {
            margin-bottom: 1.5rem;
        }
        .file-selector select {
            width: 100%;
            padding: 0.5rem;
            border-radius: 8px;
            border: 1px solid #ddd;
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
                <a href="verify.php" class="nav-link active"><i class="bi bi-shield-check"></i><span>Verify Integrity</span></a>
                <a href="decrypt.php" class="nav-link"><i class="bi bi-unlock"></i><span>Decrypt Files</span></a>
                <a href="profile.php" class="nav-link"><i class="bi bi-person"></i><span>Profile</span></a>
                <a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i><span>Logout</span></a>
            </nav>
            <!-- Main Content -->
            <main class="col-lg-9 col-md-8 main-content">
                <div class="verify-container">
                    <div class="verify-card">
                        <div class="text-center mb-4">
                            <i class="bi bi-shield-check verify-icon"></i>
                            <h3 class="mb-2" style="color:#5f2c82;font-weight:700;">Verify File Integrity</h3>
                            <p class="text-muted mb-0">Check if your encrypted file has been tampered with</p>
                        </div>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                            <div class="text-center mt-3"><a href="verify.php" class="btn btn-outline-primary">Verify Another File</a></div>
                        <?php elseif ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                            <div class="text-center mt-3"><a href="verify.php" class="btn btn-outline-primary">Try Again</a></div>
                        <?php else: ?>
                            <form method="POST" enctype="multipart/form-data" autocomplete="off">
                                <div class="mb-3">
                                    <label class="form-label" style="color:#5f2c82;font-weight:500;">Select Encrypted File</label>
                                    <input type="file" class="form-control" name="file" required>
                                </div>
                                <!-- <?php if (!empty($userFiles)): ?>
                                    <div class="file-selector">
                                        <label class="form-label" style="color:#5f2c82;font-weight:500;">Or select from your files:</label>
                                        <select onchange="document.querySelector('input[type=file]').value = this.value">
                                            <option value="">Select a file...</option>
                                            <?php foreach ($userFiles as $file): ?>
                                                <option value="<?php echo htmlspecialchars($file['encrypted_name']); ?>">
                                                    <?php echo htmlspecialchars($file['original_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                <?php endif; ?> -->
                                <button type="submit" class="btn btn-gradient w-100"> <i class="bi bi-shield-check me-2"></i>Verify Now</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>