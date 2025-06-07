<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Secure Encryption</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            min-height: 100vh;
            background-image: radial-gradient(circle at 80% 10%, #e0e7ff 10%, transparent 70%), radial-gradient(circle at 20% 90%, #f3e8ff 10%, transparent 70%);
        }
        .signup-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .signup-card {
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
        .signup-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 22px;
            z-index: 0;
            pointer-events: none;
            background: linear-gradient(120deg, #6a82fb22 0%, #5f2c8222 100%);
        }
        .signup-card > * { position: relative; z-index: 1; }
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
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="signup-card">
            <div class="brand-logo mb-2">
                <i class="bi bi-person-plus" style="font-size:2rem;color:#fff;"></i>
            </div>
            <div class="text-center mb-4">
                <h3 class="mt-2 mb-1" style="color:#5f2c82;font-weight:700;">Sign Up</h3>
                <p class="text-muted mb-0">Create your account to start encrypting files</p>
            </div>
            <form id="signupForm" method="POST" action="signup_process.php">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <div class="mb-2 text-muted small" id="metamaskInstruction">You must connect your MetaMask wallet to sign up.</div>
                    <div id="metamaskConnectArea">
                        <button type="button" id="connectMetamask" class="metamask-btn w-100">
                            <span id="metamaskBtnContent"><i class="bi bi-wallet2 me-2"></i>Connect MetaMask</span>
                            <span id="metamaskBtnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                    <div id="metamaskConnectedArea" class="d-none">
                        <div class="d-flex align-items-center justify-content-between bg-light rounded-3 px-3 py-2 mb-2">
                            <span class="text-truncate" id="metamaskAddressDisplay" style="max-width: 200px; color:#5f2c82; font-weight:500;"></span>
                            <span class="badge bg-success ms-2"><i class="bi bi-check-circle me-1"></i>Connected</span>
                        </div>
                    </div>
                    <input type="hidden" id="metamask_address" name="metamask_address" required>
                    <div id="metamaskStatus" class="form-text text-danger d-none"></div>
                </div>
                <button type="submit" class="btn btn-gradient w-100">Sign Up</button>
            </form>
            <div class="text-center mt-3">
                <span class="text-muted">Already have an account?</span>
                <a href="login.php" style="color:#5f2c82;font-weight:500;">Login</a>
            </div>
        </div>
    </div>
    <!-- MetaMask Integration Script -->
    <script>
        const connectBtn = document.getElementById('connectMetamask');
        const metamaskInput = document.getElementById('metamask_address');
        const statusText = document.getElementById('metamaskStatus');
        const connectArea = document.getElementById('metamaskConnectArea');
        const connectedArea = document.getElementById('metamaskConnectedArea');
        const addressDisplay = document.getElementById('metamaskAddressDisplay');
        const btnContent = document.getElementById('metamaskBtnContent');
        const btnSpinner = document.getElementById('metamaskBtnSpinner');

        connectBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            statusText.classList.add('d-none');
            statusText.classList.remove('text-success');
            statusText.classList.add('text-danger');
            btnContent.classList.add('d-none');
            btnSpinner.classList.remove('d-none');
            if (typeof window.ethereum !== 'undefined') {
                try {
                    // Request account access if needed
                    const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
                    if (accounts && accounts[0]) {
                        metamaskInput.value = accounts[0];
                        addressDisplay.textContent = accounts[0].slice(0, 8) + '...' + accounts[0].slice(-6);
                        connectArea.classList.add('d-none');
                        connectedArea.classList.remove('d-none');
                        statusText.classList.add('d-none');
                        btnContent.classList.remove('d-none');
                        btnSpinner.classList.add('d-none');
                    } else {
                        statusText.textContent = 'MetaMask connection failed. No address returned.';
                        statusText.classList.remove('d-none');
                        btnContent.classList.remove('d-none');
                        btnSpinner.classList.add('d-none');
                    }
                } catch (err) {
                    statusText.textContent = 'MetaMask connection failed or was cancelled.';
                    statusText.classList.remove('d-none');
                    btnContent.classList.remove('d-none');
                    btnSpinner.classList.add('d-none');
                }
            } else {
                statusText.textContent = 'MetaMask is not installed. Please install MetaMask and try again.';
                statusText.classList.remove('d-none');
                btnContent.classList.remove('d-none');
                btnSpinner.classList.add('d-none');
            }
        });
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            if (!metamaskInput.value) {
                statusText.textContent = 'Please connect your MetaMask wallet to continue.';
                statusText.classList.remove('d-none');
                e.preventDefault();
            }
        });
    </script>
</body>
</html> 