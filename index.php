<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Encryption - Welcome</title>
    <meta name="description" content="Secure Encryption App: Encrypt and protect your files with advanced blockchain-based security.">
    <link rel="icon" href="images/favicon.png" type="image/png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
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
            min-height: 70vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding-top: 4rem;
            padding-bottom: 2rem;
        }
        .hero-title {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #5f2c82;
        }
       .hero-desc {
    font-size: 0.15rem;
    margin-bottom: 0.5rem;
    background: linear-gradient(90deg, #6a82fb 0%, #5f2c82 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text; /* For Firefox */
    color: transparent;
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

.info-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    padding: 2rem;
    transition: transform 0.2s ease;
}
.info-card:hover {
    transform: translateY(-5px);
}


        .btn-gradient:hover {
            box-shadow: 0 4px 24px 0 rgba(90, 60, 130, 0.12);
            color: #fff;
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
        .footer-links a {
            color: #5f2c82;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="#">Secure Encryption</a>
        <div class="ms-auto">
            <a href="login.php" class="btn btn-outline-primary me-2">Login</a>
            <a href="signup.php" class="btn btn-primary">Sign Up</a>
        </div>
    </div>
</nav>

<section class="container hero-section ">
    <!-- <img src="images/encryption-illustration.svg" alt="Encryption Illustration" class="img-fluid mb-4" style="max-width: 400px;"> -->
    <h1 class="hero-title">Secure File Encryption</h1>
    <span class="hero-desc"><h3> Secure. Private. Fast. </h3></span> <P><br>Protect your sensitive files with advanced encryption.<br>Trusted by professionals for robust data privacy.</p>
    <div class="mb-4">
        <a href="login.php" class="btn btn-gradient me-2">Login</a>
        <a href="signup.php" class="btn btn-outline-secondary ms-2">Sign Up</a>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container text-center">
        <h3 class="mb-5" style="color:#5f2c82;">Core Features</h3>
        <div class="row justify-content-center g-4">
            <div class="col-md-4" data-aos="fade-up">
                <div class="feature-box">
                    <i class="bi bi-shield-lock feature-icon"></i>
                    <h5>Secure Authentication</h5>
                    <p>Multi-layered login with MetaMask integration.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-box">
                    <i class="bi bi-file-earmark-lock2 feature-icon"></i>
                    <h5>File Protection</h5>
                    <p>Advanced encryption ensures privacy and safety.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-box">
                    <i class="bi bi-wallet2 feature-icon"></i>
                    <h5>Web3 Ready</h5>
                    <p>Seamless MetaMask wallet support for next-gen security.</p>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="py-5 bg-light">
    <div class="container text-center">
        <h3 class="mb-5" style="color:#5f2c82;">How It Works</h3>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="info-card">
                    <i class="bi bi-person-check feature-icon"></i>
                    <h6>Create Your Account</h6>
                    <p>Sign up securely with your email and MetaMask wallet.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-card">
                    <i class="bi bi-lock feature-icon"></i>
                    <h6>Encrypt & Protect</h6>
                    <p>Upload and encrypt your files instantly.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-card">
                    <i class="bi bi-unlock feature-icon"></i>
                    <h6>Access Anytime</h6>
                    <p>Decrypt and access your files securely whenever needed.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container text-center">
        <h3 class="mb-5" style="color:#5f2c82;">Why Choose Secure Encryption?</h3>
        <div class="row g-4">
            <div class="col-md-3">
              <div class="info-card">
    <i class="bi bi-shield-lock fs-2 text-primary"></i>
    <h6 class="mt-2">Strong & Safe </h6>

</div>

            </div>
            <div class="col-md-3">
                <div class="info-card">
                    <i class="bi bi-cloud-upload fs-2 text-primary"></i>
                    <h6 class="mt-2">Fast Uploads</h6>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-card">
                    <i class="bi bi-person-lock fs-2 text-primary"></i>
                    <h6 class="mt-2">Web3 Logins</h6>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-card">
                    <i class="bi bi-speedometer2 fs-2 text-primary"></i>
                    <h6 class="mt-2">Quick Access</h6>
                </div>
            </div>
        </div>
    </div>
</section>



<section class="py-5 text-white" style="background: linear-gradient(90deg, #6a82fb 0%, #5f2c82 100%);">
    <div class="container text-center">
        <h2 class="mb-3">Start Securing Your Files Today</h2>
        <p class="mb-4">Your privacy matters. Encrypt with confidence using Secure Encryption.</p>
        <a href="signup.php" class="btn btn-light px-4 py-2 fw-bold">Get Started</a>
    </div>
</section>

<footer class="mt-5 pt-4 pb-3 bg-light border-top">
    <div class="container d-flex flex-column flex-md-row justify-content-center align-items-center">
        <div class="text-muted small mb-2 mb-md-0">
            &copy;                                                                                                                                                                                                                                                                               <?php echo date('Y'); ?> Secure Encryption. All rights reserved.
        </div>

    </div>
</footer>

<!-- JS Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init();</script>
</body>
</html>
