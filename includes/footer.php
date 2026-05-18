    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="index.php" class="nav-logo">
                        <span class="logo-icon">🎬</span>
                        <span class="logo-text">Cine<span class="logo-accent">Vault</span></span>
                    </a>
                    <p class="footer-desc">Your ultimate destination for movie reviews. Discover, rate, and share your thoughts on the films you love.</p>
                </div>
                <div class="footer-links-col">
                    <h4>Navigate</h4>
                    <a href="index.php">Home</a>
                    <a href="movies.php">Browse Movies</a>
                    <?php if (isLoggedIn()): ?>
                        <a href="add_movie.php">Add Movie</a>
                        <a href="profile.php">My Profile</a>
                    <?php else: ?>
                        <a href="login.php">Login</a>
                        <a href="register.php">Sign Up</a>
                    <?php endif; ?>
                </div>
                <div class="footer-links-col">
                    <h4>Genres</h4>
                    <a href="movies.php?genre=Action">Action</a>
                    <a href="movies.php?genre=Drama">Drama</a>
                    <a href="movies.php?genre=Sci-Fi">Sci-Fi</a>
                    <a href="movies.php?genre=Thriller">Thriller</a>
                    <a href="movies.php?genre=Comedy">Comedy</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> CineVault. All rights reserved.</p>
                <p class="footer-credits">Made with ❤️ for movie lovers</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/bundle.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>
