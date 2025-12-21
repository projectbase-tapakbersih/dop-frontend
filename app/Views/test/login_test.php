<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Login - API Integration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .response-box {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 15px;
            border-radius: 5px;
            max-height: 500px;
            overflow-y: auto;
            font-family: 'Courier New', monospace;
            font-size: 12px;
        }
        .success { color: #28a745; }
        .error { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">🧪 Test Login API</h4>
                    </div>
                    <div class="card-body">
                        <div id="alert-container" class="mb-3"></div>
                        
                        <form id="testLoginForm">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="tintacreav@gmail.com" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" value="password123" required>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-play-fill"></i> Test Login
                                </button>
                                <a href="<?= base_url('test/api') ?>" class="btn btn-secondary">
                                    Test All APIs
                                </a>
                                <a href="<?= base_url('test/session') ?>" class="btn btn-info">
                                    View Session
                                </a>
                                <a href="<?= base_url('/') ?>" class="btn btn-outline-primary">
                                    Back to Home
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">📊 API Response</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="response-box" id="response-output">
                            <p class="text-muted">Waiting for response...</p>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">ℹ️ Debug Info</h6>
                    </div>
                    <div class="card-body">
                        <small>
                            <strong>API URL:</strong> <?= getenv('API_BASE_URL') ?><br>
                            <strong>Endpoint:</strong> /auth/login<br>
                            <strong>Method:</strong> POST
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('testLoginForm');
        const output = document.getElementById('response-output');
        const alertContainer = document.getElementById('alert-container');

        function showAlert(message, type = 'info') {
            alertContainer.innerHTML = `
                <div class="alert alert-${type} alert-dismissible fade show">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            output.innerHTML = '<p class="text-warning">🔄 Loading...</p>';
            alertContainer.innerHTML = '';
            
            const formData = new FormData(form);
            
            try {
                const response = await fetch('<?= base_url('test/login') ?>', {
                    method: 'POST',
                    body: new URLSearchParams(formData)
                });
                
                const result = await response.json();
                
                // Format response beautifully
                let formattedOutput = '<div>';
                formattedOutput += '<p class="mb-2"><strong>HTTP Status:</strong> ' + response.status + '</p>';
                formattedOutput += '<p class="mb-2"><strong>Success:</strong> <span class="' + (result.success ? 'success' : 'error') + '">' + result.success + '</span></p>';
                
                if (result.message) {
                    formattedOutput += '<p class="mb-2"><strong>Message:</strong> ' + result.message + '</p>';
                }
                
                if (result.http_code) {
                    formattedOutput += '<p class="mb-2"><strong>API HTTP Code:</strong> ' + result.http_code + '</p>';
                }
                
                formattedOutput += '<hr>';
                formattedOutput += '<p class="mb-2"><strong>Full Response:</strong></p>';
                formattedOutput += '<pre>' + JSON.stringify(result, null, 2) + '</pre>';
                formattedOutput += '</div>';
                
                output.innerHTML = formattedOutput;
                
                // Show alert
                if (result.success) {
                    showAlert('✅ Login successful! Data structure is correct.', 'success');
                    
                    // Check data structure
                    if (result.data && result.data.user && result.data.token) {
                        showAlert('✅ Response structure is valid!<br>' +
                                 'User: ' + result.data.user.email + '<br>' +
                                 'Token: ' + result.data.token.substring(0, 20) + '...', 'success');
                    } else {
                        showAlert('⚠️ Login successful but data structure is unexpected!<br>' +
                                 'Expected: { data: { user: {...}, token: "..." } }', 'warning');
                    }
                } else {
                    showAlert('❌ Login failed: ' + (result.message || 'Unknown error'), 'danger');
                }
                
            } catch (error) {
                output.innerHTML = '<p class="error">💥 Error: ' + error.message + '</p>';
                showAlert('❌ Connection Error: ' + error.message, 'danger');
            }
        });
    </script>
</body>
</html>