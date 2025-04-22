<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8">
  <title>Login and Registration</title>
  <link rel="stylesheet" href="style.css">
 
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
  <div class="container">
    <input type="checkbox" id="flip">
    <div class="cover">
  <img src="image.png" alt="">

  <div class="text front-text">
  <span class="text-1">Complete miles of journey <br> with one step</span>
  <span class="text-2">Let's get started</span>
  </div>

  <div class="text back-text">
    <span class="text-1">Every new friend is a <br> new adventure</span>
    <span class="text-2">Let's get connected</span>
  </div>
</div>


    <div class="forms">
      <div class="form-content">

        <!-- LOGIN FORM -->
        <div class="login-form">
  <div class="title">Login</div>
  <form id="loginForm">
    <div class="input-boxes">
      <div class="input-box">
        <i class="fas fa-envelope"></i>
        <input type="email" name="email" placeholder="Enter your email" required>
      </div>
      <div class="input-box">
        <i class="fas fa-lock"></i>
        <input type="password" name="password" placeholder="Enter your password" required>
      </div>
      <div class="text"><a href="#">Forgot password?</a></div>
      <div class="button input-box">
        <input type="submit" value="Login">
      </div>
      <div class="text sign-up-text">Don't have an account? <label for="flip">Signup now</label></div>
    </div>
  </form>
</div>


    <!-- SIGNUP FORM -->
<div class="signup-form">
  <div class="title">Signup</div>
  <form id="signupForm" method="POST">
    <div class="input-boxes">

      <div class="input-box">
        <i class="fas fa-user"></i>
        <input type="text" name="first_name" placeholder="First Name" required>
      </div>

      <div class="input-box">
        <i class="fas fa-user"></i>
        <input type="text" name="last_name" placeholder="Last Name" required>
      </div>

      <div class="input-box">
        <i class="fas fa-building"></i>
        <input type="text" name="company_name" placeholder="Company Name ">
      </div>

      <div class="input-box">
        <i class="fas fa-envelope"></i>
        <input type="email" name="email" placeholder="Email" required>
      </div>

      <div class="input-box">
        <i class="fas fa-lock"></i>
        <input type="password" name="password" placeholder="Password" id="password" required>
      </div>

      <div class="input-box">
        <i class="fas fa-lock"></i>
        <input type="password" name="confirm_password" placeholder="Confirm Password" id="confirm_password" required>
      </div>

      <div class="button input-box">
        <input type="submit" value="Signup">
      </div>

      <div class="text sign-up-text">Already have an account? <label for="flip">Login now</label></div>
    </div>
  </form>
</div>

<!-- JavaScript for password validation and AJAX submission -->
<script>
  document.getElementById("signupForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm_password").value;

    // Regular expression for password validation
    const passwordRegex = /^(?=.*[A-Z])(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/;

    // Validate password
    if (!passwordRegex.test(password)) {
      alert("Password must be at least 8 characters long, contain at least one uppercase letter and one special character.");
      return;
    }

    // Validate confirm password
    if (password !== confirmPassword) {
      alert("Passwords do not match.");
      return;
    }

    const form = e.target;
    const formData = new FormData(form);

    fetch('signup.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(response => {
        alert(response); // Show response as alert
        if (response.toLowerCase().includes("signup successful")) {
            form.reset(); // Clear form fields
            document.getElementById("flip").checked = false; // Flip to login form
        }
    })
    .catch(error => alert("An error occurred: " + error));
  });



  document.getElementById('loginForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);

  fetch('login.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      alert("Login successful!");
      const role = parseInt(data.role);
      switch (role) {
        case 1:
          window.location.href = 'admin_dashboard.php';  // Redirect to Admin dashboard
          break;
        case 2:
          window.location.href = 'seller_dashboard.php';  // Redirect to Seller dashboard
          break;
        case 3:
          window.location.href = 'user_dashboard.php';  // Redirect to User dashboard
          break;
        case 5:
          window.location.href = 'super_admin_dashboard.php';  // Redirect to Super Admin dashboard
          break;
        default:
          alert("Unknown role. Contact support.");
      }
    } else {
      alert(data.message || "Login failed!");
    }
  })
  .catch(err => {
    console.error("Login error:", err);
    alert("Something went wrong. Please try again.");
  });
});

  </script>

</body>
</html>