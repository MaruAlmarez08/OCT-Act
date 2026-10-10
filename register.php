<?php
$validationErrors = [];
$validationMessage = '';
$name = '';
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nameInput = $_POST['name'] ?? '';
    $usernameInput = $_POST['username'] ?? '';
    $emailInput = $_POST['email'] ?? '';
    $passwordInput = $_POST['password'] ?? '';
    $confirmPasswordInput = $_POST['confirm-password'] ?? '';
    $name = is_string($nameInput) ? trim($nameInput) : '';
    $username = is_string($usernameInput) ? trim($usernameInput) : '';
    $email = is_string($emailInput) ? trim($emailInput) : '';
    $password = is_string($passwordInput) ? $passwordInput : '';
    $confirmPassword = is_string($confirmPasswordInput) ? $confirmPasswordInput : '';

    if (!preg_match("/^[\\p{L}\\p{M}][\\p{L}\\p{M} .'-]{1,79}$/u", $name)) {
        $validationErrors[] = 'Name must be 2-80 characters and contain only letters, spaces, periods, apostrophes, or hyphens.';
    }

    if (!preg_match('/^[A-Za-z0-9_]{3,20}$/D', $username)) {
        $validationErrors[] = 'Username must be 3-20 characters and contain only letters, numbers, or underscores.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[^@\s]+@[^@\s]+\.[^@\s]+$/D', $email)) {
        $validationErrors[] = 'Enter a valid email address.';
    }

    if (!preg_match('/^.{8,128}$/sD', $password)) {
        $validationErrors[] = 'Password must be 8-128 characters long.';
    }

    if ($password !== $confirmPassword) {
        $validationErrors[] = 'The passwords do not match.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="design.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.21.0/dist/jquery.validate.min.js"></script>
</head>

<body class="register-page">

    <main class="container">
        <section class="form-box register-box" aria-labelledby="register-title">

            <h1 id="register-title">Register</h1>
            <p class="welcome">Create your account</p>

            <form id="register-form" action="#" method="post" novalidate>

                <div class="input-group">
                    <label for="name">Name</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="Enter your name"
                        required
                    >
                </div>

                <div class="input-group">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="Choose a username"
                        required
                    >
                </div>

                <div class="input-group">
                    <label for="email">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="Enter your email"
                        required
                    >
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        data-password-input
                        placeholder="Create a password"
                        required
                    >
                    <button class="password-toggle" type="button" data-password-target="password" aria-label="Show password" aria-pressed="false">
                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <span>Show</span>
                    </button>
                </div>

                <div class="input-group">
                    <label for="confirm-password">Confirm Password</label>
                    <input
                        type="password"
                        id="confirm-password"
                        name="confirm-password"
                        data-password-input
                        placeholder="Confirm your password"
                        required
                    >
                    <button class="password-toggle" type="button" data-password-target="confirm-password" aria-label="Show password" aria-pressed="false">
                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <span>Show</span>
                    </button>
                </div>

                <button type="submit">Register</button>
                <p id="register-feedback" class="form-feedback <?= $validationErrors ? 'error-message' : '' ?>" role="status" aria-live="polite"><?= htmlspecialchars(implode(' ', $validationErrors) ?: $validationMessage, ENT_QUOTES, 'UTF-8') ?></p>

                <p class="account-text">
                    Already have an account?
                    <a href="login.php">Login Here</a>
                </p>

            </form>

        </section>
    </main>
    <script>
        $(function () {
            const accountsStorageKey = "registeredAccounts";

            $(".password-toggle").on("click", function () {
                const $toggle = $(this);
                const input = document.getElementById($toggle.attr("data-password-target"));
                const isVisible = input.type === "text";

                input.type = isVisible ? "password" : "text";
                $toggle.attr("aria-pressed", String(!isVisible));
                $toggle.attr("aria-label", isVisible ? "Show password" : "Hide password");
                $toggle.find("span").text(isVisible ? "Show" : "Hide");
            });

            $("#register-form").validate({
                rules: {
                    name: { required: true, minlength: 2 },
                    username: { required: true, minlength: 3 },
                    email: { required: true, email: true },
                    password: { required: true, minlength: 8 },
                    "confirm-password": { required: true, equalTo: "#password" }
                },
                messages: {
                    name: {
                        required: "Please enter your name.",
                        minlength: "Your name must be at least 2 characters long."
                    },
                    username: {
                        required: "Please choose a username.",
                        minlength: "Your username must be at least 3 characters long."
                    },
                    email: {
                        required: "Please enter your email address.",
                        email: "Please enter a valid email address."
                    },
                    password: {
                        required: "Please create a password.",
                        minlength: "Your password must be at least 8 characters long."
                    },
                    "confirm-password": {
                        required: "Please confirm your password.",
                        equalTo: "The passwords do not match."
                    }
                },
                submitHandler: function (form) {
                    const $feedback = $("#register-feedback");
                    const username = form.elements.username.value.trim();
                    let accounts;

                    try {
                        accounts = JSON.parse(localStorage.getItem(accountsStorageKey) || "[]");
                        if (!Array.isArray(accounts)) {
                            throw new Error("Saved account data is invalid.");
                        }

                        const usernameExists = accounts.some(function (savedAccount) {
                            return typeof savedAccount.username === "string"
                                && savedAccount.username.toLowerCase() === username.toLowerCase();
                        });

                        if (usernameExists) {
                            $feedback
                                .removeClass("success-message")
                                .addClass("error-message")
                                .text("That username is already registered. Please choose another.");
                            return false;
                        }

                        accounts.push({
                            name: form.elements.name.value.trim(),
                            username: username,
                            email: form.elements.email.value.trim(),
                            password: form.elements.password.value
                        });
                        localStorage.setItem(accountsStorageKey, JSON.stringify(accounts));
                    } catch (error) {
                        $feedback
                            .removeClass("success-message")
                            .addClass("error-message")
                            .text("We couldn't save your account in this browser. Please try again.");
                        return false;
                    }

                    $feedback
                        .removeClass("error-message")
                        .addClass("success-message")
                        .text("Registration successful! You can now log in with your username and password.");
                    return false;
                }
            });

            $("#register-form input").on("input", function () {
                $("#register-feedback").removeClass("error-message success-message").empty();
            });
        });
    </script>

</body>
</html>