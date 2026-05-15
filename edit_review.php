<?php
$pageTitle = 'Edit Review';
require_once 'includes/auth.php';
requireLogin();
require_once 'config/database.php';

$reviewId = intval($_GET['id'] ?? 0);
if ($reviewId <= 0) {
    header('Location: profile.php');
    exit;
}

// Fetch review and verify ownership
$stmt = $pdo->prepare("
    SELECT r.*, m.title as movie_title, m.id as movie_id
    FROM reviews r
    JOIN movies m ON r.movie_id = m.id
    WHERE r.id = ? AND r.user_id = ?
");
$stmt->execute([$reviewId, getCurrentUserId()]);
$review = $stmt->fetch();

if (!$review) {
    setFlash('error', 'Review not found or you don\'t have permission to edit it.');
    header('Location: profile.php');
    exit;
}

$errors = [];



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = intval($_POST['rating'] ?? 0);
    $reviewText = trim($_POST['review_text'] ?? '');

    if ($rating < 1 || $rating > 5) {
        $errors[] = 'Please select a rating (1-5 stars).';
    }
    if (strlen($reviewText) < 10) {
        $errors[] = 'Review must be at least 10 characters.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE reviews SET rating = ?, review_text = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$rating, $reviewText, $reviewId, getCurrentUserId()]);
        setFlash('success', 'Review updated successfully! ✏️');
        header("Location: movie.php?id=" . $review['movie_id']);
        exit;
    }
}

require_once 'includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1><span class="emoji">✏️</span> Edit Review</h1>
    </div>

    <div class="card form-card wide">
        <h2 style="font-size:1.2rem;margin-bottom:24px;">
            Editing review for <a href="movie.php?id=<?php echo $review['movie_id']; ?>" style="color:var(--primary-light);"><?php echo sanitize($review['movie_title']); ?></a>
        </h2>

        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $err): ?>
                <div class="form-error mb-2">⚠ <?php echo $err; ?></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <form method="POST" action="edit_review.php?id=<?php echo $reviewId; ?>">
            <div class="star-rating-group">
                <label>Your Rating</label>
                <?php echo renderStars($_POST['rating'] ?? $review['rating'], true); ?>
            </div>

            <div class="form-group">
                <label for="review_text">Your Review</label>
                <textarea id="review_text" name="review_text" class="form-control" required
                ><?php echo sanitize($_POST['review_text'] ?? $review['review_text']); ?></textarea>
            </div>

            <div class="flex" style="gap:12px;">
                <button type="submit" class="btn btn-primary">Update Review</button>
                <a href="movie.php?id=<?php echo $review['movie_id']; ?>" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
