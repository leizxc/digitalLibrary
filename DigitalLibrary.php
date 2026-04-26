<?php
session_start();
include("connection.php");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Library System</title>
    <link rel="stylesheet" href="./assets/style.css">

    <script>
        function togglepassword(id, eyeIcon){
            const passwordInput = document.getElementById(id);

            if (passwordInput.type === "password"){
                passwordInput.type = "text";

                eyeIcon.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor">
                        <path d="M2 12s4-7.5 10-7.5 10 7.5 10 7.5-4 7.5-10 7.5S2 12 2 12zm10 3a3 3 0 100-6 3 3 0 000 6z"/>
                        <line x1="2" y1="2" x2="22" y2="22" stroke="currentColor" stroke-width="2"/>
                    </svg>`;
            } else {
                passwordInput.type = "password";

                eyeIcon.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor">
                        <path d="M12 4.5c-7 0-11 7.5-11 7.5s4 7.5 11 7.5 11-7.5 11-7.5-4-7.5-11-7.5zm0 12c-2.5 0-4.5-2-4.5-4.5S9.5 7.5 12 7.5s4.5 2 4.5 4.5-2 4.5-4.5 4.5z"/>
                    </svg>`;
            }
        }

        function showForm(formType){
            document.getElementById("login-form").style.display = (formType === "login") ? "block" : "none";
            document.getElementById("signup-form").style.display = (formType === "signup") ? "block" : "none";
        }
    </script>
</head>

<body>
<div class="container">
    <div class="form-container">

        <!-- BLOBS -->
        <div class="form-blob">
            <img src="./assets/images/blob.svg" class="blob-image blob-image--1">
            <img src="./assets/images/blob.svg" class="blob-image blob-image--2">
            <img src="./assets/images/blob.svg" class="blob-image blob-image--3">
        </div>

        <!-- LOGIN FORM -->
<div id="login-form">
    <div class="form-header">
        <p>Please Enter your Details</p>
        <h1>Welcome Back</h1>
    </div>
    <!-- error box -->
    <?php if(isset($_SESSION['error'])): ?>
        <div class="error-box">
            <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
        </div>
        <?php endif; ?>

      <form id="loginForm" class="form-box" action="register.php" method="POST" autocomplete="off">
        <input type="hidden" name="signIn" value="1">
             <div class="input-group">
            <input type="email" id="email" name="email" class="input-field" placeholder=" " required autocomplete="off">
            <label class="floating-label">Email Address</label>
        </div>

        <div class="input-group">
            <input type="password" id="password" name="password" class="input-field" placeholder=" " required autocomplete="new-password">
            <label class="floating-label">Password</label>

            <div class="eye-icon" onclick="togglepassword('password', this)">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor">
                    <path d="M12 4.5c-7 0-11 7.5-11 7.5s4 7.5 11 7.5 11-7.5 11-7.5-4-7.5-11-7.5zm0 12c-2.5 0-4.5-2-4.5-4.5S9.5 7.5 12 7.5s4.5 2 4.5 4.5-2 4.5-4.5 4.5z"/>
                </svg>
            </div>
        </div>

        <div class="input-group checkbox-group">
            <div class="remember-me">
                <input type="checkbox" id="remember">
                <label for="remember">Remember me</label>
            </div>
            <a href="#" class="form-link">Forgot password?</a>
        </div>

        <button type="submit" class="form-btn form-btn--submit" name="signIn">
            Sign In
        </button>

        <div class="form-bottom">
            <p>
                No account?
                <a href="#" class="form-link" onclick="showForm('signup')">Sign Up</a>
            </p>
        </div>
    </form>
</div>

        <!-- SIGNUP FORM -->
        <div id="signup-form" style="display:none;">
            <div class="form-header">

                <p>Create your account</p>
                <h1>Sign Up</h1>

                  <?php if(isset($_SESSION['signup_error'])): ?>
                    <div class="error-box">
                        <?php
                        echo $_SESSION['signup_error'];
                        unset($_SESSION['signup_error']);
                        ?>
                    </div>
                    <?php endif; ?>

                <?php if(isset($_SESSION['signup_success'])): ?>
                    <div class="success-box">
                        <?php
                        echo $_SESSION['signup_success'];
                        unset($_SESSION['signup_success']);
                        ?>
                    </div>
                <?php endif; ?>
            </div>

            <form class="form-box" action="register.php" method="POST" autocomplete="off">

                <div class="input-group">
                    <input type="text" name="fname" class="input-field" placeholder=" " required>
                    <label class="floating-label">First Name</label>
                </div>

                <div class="input-group">
                    <input type="text" name="mname" class="input-field" placeholder=" " required>
                    <label class="floating-label">Middle Name</label>
                </div>

                <div class="input-group">
                    <input type="text" name="lname" class="input-field" placeholder=" " required>
                    <label class="floating-label">Last Name</label>
                </div>

                <div class="input-group">
                    <input type="email" name="email" class="input-field" placeholder=" " required autocomplete="off">
                    <label class="floating-label">Email Address</label>
                </div>
                <div class="input-group">
                    <select name="role" id="role">
                        <option value="">--Select Role---</option>
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                    </select>
                </div>

                <div class="input-group">
                    <input type="password" id="signup-password" name="password" class="input-field" placeholder=" " required autocomplete="new-password">
                    <label class="floating-label">Password</label>

                    <div class="eye-icon" onclick="togglepassword('signup-password', this)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor">
                            <path d="M12 4.5c-7 0-11 7.5-11 7.5s4 7.5 11 7.5 11-7.5 11-7.5-4-7.5-11-7.5zm0 12c-2.5 0-4.5-2-4.5-4.5S9.5 7.5 12 7.5s4.5 2 4.5 4.5-2 4.5-4.5 4.5z"/>
                        </svg>
                    </div>
                </div>

                <button type="submit" class="form-btn form-btn--submit" name="signUp">
                    Register
                </button>

                <div class="form-bottom">
                    <p>
                        Already have an account?
                        <a href="#" class="form-link" onclick="showForm('login')">Sign In</a>
                    </p>
                </div>

            </form>
        </div>

    </div>
</div>
</body>
<!--sign up form errorbox logic-->
<?php if(isset($_SESSION['form']) && $_SESSION['form'] === 'signup'): ?>
    <script>
        window.onload = function(){
            showForm('signup');
        }
    </script>
    <?php unset($_SESSION['form']); ?>
<?php endif; ?>

<!-- Success box-->
 <?php if(isset($_SESSION['signup_success'])): ?>
    <script>
        window.onload = function(){
            showForm('signup');
        }
    </script>
    <?php endif; ?>
</html>