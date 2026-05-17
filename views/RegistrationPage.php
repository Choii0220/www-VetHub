<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VetHub - Register</title>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../css/Registration.css">

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
        <div class="register-container">
            <div class="register-header">
                <h2>Create Account</h2>
                <p>Join VetHub to manage your clinic with ease and efficiency </p>
            </div>

            <form id="registerForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="fName">First Name</label>
                        <div class="input-field">
                            <input id="fName" type="text" placeholder="First name" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="lName">Last Name</label>
                        <div class="input-field">
                            <input id="lName" type="text" placeholder="Last name" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-field">
                        <input id="email" type="email" placeholder="you@example.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-field password-field">
                        <input id="password" type="password" placeholder="Create a password" required>
                        <i class="material-icons toggle-password" onclick="togglePassword(this)">visibility</i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="prc">PRC License Number</label>
                    <div class="input-field">
                        <input id="prc" type="text" maxlength="7" placeholder="7-digit PRC number" required>
                    </div>
                </div>

                <button type="button" class="btn register-button" onclick="registerFunc()">
                    Create Account
                </button>
            </form>

            <div class="form-footer">
                <p>Already have an account? <a onclick="redirectFunc(1)">Sign in</a></p>
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

        function togglePassword(icon) {
            const passwordInput = document.getElementById('password');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                passwordInput.type = 'password';
                icon.textContent = 'visibility';
            }
        }
    </script>
</body>
</html>