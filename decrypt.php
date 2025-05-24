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

    // Create decrypted directory if it doesn't exist
    if (! file_exists('decrypted')) {
        mkdir('decrypted', 0777, true);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_FILES['enc_file']) && $_FILES['enc_file']['error'] == 0) {
            try {
                // Get file info
                $enc_file = $_FILES['enc_file']['tmp_name'];

                // Read encrypted file in binary mode
                $handle   = fopen($enc_file, 'rb');
                $enc_data = '';
                while (! feof($handle)) {
                    $chunk = fread($handle, 8192);
                    $enc_data .= $chunk;
                }
                fclose($handle);

                // Extract IV and ciphertext
                $iv         = substr($enc_data, 0, 16);
                $ciphertext = substr($enc_data, 16);

                // Get encryption key from database
                $conn = new mysqli('localhost', 'root', '', 'image_encryption');
                if (! $conn->connect_error) {
                    $encryptedName = basename($_FILES['enc_file']['name']);
                    $stmt          = $conn->prepare('SELECT encryption_key, original_name, file_type FROM files WHERE encrypted_name = ? AND user_id = ?');
                    $userId        = $_SESSION['user_id'];
                    $stmt->bind_param('si', $encryptedName, $userId);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($row = $result->fetch_assoc()) {
                        $key          = $row['encryption_key'];
                        $originalName = $row['original_name'];
                        $fileType     = $row['file_type'];

                        // Decrypt the file
                        $decrypted = openssl_decrypt($ciphertext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

                        if ($decrypted !== false) {
                            // Get original file extension
                            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
                            if (empty($extension)) {
                                // If no extension in original name, try to determine from file type
                                switch ($fileType) {
                                    case 'application/pdf':
                                        $extension = 'pdf';
                                        break;
                                    case 'video/mp4':
                                        $extension = 'mp4';
                                        break;
                                    case 'video/quicktime':
                                        $extension = 'mov';
                                        break;
                                    case 'video/x-msvideo':
                                        $extension = 'avi';
                                        break;
                                    case 'image/jpeg':
                                        $extension = 'jpg';
                                        break;
                                    case 'image/png':
                                        $extension = 'png';
                                        break;
                                    case 'image/gif':
                                        $extension = 'gif';
                                        break;
                                    case 'application/msword':
                                        $extension = 'doc';
                                        break;
                                    case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                                        $extension = 'docx';
                                        break;
                                    default:
                                        $extension = 'bin';
                                }
                            }

                            // Save decrypted file with original extension in binary mode
                            $filename = 'decrypted/' . pathinfo($originalName, PATHINFO_FILENAME) . '_decrypted.' . $extension;
                            $handle   = fopen($filename, 'wb');
                            fwrite($handle, $decrypted);
                            fclose($handle);

                            // Log the decryption
                            $stmt    = $conn->prepare('INSERT INTO report (user_id, action, details) VALUES (?, "decrypt", ?)');
                            $details = "Decrypted file: " . $originalName;
                            $stmt->bind_param('is', $userId, $details);
                            $stmt->execute();

                            $success = "<h5 class='text-success'>Decryption Successful</h5>
                                  <p class='text-muted'>Your file has been decrypted successfully.</p>
                                  <a href='$filename' class='btn btn-success mt-2' download>⬇️ Download Decrypted File</a>";
                        } else {
                            $error = 'Decryption failed. The file may be corrupted or the wrong key was used.';
                        }
                    } else {
                        $error = 'File not found in database or you do not have permission to decrypt it.';
                    }
                    $stmt->close();
                    $conn->close();
                } else {
                    $error = 'Database connection failed.';
                }
            } catch (Exception $e) {
                $error = 'Decryption failed: ' . $e->getMessage();
            }
        } else {
            $error = 'Upload failed! Please select a valid encrypted file (.enc).';
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Decrypt File - Secure Encryption</title>
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
        .decrypt-container { max-width: 480px; margin: 0 auto; }
        .decrypt-card {
            background: #fff;
            border-radius: 22px;
            box-shadow: 0 6px 32px rgba(90, 60, 130, 0.10);
            padding: 2.5rem 2.2rem 2.2rem 2.2rem;
        }
        .decrypt-icon {
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
                <a href="encrypt.php" class="nav-link"><i class="bi bi-lock"></i><span>Encrypt Files</span></a>
                <a href="decrypt.php" class="nav-link active"><i class="bi bi-unlock"></i><span>Decrypt Files</span></a>
                <a href="profile.php" class="nav-link"><i class="bi bi-person"></i><span>Profile</span></a>
                <a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i><span>Logout</span></a>
            </nav>
            <!-- Main Content -->
            <main class="col-lg-9 col-md-8 main-content">
                <div class="decrypt-container">
                    <div class="decrypt-card">
                        <div class="text-center mb-4">
                            <i class="bi bi-unlock decrypt-icon"></i>
                            <h3 class="mb-2" style="color:#5f2c82;font-weight:700;">Decrypt File</h3>
                            <p class="text-muted mb-0">Upload an encrypted file (.enc) to decrypt it securely.</p>
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
                                    <label class="form-label" style="color:#5f2c82;font-weight:500;">Select Encrypted File</label>
                                    <input type="file" class="form-control" name="enc_file" accept=".enc" required>
                                </div>
                                <button type="submit" class="btn btn-gradient w-100"> <i class="bi bi-unlock me-2"></i>Decrypt Now</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>