<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VetHub - Login</title>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../css/login.css">

</head>
<body>
    <!-- HEADER -->
    <header>
        <div class="logo-container">
            <img src="../assets/logo.png" alt="VetHub Logo">
            <span class="logo-text">VetHub</span>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main>
        <div class="login-container">
            <div class="login-header">
                <img src="../assets/logo2.png" alt="VetHub">
                <h2>Welcome Back</h2>
                <p>Sign in to your VetHub account</p>
            </div>

            <form id="loginForm">
                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-field">
                        <input id="email" type="email" placeholder="Enter your email" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-field">
                        <input id="password" type="password" placeholder="Enter your password" required>
                    </div>
                </div>

                <button type="button" class="btn login-button" onclick="loginFunc()">
                    Sign In
                </button>
            </form>

            <div class="form-footer">
                <p>Don&apos;t have an account? <a onclick="redirectFunc(3)">Create one</a></p>
            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <footer>
        <p>&copy; 2026 VetHub - Professional Veterinary Management System | 
        <a href="#privacy">Privacy Policy</a> | 
        <a href="#terms">Terms of Service</a></p>
    </footer>

    <script src="../scripts/Service.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            M.AutoInit();
            M.updateTextFields();
        });
    </script>
</body>
</html>