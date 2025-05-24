<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Secure Encryption</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            min-height: 100vh;
            background-image: radial-gradient(circle at 80% 10%, #e0e7ff 10%, transparent 70%), radial-gradient(circle at 20% 90%, #f3e8ff 10%, transparent 70%);
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 22px;
            box-shadow: 0 6px 32px rgba(90, 60, 130, 0.10);
            padding: 2.7rem 2.2rem 2.2rem 2.2rem;
            max-width: 410px;
            width: 100%;
            border: 1.5px solid #ece6fa;
            position: relative;
            overflow: hidden;
        }
        .login-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 22px;
            z-index: 0;
            pointer-events: none;
            background: linear-gradient(120deg, #6a82fb22 0%, #5f2c8222 100%);
        }
        .login-card > * { position: relative; z-index: 1; }
        .form-label {
            color: #5f2c82;
            font-weight: 500;
        }
        .btn-gradient {
            background: linear-gradient(90deg, #6a82fb 0%, #5f2c82 100%);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 0.65rem 2.2rem;
            border-radius: 30px;
            transition: box-shadow 0.2s, transform 0.2s;
            letter-spacing: 0.5px;
        }
        .btn-gradient:hover {
            box-shadow: 0 4px 24px 0 rgba(90, 60, 130, 0.12);
            color: #fff;
            transform: translateY(-2px) scale(1.03);
        }
        .metamask-btn {
            background: #fff;
            border: 1.5px solid #6a82fb;
            color: #5f2c82;
            font-weight: 500;
            border-radius: 30px;
            padding: 0.5rem 1.5rem;
            margin-bottom: 1rem;
            transition: background 0.2s, color 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px rgba(106,130,251,0.07);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.08rem;
        }
        .metamask-btn:hover {
            background: #f3f0ff;
            color: #5f2c82;
            box-shadow: 0 4px 16px rgba(106,130,251,0.13);
        }
        .metamask-btn.connected {
            background: linear-gradient(90deg, #6a82fb 0%, #5f2c82 100%);
            color: #fff;
            border: none;
            box-shadow: 0 4px 16px rgba(106,130,251,0.13);
        }
        .brand-logo {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6a82fb 0%, #5f2c82 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.1rem auto;
            box-shadow: 0 2px 12px rgba(106,130,251,0.10);
        }
        .footer {
            margin-top: 3rem;
            padding: 1.5rem 0 0.5rem 0;
            background: #f8f9fa;
            border-top: 1px solid #eee;
            text-align: center;
            color: #888;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="text-center mb-4">
                <i class="bi bi-shield-lock" style="font-size:2.5rem;color:#6a82fb;"></i>
                <h3 class="mt-2 mb-1" style="color:#5f2c82;font-weight:700;">Login</h3>
                <p class="text-muted mb-0">Access your encrypted files securely</p>
            </div>
            <form id="loginForm" method="POST" action="login_process.php">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <button type="button" id="connectMetamask" class="metamask-btn w-100">
                        <i class="bi bi-wallet2 me-2"></i>Connect MetaMask
                    </button>
                    <input type="hidden" id="metamask_address" name="metamask_address" required>
                    <div id="metamaskStatus" class="form-text text-success d-none">MetaMask Connected</div>
                </div>
                <button type="submit" class="btn btn-gradient w-100">Login</button>
            </form>
            <div class="text-center mt-3">
                <span class="text-muted">Don't have an account?</span>
                <a href="signup.php" style="color:#5f2c82;font-weight:500;">Sign Up</a>
            </div>
        </div>
    </div>
    <!-- MetaMask Integration Script -->
    <script>
        const connectBtn = document.getElementById('connectMetamask');
        const metamaskInput = document.getElementById('metamask_address');
        const statusText = document.getElementById('metamaskStatus');
        connectBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            if (typeof window.ethereum !== 'undefined') {
                try {
                    const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
                    metamaskInput.value = accounts[0];
                    connectBtn.classList.add('connected');
                    connectBtn.innerHTML = '<i class="bi bi-wallet2 me-2"></i>' + accounts[0].slice(0,6) + '...' + accounts[0].slice(-4);
                    statusText.classList.remove('d-none');
                } catch (err) {
                    alert('MetaMask connection failed.');
                }
            } else {
                alert('MetaMask is not installed. Please install MetaMask and try again.');
            }
        });
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            if (!metamaskInput.value) {
                alert('Please connect your MetaMask wallet to continue.');
                e.preventDefault();
            }
        });
    </script>
</body>
</html> 