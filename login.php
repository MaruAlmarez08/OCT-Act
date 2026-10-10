<?php
$validationErrors = [];
$validationMessage = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameInput = $_POST['username'] ?? '';
    $passwordInput = $_POST['password'] ?? '';
    $username = is_string($usernameInput) ? trim($usernameInput) : '';
    $password = is_string($passwordInput) ? $passwordInput : '';

    if (!preg_match('/^[A-Za-z0-9_]{3,20}$/D', $username)) {
        $validationErrors[] = 'Please enter a username using 3 to 20 letters, numbers, or underscores.';
    }

    if (!preg_match('/^.{1,32}$/sD', $password)) {
        $validationErrors[] = 'Please enter your password. It can be up to 32 characters long.';
    }

    if (!$validationErrors) {
        $validationMessage = 'Input format is valid. JavaScript is required to verify this browser-based demo account.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="design.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.21.0/dist/jquery.validate.min.js"></script>
</head>

<body class="login-page">

    <main class="container">
        <section class="form-box" aria-labelledby="login-title">

            <h1 id="login-title">Login</h1>
            <p class="welcome">Welcome back!</p>

            <form id="login-form" action="#" method="post" novalidate>

                <div class="input-group">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="Enter your username"
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
                        placeholder="Enter your password"
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

                <div class="options">
                    <label class="remember">
                        <input type="checkbox" id="remember" name="remember">
                        Remember me
                    </label>
                </div>

                <button id="login-submit" type="submit">Login</button>
                <p id="login-feedback" class="form-feedback <?= $validationErrors ? 'error-message' : '' ?>" role="status" aria-live="polite"><?= htmlspecialchars(implode(' ', $validationErrors) ?: $validationMessage, ENT_QUOTES, 'UTF-8') ?></p>

                <p class="account-text">
                    Don't have an account?
                    <a href="register.php">Register</a>
                </p>

            </form>

        </section>
    </main>
    <script>
        $(function () {
            const maxLoginAttempts = 3;
            let failedLoginAttempts = 0;
            const rememberedUsername = localStorage.getItem("rememberedUsername");

            if (rememberedUsername) {
                $("#username").val(rememberedUsername);
                $("#remember").prop("checked", true);
            }

            $("#remember").on("change", function () {
                if (!this.checked) {
                    localStorage.removeItem("rememberedUsername");
                }
            });

            $(".password-toggle").on("click", function () {
                const $toggle = $(this);
                const input = document.getElementById($toggle.attr("data-password-target"));
                const isVisible = input.type === "text";

                input.type = isVisible ? "password" : "text";
                $toggle.attr("aria-pressed", String(!isVisible));
                $toggle.attr("aria-label", isVisible ? "Show password" : "Hide password");
                $toggle.find("span").text(isVisible ? "Show" : "Hide");
            });

            $("#login-form").validate({
                rules: {
                    username: { required: true },
                    password: { required: true }
                },
                messages: {
                    username: { required: "Please enter your username." },
                    password: { required: "Please enter your password." }
                },
                submitHandler: function (form) {
                    const username = form.elements.username.value.trim();
                    const password = form.elements.password.value;
                    const $feedback = $("#login-feedback");
                    let accounts;

                    try {
                        accounts = JSON.parse(localStorage.getItem("registeredAccounts") || "[]");
                        if (!Array.isArray(accounts)) {
                            throw new Error("Saved account data is invalid.");
                        }
                    } catch (error) {
                        $feedback
                            .removeClass("success-message")
                            .addClass("error-message")
                            .text("Saved account data could not be read. Please contact the site owner.");
                        return false;
                    }

                    const account = accounts.find(function (savedAccount) {
                        return typeof savedAccount.username === "string"
                            && savedAccount.username.toLowerCase() === username.toLowerCase()
                            && savedAccount.password === password;
                    });
                    const isDemoAccount = username === "admin" && password === "12345";

                    if (!account && !isDemoAccount) {
                        failedLoginAttempts += 1;
                        $feedback
                            .removeClass("success-message")
                            .addClass("error-message")
                            .text(failedLoginAttempts >= maxLoginAttempts
                                ? "Too many incorrect attempts. Refresh the page to try again."
                                : `The username or password is incorrect. ${maxLoginAttempts - failedLoginAttempts} attempts remaining.`);

                        if (failedLoginAttempts >= maxLoginAttempts) {
                            $("#login-submit").prop("disabled", true);
                        }

                        return false;
                    }

                    if ($("#remember").is(":checked")) {
                        localStorage.setItem("rememberedUsername", username);
                    } else {
                        localStorage.removeItem("rememberedUsername");
                    }

                    sessionStorage.setItem("authenticatedUser", username);
                    sessionStorage.setItem("authenticatedName", account ? account.username : username);
                    window.location.href = "index.php";
                    return false;
                }
            });

            $("#login-form input").on("input", function () {
                if (failedLoginAttempts < maxLoginAttempts) {
                    $("#login-feedback").removeClass("error-message").empty();
                }
            });
        });
    </script>

</body>
</html>