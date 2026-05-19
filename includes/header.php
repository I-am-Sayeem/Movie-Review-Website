<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/database.php';

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="CineVault — Discover, review, and rate your favorite movies. Join our community of film enthusiasts.">
    <title>CineVault<?php echo isset($pageTitle) ? ' — ' . $pageTitle : ' — Movie Reviews'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dome-gallery.css">
</head>
<body>
    <!-- Flash Messages -->
    <?php $flash = getFlash(); if ($flash): ?>
    <div class="flash-message flash-<?php echo $flash['type']; ?>" id="flashMsg">
        <span><?php echo $flash['message']; ?></span>
        <button onclick="this.parentElement.remove()" class="flash-close">&times;</button>
    </div>
    <?php endif; ?>

    <nav class="navbar" id="navbar">
        <div class="container nav-container">
            <a href="index.php" class="nav-logo">
                <span class="logo-icon">🎬</span>
                <span class="logo-text">Cine<span class="logo-accent">Vault</span></span>
            </a>
            
            <div class="nav-links" id="navLinks">
                <?php if (!isLoggedIn()): ?>
                    <a href="index.php" class="nav-link <?php echo $currentPage === 'index' ? 'active' : ''; ?>">Home</a>
                <?php endif; ?>
                <?php if (isLoggedIn()): ?>
                    <a href="movies.php" class="nav-link <?php echo $currentPage === 'movies' ? 'active' : ''; ?>">Movies</a>
                    <?php if (isAdmin()): ?>
                        <a href="add_movie.php" class="nav-link <?php echo $currentPage === 'add_movie' ? 'active' : ''; ?>">Add Movie</a>
                        <a href="admin_dashboard.php" class="nav-link <?php echo $currentPage === 'admin_dashboard' ? 'active' : ''; ?>">🛡️ Admin</a>
                    <?php endif; ?>
                    <a href="profile.php" class="nav-link <?php echo $currentPage === 'profile' ? 'active' : ''; ?>">Profile</a>
                <?php endif; ?>
            </div>

            <div class="nav-actions">
                <?php if (isLoggedIn()): ?>
                    <div class="nav-user">
                        <div class="nav-avatar"><?php echo getInitials(getCurrentUsername()); ?></div>
                        <span class="nav-username"><?php echo sanitize(getCurrentUsername()); ?></span>
                    </div>
                    <a href="logout.php" class="btn btn-outline btn-sm">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline btn-sm">Login</a>
                    <a href="register.php" class="btn btn-primary btn-sm">Sign Up</a>
                <?php endif; ?>
                <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </nav>

    <main class="main-content">
