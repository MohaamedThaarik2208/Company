```javascript
// Text & Solution Company - JavaScript

document.addEventListener("DOMContentLoaded", function () {

    // Employee Registration Form Validation
    const registerForm = document.querySelector(
        'form[action="register.php"], form#registerForm'
    );

    if (registerForm) {
        registerForm.addEventListener("submit", function (event) {
            const password = registerForm.querySelector(
                '[name="password"]'
            );
            const confirmPassword = registerForm.querySelector(
                '[name="confirm_password"]'
            );

            if (password && password.value.length < 8) {
                event.preventDefault();
                alert("Password must contain at least 8 characters.");
                password.focus();
                return;
            }

            if (
                password &&
                confirmPassword &&
                password.value !== confirmPassword.value
            ) {
                event.preventDefault();
                alert("Passwords do not match.");
                confirmPassword.focus();
            }
        });
    }

    // Show / Hide Password
    document.querySelectorAll(".toggle-password").forEach(function (button) {
        button.addEventListener("click", function () {
            const input = document.getElementById(
                button.dataset.target
            );

            if (!input) return;

            if (input.type === "password") {
                input.type = "text";
                button.textContent = "Hide";
            } else {
                input.type = "password";
                button.textContent = "Show";
            }
        });
    });

    // Confirm before rejecting an employee
    document.querySelectorAll(".reject-confirm").forEach(function (form) {
        form.addEventListener("submit", function (event) {
            if (!confirm("Are you sure you want to reject this employee?")) {
                event.preventDefault();
            }
        });
    });

    // Confirm before logging out
    document.querySelectorAll(".logout-confirm").forEach(function (link) {
        link.addEventListener("click", function (event) {
            if (!confirm("Are you sure you want to log out?")) {
                event.preventDefault();
            }
        });
    });

});
```