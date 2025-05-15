<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8">
  <title>Login and Registration</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    /* Toast Styles */
    .toast {
      visibility: hidden;
      min-width: 250px;
      background-color: #333;
      color: #fff;
      text-align: center;
      border-radius: 8px;
      padding: 12px;
      position: fixed;
      z-index: 1000;
      left: 50%;
      bottom: 30px;
      transform: translateX(-50%);
      font-size: 16px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.3);
      transition: all 0.5s ease;
    }

    .toast.show {
      visibility: visible;
    }

    .toast.success {
      background-color: #28a745;
    }

    .toast.error {
      background-color: #dc3545;
    }

    .toast.info {
      background-color: #007bff;
    }

    /* Tooltip styles for email domain hint */
    .email-tooltip {
      display: none;
      position: absolute;
      bottom: -25px;
      left: 0;
      background-color: #333;
      color: white;
      padding: 5px;
      border-radius: 5px;
      font-size: 12px;
      z-index: 999;
    }
  </style>
</head>
<body>

  <!-- Toast container -->
  <div id="toast" class="toast"></div>

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
              <div class="input-box" style="position: relative;">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Enter your password" id="loginPassword" required>
                <span class="toggle-password" id="toggleLoginPassword" style="cursor:pointer; position:absolute; right: 10px; top: 50%; transform: translateY(-50%);">
                  <i class="fas fa-eye"></i>
                </span>
              </div>
              <div class="text"><a href="forgot_password.php">Forgot password?</a></div>
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
                <input type="text" name="company_name" placeholder="Company Name">
              </div>

              <div class="input-box" style="position: relative;">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" id="signupEmail" placeholder="Email" required>
                <div id="emailTooltip" class="email-tooltip">
                  Allowed domains: yahoo, hotmail, gmail, bing, icloud, or outlook
                </div>
              </div>

              <div class="input-box" style="position: relative;">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" id="signupPassword" placeholder="Password" required>
                <span class="toggle-password" id="toggleSignupPassword" style="cursor:pointer; position:absolute; right: 10px; top: 50%; transform: translateY(-50%);">
                  <i class="fas fa-eye"></i>
                </span>
              </div>

              <div class="password-hint" id="passwordHint" style="display: none; font-size: 13px; margin-top: 5px; color: #ff4444;">
                Must include: 1 uppercase letter, 1 number, 1 special character, min 8 characters.
              </div>

              <div class="input-box" style="position: relative;">
                <i class="fas fa-lock"></i>
                <input type="password" name="confirm_password" id="confirmPassword" placeholder="Confirm Password" required>
                <span class="toggle-password" id="toggleConfirmPassword" style="cursor:pointer; position:absolute; right: 10px; top: 50%; transform: translateY(-50%);">
                  <i class="fas fa-eye"></i>
                </span>
              </div>

              <div class="button input-box">
                <input type="submit" value="Signup">
              </div>

              <div class="text sign-up-text">Already have an account? <label for="flip">Login now</label></div>
            </div>
          </form>
        </div>

      </div>
    </div>
  </div>

  <!-- JavaScript -->
  <script>
    function showToast(message, type = 'info', duration = 3000) {
      const toast = document.getElementById("toast");
      toast.textContent = message;
      toast.className = `toast show ${type}`;
      setTimeout(() => {
        toast.className = "toast";
      }, duration);
    }

    // Email focus event listener to show allowed domains
    document.getElementById("signupEmail").addEventListener("focus", function() {
      const tooltip = document.getElementById("emailTooltip");
      tooltip.style.display = "block"; // Show tooltip
    });

    document.getElementById("signupEmail").addEventListener("blur", function() {
      const tooltip = document.getElementById("emailTooltip");
      tooltip.style.display = "none"; // Hide tooltip when focus is lost
    });
     // Function to validate email domain
     function isAllowedEmailDomain(email) {
            const allowedDomains = ['yahoo.com', 'hotmail.com', 'gmail.com', 'bing.com', 'icloud.com', 'outlook.com'];
            const domain = email.substring(email.lastIndexOf("@") + 1).toLowerCase();
            return allowedDomains.includes(domain);
        }
    // Signup Form Submission
    document.getElementById("signupForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const email = document.getElementById("signupEmail").value;
  if (!isAllowedEmailDomain(email)) {
    showToast("Signup failed: Only emails from yahoo, hotmail, gmail, bing, icloud, or outlook are allowed.", "error");
    return; // Stop further submission
  }

  const password = document.getElementById("signupPassword").value;
  const confirmPassword = document.getElementById("confirmPassword").value;
  const passwordRegex = /^(?=.*[A-Z])(?=.*[!@#$%^&*])(?=.*\d)[A-Za-z\d!@#$%^&*]{8,}$/;

  if (!passwordRegex.test(password)) {
    showToast("Password must be at least 8 characters, include an uppercase letter, a number, and a special character.", "error");
    return;
  }

  if (password !== confirmPassword) {
    showToast("Passwords do not match.", "error");
    return;
  }

  const formData = new FormData(this);

  fetch('signup.php', {
    method: 'POST',
    body: formData
  })
    .then(res => res.text())
    .then(response => {
      showToast(response.toLowerCase().includes("success") ? response : "Signup failed: " + response, response.toLowerCase().includes("success") ? "success" : "error");
      if (response.toLowerCase().includes("success")) {
        this.reset();
        document.getElementById("flip").checked = false;
      }
    })
    .catch(err => showToast("Signup error: " + err, "error"));
});

    // Login Form Submission
    document.getElementById("loginForm").addEventListener("submit", function (e) {
      e.preventDefault();

      const formData = new FormData(this);

      fetch('login.php', {
        method: 'POST',
        body: formData
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            showToast("Login successful!", "success");
            const role = parseInt(data.role);
            setTimeout(() => {
              switch (role) {
                case 1: window.location.href = 'admin_dashboard.php'; break;
                case 2: window.location.href = 'seller_dashboard.php'; break;
                case 3: window.location.href = 'supplier_dashboard.php'; break;
                case 5: window.location.href = 'super_admin_dashboard.php'; break;
                default: showToast("Unknown role. Contact support.", "error");
              }
            }, 1000);
          } else {
            showToast(data.message || "Login failed!", "error");
          }
        })
        .catch(err => showToast("Login error: " + err, "error"));
    });

    // Password Hint and Strength Feedback
    const signupPassword = document.getElementById("signupPassword");
    const passwordHint = document.getElementById("passwordHint");

    signupPassword.addEventListener("focus", () => passwordHint.style.display = "block");
    signupPassword.addEventListener("blur", () => passwordHint.style.display = "none");

    signupPassword.addEventListener("input", () => {
      const strongRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/;
      passwordHint.style.color = strongRegex.test(signupPassword.value) ? "green" : "#ff4444";
    });

    // Toggle Password Visibility
    document.getElementById("toggleLoginPassword").addEventListener("click", () => {
      const loginInput = document.getElementById("loginPassword");
      const isPassword = loginInput.getAttribute("type") === "password";
      loginInput.setAttribute("type", isPassword ? "text" : "password");
      document.getElementById("toggleLoginPassword").innerHTML = isPassword ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
    });

    document.getElementById("toggleSignupPassword").addEventListener("click", () => {
      const signupInput = document.getElementById("signupPassword");
      const isPassword = signupInput.getAttribute("type") === "password";
      signupInput.setAttribute("type", isPassword ? "text" : "password");
      document.getElementById("toggleSignupPassword").innerHTML = isPassword ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
    });

    document.getElementById("toggleConfirmPassword").addEventListener("click", () => {
      const confirmInput = document.getElementById("confirmPassword");
      const isPassword = confirmInput.getAttribute("type") === "password";
      confirmInput.setAttribute("type", isPassword ? "text" : "password");
      document.getElementById("toggleConfirmPassword").innerHTML = isPassword ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
    });

  </script>
</body>
</html>
