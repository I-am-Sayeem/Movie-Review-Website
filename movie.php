<?php
require_once 'includes/auth.php';
require_once 'config/database.php';

$movieId = intval($_GET['id'] ?? 0);
if ($movieId <= 0) {
    header('Location: movies.php');
    exit;
}

// Fetch movie
$stmt = $pdo->prepare("SELECT m.*, u.username as added_by_name FROM movies m LEFT JOIN users u ON m.added_by = u.id WHERE m.id = ?");
$stmt->execute([$movieId]);
$movie = $stmt->fetch();

if (!$movie) {
    setFlash('error', 'Movie not found.');
    header('Location: movies.php');
    exit;
}

$pageTitle = $movie['title'];
$avgRating = getAverageRating($pdo, $movieId);
$reviewCount = getReviewCount($pdo, $movieId);

// Fetch all reviews for this movie
$reviewsStmt = $pdo->prepare("
    SELECT r.*, u.username 
    FROM reviews r 
    JOIN users u ON r.user_id = u.id 
    WHERE r.movie_id = ? 
    ORDER BY r.created_at DESC
");
$reviewsStmt->execute([$movieId]);
$reviews = $reviewsStmt->fetchAll();

// Check if current user has already reviewed
$userReview = null;
if (isLoggedIn()) {
    $checkStmt = $pdo->prepare("SELECT * FROM reviews WHERE movie_id = ? AND user_id = ?");
    $checkStmt->execute([$movieId, getCurrentUserId()]);
    $userReview = $checkStmt->fetch();
}

// Handle review submission
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn()) {
    $rating = intval($_POST['rating'] ?? 0);
    $reviewText = trim($_POST['review_text'] ?? '');

    if ($rating < 1 || $rating > 5) {
        $errors[] = 'Please select a rating (1-5 stars).';
    }
    if (strlen($reviewText) < 10) {
        $errors[] = 'Review must be at least 10 characters.';
    }

    if (empty($errors)) {
        if ($userReview) {
            $errors[] = 'You have already reviewed this movie.';
        } else {
            $stmt = $pdo->prepare("INSERT INTO reviews (movie_id, user_id, rating, review_text) VALUES (?, ?, ?, ?)");
            $stmt->execute([$movieId, getCurrentUserId(), $rating, $reviewText]);
            setFlash('success', 'Review submitted successfully! ⭐');
            header("Location: movie.php?id=$movieId");
            exit;
        }
    }
}

require_once 'includes/header.php';
?>

<div class="container movie-detail">
    <div class="movie-detail-grid">
        <!-- Poster -->
        <div class="movie-poster-large">
            <?php if ($movie['poster']): ?>
                <img src="<?php echo sanitize($movie['poster']); ?>" alt="<?php echo sanitize($movie['title']); ?>">
            <?php else: ?>
                <div class="poster-placeholder">
                    <span class="placeholder-icon">🎬</span>
                    <span class="placeholder-title"><?php echo sanitize($movie['title']); ?></span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Info -->
        <div class="movie-info">
            <h1><?php echo sanitize($movie['title']); ?></h1>
            
            <div class="movie-meta-row">
                <span class="genre-tag"><?php echo sanitize($movie['genre']); ?></span>
                <span class="year-tag">📅 <?php echo $movie['release_year']; ?></span>
                <?php if ($movie['added_by_name']): ?>
                    <span class="text-muted" style="font-size:0.85rem;">Added by <?php echo sanitize($movie['added_by_name']); ?></span>
                <?php endif; ?>
            </div>

            <!-- Rating Summary -->
            <div class="movie-rating-big">
                <div class="rating-value"><?php echo number_format($avgRating, 1); ?></div>
                <div class="rating-details">
                    <?php echo renderStars($avgRating); ?>
                    <span class="rating-count"><?php echo $reviewCount; ?> review<?php echo $reviewCount !== 1 ? 's' : ''; ?></span>
                </div>
            </div>

            <?php if ($movie['description']): ?>
                <p class="movie-description"><?php echo nl2br(sanitize($movie['description'])); ?></p>
            <?php endif; ?>

            <!-- Reviews -->
            <div class="movie-detail-section">
                <h3>Reviews (<?php echo $reviewCount; ?>)</h3>
                
                <?php if (!empty($reviews)): ?>
                    <div class="reviews-list">
                        <?php foreach ($reviews as $review): ?>
                        <div class="card review-card <?php echo (isLoggedIn() && $review['user_id'] == getCurrentUserId()) ? 'own-review' : ''; ?>">
                            <div class="review-header">
                                <div class="review-avatar"><?php echo getInitials($review['username']); ?></div>
                                <div class="review-user-info">
                                    <div class="review-username">
                                        <?php echo sanitize($review['username']); ?>
                                        <?php if (isLoggedIn() && $review['user_id'] == getCurrentUserId()): ?>
                                            <span style="font-size:0.75rem;color:var(--secondary);margin-left:6px;">You</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="review-date"><?php echo date('M j, Y \a\t g:i A', strtotime($review['created_at'])); ?></div>
                                </div>
                                <?php echo renderStars($review['rating']); ?>
                            </div>
                            <p class="review-content"><?php echo nl2br(sanitize($review['review_text'])); ?></p>
                            
                            <?php if (isLoggedIn() && $review['user_id'] == getCurrentUserId()): ?>
                            <div class="review-actions">
                                <a href="edit_review.php?id=<?php echo $review['id']; ?>" class="btn btn-outline btn-sm">✏️ Edit</a>
                                <button class="btn btn-danger btn-sm" data-delete="delete_review.php?id=<?php echo $review['id']; ?>&movie_id=<?php echo $movieId; ?>">🗑️ Delete</button>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state" style="padding: 40px 0;">
                        <div class="empty-icon">💬</div>
                        <h3>No Reviews Yet</h3>
                        <p>Be the first to review this movie!</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Add Review Form -->
            <?php if (isLoggedIn() && !$userReview): ?>
            <div class="review-form-section">
                <h4>Write a Review</h4>
                
                <?php if (!empty($errors)): ?>
                    <?php foreach ($errors as $err): ?>
                        <div class="form-error mb-2">⚠ <?php echo $err; ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <form method="POST" action="movie.php?id=<?php echo $movieId; ?>">
                    <div class="star-rating-group">
                        <label>Your Rating</label>
                        <?php echo renderStars(0, true); ?>
                    </div>

                    <div class="form-group">
                        <label for="review_text">Your Review</label>
                        <textarea id="review_text" name="review_text" class="form-control" 
                                  placeholder="Share your thoughts about this movie..." required
                        ><?php echo sanitize($_POST['review_text'] ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </form>
            </div>
            <?php elseif (!isLoggedIn()): ?>
            <div class="review-form-section text-center">
                <p style="color:var(--text-muted);">
                    <a href="login.php" class="btn btn-primary">Log in</a> to write a review.
                </p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal">
        <h3>Delete Review?</h3>
        <p>Are you sure you want to delete this review? This action cannot be undone.</p>
        <div class="modal-actions">
            <button class="btn btn-outline" id="cancelDelete">Cancel</button>
            <a href="#" class="btn btn-danger" id="confirmDelete">Delete</a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
