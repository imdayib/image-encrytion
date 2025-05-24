<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Encryption - Welcome</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            color: #222;
        }
        .navbar {
            background: #fff;
            box-shadow: 0 2px 8px rgba(90, 60, 130, 0.04);
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: #5f2c82 !important;
        }
        .hero-section {
            min-height: 60vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding-top: 4rem;
            padding-bottom: 2rem;
        }
        .hero-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #5f2c82;
        }
        .hero-desc {
            font-size: 1.15rem;
            margin-bottom: 2.5rem;
            color: #444;
        }
        .btn-gradient {
            background: linear-gradient(90deg, #6a82fb 0%, #5f2c82 100%);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 0.65rem 2.2rem;
            border-radius: 30px;
            margin: 0 0.5rem;
            transition: box-shadow 0.2s;
        }
        .btn-gradient:hover {
            box-shadow: 0 4px 24px 0 rgba(90, 60, 130, 0.12);
            color: #fff;
        }
        .features {
            margin-top: 2rem;
            display: flex;
            justify-content: center;
            gap: 2.5rem;
        }
        .feature-box {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 2px 12px rgba(90, 60, 130, 0.06);
            padding: 2rem 1.5rem;
            min-width: 220px;
            text-align: center;
        }
        .feature-icon {
            font-size: 2.2rem;
            color: #6a82fb;
            margin-bottom: 0.7rem;
        }
        @media (max-width: 900px) {
            .features {
                flex-direction: column;
                align-items: center;
                gap: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">Secure Encryption</a>
            <div class="ms-auto">
                <a href="login.php" class="btn btn-outline-primary me-2">Login</a>
                <a href="signup.php" class="btn btn-primary">Sign Up</a>
            </div>
        </div>
    </nav>
    <div class="container hero-section">
        <h1 class="hero-title">Secure File Encryption</h1>
        <p class="hero-desc">Secure. Private. Fast.<br>Protect your sensitive files with advanced encryption and seamless access.<br>Trusted by professionals for robust data privacy.</p>
        <div class="mb-4">
            <a href="login.php" class="btn btn-gradient me-2">Login</a>
            <a href="signup.php" class="btn btn-outline-secondary ms-2">Sign Up</a>
        </div>
        <div class="features">
            <div class="feature-box">
                <i class="bi bi-shield-lock feature-icon"></i>
                <h5 class="mb-2">Secure Authentication</h5>
                <p class="mb-0">Multi-layered login with MetaMask wallet integration.</p>
            </div>
            <div class="feature-box">
                <i class="bi bi-file-earmark-lock2 feature-icon"></i>
                <h5 class="mb-2">File Protection</h5>
                <p class="mb-0">Advanced encryption keeps your files safe and private.</p>
            </div>
            <div class="feature-box">
                <i class="bi bi-wallet2 feature-icon"></i>
                <h5 class="mb-2">Web3 Ready</h5>
                <p class="mb-0">Seamless MetaMask wallet support for next-gen security.</p>
            </div>
        </div>
    </div>
    <!-- How it Works Section -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="p-4 p-md-5 bg-white rounded-4 shadow-lg mb-4" style="background:rgba(255,255,255,0.98);">
                    <h3 class="mb-4 text-center" style="color:#5f2c82; font-weight:600; letter-spacing:0.5px;">How It Works</h3>
                    <div class="row g-4 align-items-stretch">
                        <div class="col-md-4">
                            <div class="h-100 d-flex flex-column align-items-center justify-content-center px-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;background:linear-gradient(135deg,#6a82fb22,#5f2c8222);">
                                    <i class="bi bi-person-check" style="font-size:2rem;color:#6a82fb;"></i>
                                </div>
                                <h6 class="mb-2" style="font-weight:600;">1. Create Your Account</h6>
                                <p class="small text-muted mb-0">Sign up securely with your email and MetaMask wallet.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="h-100 d-flex flex-column align-items-center justify-content-center px-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;background:linear-gradient(135deg,#6a82fb22,#5f2c8222);">
                                    <i class="bi bi-lock" style="font-size:2rem;color:#6a82fb;"></i>
                                </div>
                                <h6 class="mb-2" style="font-weight:600;">2. Encrypt & Protect</h6>
                                <p class="small text-muted mb-0">Upload and encrypt your files instantly with advanced security.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="h-100 d-flex flex-column align-items-center justify-content-center px-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;background:linear-gradient(135deg,#6a82fb22,#5f2c8222);">
                                    <i class="bi bi-unlock" style="font-size:2rem;color:#6a82fb;"></i>
                                </div>
                                <h6 class="mb-2" style="font-weight:600;">3. Access Anytime</h6>
                                <p class="small text-muted mb-0">Easily decrypt and access your files whenever you need them.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="mt-5 pt-4 pb-3 bg-light border-top">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="text-muted small mb-2 mb-md-0">
                &copy; <?php echo date('Y'); ?> Secure Encryption. All rights reserved.
            </div>
            <div>
                <a href="login.php" class="text-decoration-none me-3" style="color:#5f2c82;">Login</a>
                <a href="signup.php" class="text-decoration-none" style="color:#5f2c82;">Sign Up</a>
            </div>
        </div>
    </footer>
</body>
</html>