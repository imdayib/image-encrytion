<?php
    session_start();
    if (! isset($_SESSION['email'])) {
        header("Location: index.php");
        exit();
    }

    $email   = $_SESSION['email'];
    $username = ucfirst(strtolower(explode('@', $email)[0]));
    $role    = $_SESSION['role'];
    $avatar  = strtoupper($email[0]);
    $success = '';
    $error   = '';

    // Create encrypted directory if it doesn't exist
    if (! file_exists('encrypted')) {
        mkdir('encrypted', 0777, true);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                                // Generate a secure encryption key
        $key = bin2hex(random_bytes(32));       // 64 characters for AES-256
        $iv  = openssl_random_pseudo_bytes(16); // 16-byte IV for AES-256-CBC

        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $file         = $_FILES['file'];
            $fileType     = $file['type'];
            $fileSize     = $file['size'];
            $originalName = $file['name'];

            // Validate file type
            $allowedTypes = [
                'image/jpeg', 'image/png', 'image/gif',
                'video/mp4', 'video/quicktime', 'video/x-msvideo',
                'application/pdf',
                'application/msword',                                                      // .doc
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
            ];
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'mov', 'avi', 'pdf', 'doc', 'docx'];
            $ext               = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

            if (! in_array($ext, $allowedExtensions)) {
                $error = 'Invalid file type. Please upload an image, video, or PDF. doc and docx files are also allowed.';
            } elseif ($fileSize > 100 * 1024 * 1024) { // 100MB limit
                $error = 'File size too large. Maximum size is 100MB.';
            } else {
                try {
                    // Read file in binary mode
                    $handle = fopen($file['tmp_name'], 'rb');
                    $data   = '';

                    // Read file in chunks to handle large files
                    while (! feof($handle)) {
                        $chunk = fread($handle, 8192); // Read 8KB at a time
                        $data .= $chunk;
                    }
                    fclose($handle);

                    // Encrypt the entire file at once to maintain binary integrity
                    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

                    // Combine IV and encrypted data
                    $output = $iv . $encrypted;

                    // Generate unique filename
                    $encryptedName = uniqid('file_', true) . '.enc';
                    $filename      = 'encrypted/' . $encryptedName;

                    // Save encrypted file in binary mode
                    $handle = fopen($filename, 'wb');
                    fwrite($handle, $output);
                    fclose($handle);

                    // Save to database
                    $conn = new mysqli('localhost', 'root', '', 'image_encryption');
                    if (! $conn->connect_error) {
                        $stmt   = $conn->prepare('INSERT INTO files (user_id, original_name, encrypted_name, file_type, file_size, encryption_key) VALUES (?, ?, ?, ?, ?, ?)');
                        $userId = $_SESSION['user_id'];
                        $stmt->bind_param('isssis', $userId, $originalName, $encryptedName, $fileType, $fileSize, $key);
                        $stmt->execute();

                        // Log the encryption
                        $stmt    = $conn->prepare('INSERT INTO report (user_id, action, details) VALUES (?, "encrypt", ?)');
                        $details = "Encrypted file: " . $originalName;
                        $stmt->bind_param('is', $userId, $details);
                        $stmt->execute();

                        $stmt->close();
                        $conn->close();
                    }

                    $success = "<h5 class='text-success'>Encryption Successful</h5>
                           <p class='text-muted'>Your file has been encrypted securely.</p>
                           <a href='$filename' class='btn btn-success mt-2' download>⬇️ Download Encrypted File</a>";
                } catch (Exception $e) {
                    $error = 'Encryption failed: ' . $e->getMessage();
                }
            }
        } else {
            $error = 'Upload failed! Please select a valid file (image, video, or PDF).';
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encrypt File - Secure Encryption</title>
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
        .encrypt-container { max-width: 480px; margin: 0 auto; }
        .encrypt-card {
            background: #fff;
            border-radius: 22px;
            box-shadow: 0 6px 32px rgba(90, 60, 130, 0.10);
            padding: 2.5rem 2.2rem 2.2rem 2.2rem;
        }
        .encrypt-icon {
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
                <a href="encrypt.php" class="nav-link active"><i class="bi bi-lock"></i><span>Encrypt Files</span></a>
                <a href="decrypt.php" class="nav-link"><i class="bi bi-unlock"></i><span>Decrypt Files</span></a>
                <a href="profile.php" class="nav-link"><i class="bi bi-person"></i><span>Profile</span></a>
                <a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i><span>Logout</span></a>
            </nav>
            <!-- Main Content -->
            <main class="col-lg-9 col-md-8 main-content">
                <div class="encrypt-container">
                    <div class="encrypt-card">
                        <div class="text-center mb-4">
                            <i class="bi bi-lock encrypt-icon"></i>
                            <h3 class="mb-2" style="color:#5f2c82;font-weight:700;">Encrypt File</h3>
                            <p class="text-muted mb-0">Upload an image, video, or PDF to encrypt it securely.</p>
                        </div>
                        <?php if ($success): ?>
                            <div class="alert alert-success text-center"><?php echo $success; ?></div>
                            <div class="text-center mt-3"><a href="dashboard.php" class="btn btn-outline-primary">Back to Dashboard</a></div>
                        <?php else: ?>
<?php if ($error): ?>
                                <div class="alert alert-danger text-center"><?php echo $error; ?></div>
                            <?php endif; ?>
                            <form method="POST" enctype="multipart/form-data" autocomplete="off">
                                <div class="mb-3">
                                    <label class="form-label" style="color:#5f2c82;font-weight:500;">Select File</label>
                                    <input type="file" class="form-control" name="file" accept="image/*,video/*,application/pdf,application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document " required>
                                </div>
                                <button type="submit" class="btn btn-gradient w-100"> <i class="fbi bi-lock me-2"></i>Encrypt Now</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>