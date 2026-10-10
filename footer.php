<footer>
    <div class="container">
        <h3>Switzerland Travel Guide</h3>
        <p>Explore the Swiss Alps, cities, and lakes.</p>
        <p class="copyright">&copy; <?= date('Y') ?> Switzerland Travel Guide</p>
    </div>
</footer>
<script>
    const username = sessionStorage.getItem("authenticatedName")
        || sessionStorage.getItem("authenticatedUser");
    const welcome = document.getElementById("login-welcome");

    if (username) {
        welcome.textContent = `Welcome, ${username}!`;
        welcome.hidden = false;
    }
</script>
</body>
</html>
