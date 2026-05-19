<?php
require_once 'includes/auth.php';
requireAdmin();
require_once 'config/database.php';

$movieId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;

if ($movieId <= 0) {
    setFlash('error', 'Invalid movie ID.');
    header('Location: admin_dashboard.php');
    exit;
}

// Fetch movie to get poster path for cleanup
$stmt = $pdo->prepare("SELECT poster FROM movies WHERE id = ?");
$stmt->execute([$movieId]);
$movie = $stmt->fetch();

if (!$movie) {
    setFlash('error', 'Movie not found.');
    header('Location: admin_dashboard.php');
    exit;
}

// Delete the movie (reviews cascade automatically via FK)
$deleteStmt = $pdo->prepare("DELETE FROM movies WHERE id = ?");
$deleteStmt->execute([$movieId]);

if ($deleteStmt->rowCount() > 0) {
    // Optionally delete the poster file (only if it's a user-uploaded file)
    if ($movie['poster'] && strpos($movie['poster'], 'poster_') !== false && file_exists($movie['poster'])) {
        unlink($movie['poster']);
    }
    setFlash('success', '✅ Movie deleted successfully.');
} else {
    setFlash('error', '❌ Failed to delete movie.');
}

header('Location: admin_dashboard.php');
exit;
