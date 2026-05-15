echo "# Movie-Review-Website" >> README.md<?php
require_once 'includes/auth.php';
requireLogin();
require_once 'config/database.php';

$reviewId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$movieId  = filter_input(INPUT_GET, 'movie_id', FILTER_VALIDATE_INT) ?: 0;


if ($reviewId <= 0) {
    header('Location: profile.php');
    exit;
}

// Verify ownership and delete
$stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ? AND user_id = ?");
$stmt->execute([$reviewId, getCurrentUserId()]);

if ($stmt->rowCount() > 0) {
    setFlash('success', 'Review deleted successfully.');
} else {
    setFlash('error', 'Review not found or you don\'t have permission.');
}

// Redirect back
if ($movieId > 0) {
    header("Location: movie.php?id=$movieId");
} else {
    header('Location: profile.php');
}
exit;
