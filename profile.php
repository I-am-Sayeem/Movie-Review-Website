<?php
$pageTitle = 'Profile';
require_once 'includes/auth.php';
requireLogin();
require_once 'config/database.php';

$userId = getCurrentUserId();

// Fetch user info
$userStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$userStmt->execute([$userId]);
$user = $userStmt->fetch();

// Handle profile update
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $bio = trim($_POST['bio'] ?? '');
    $username = trim($_POST['username'] ?? '');

    if (strlen($username) < 3) {
        $errors['username'] = 'Username must be at least 3 characters.';
    } else {
        // Check uniqueness (excluding self)
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $checkStmt->execute([$username, $userId]);
        if ($checkStmt->fetch()) {
            $errors['username'] = 'Username already taken.';
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE users SET username = ?, bio = ? WHERE id = ?");
        $stmt->execute([$username, $bio, $userId]);
        $_SESSION['username'] = $username;
        setFlash('success', 'Profile updated! ✅');
        header('Location: profile.php');
        exit;
    }
}

// Fetch user's reviews with movie info
$reviewsStmt = $pdo->prepare("
    SELECT r.*, m.title as movie_title, m.id as movie_id, m.genre, m.release_year
    FROM reviews r
    JOIN movies m ON r.movie_id = m.id
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
");
$reviewsStmt->execute([$userId]);
$userReviews = $reviewsStmt->fetchAll();

// Stats
$totalReviews = count($userReviews);
$avgRating = 0;
if ($totalReviews > 0) {
    $sum = array_sum(array_column($userReviews, 'rating'));
    $avgRating = round($sum / $totalReviews, 1);
}

require_once 'includes/header.php';
?>

<div class="container">
    <!-- Profile Header -->
    <section class="profile-header-section">
        <div class="card profile-card">
            <div class="profile-avatar-large"><?php echo getInitials($user['username']); ?></div>
            <div class="profile-info">
                <h2><?php echo sanitize($user['username']); ?></h2>
                <p class="profile-email"><?php echo sanitize($user['email']); ?></p>
                <?php if ($user['bio']): ?>
                    <p class="profile-bio"><?php echo sanitize($user['bio']); ?></p>
                <?php endif; ?>
                <p class="text-muted mt-1" style="font-size:0.82rem;">
                    Member since <?php echo date('F Y', strtotime($user['created_at'])); ?>
                </p>
            </div>
            <div class="profile-stats">
                <div class="profile-stat">
                    <div class="stat-value"><?php echo $totalReviews; ?></div>
                    <div class="stat-label">Reviews</div>
                </div>
                <div class="profile-stat">
                    <div class="stat-value"><?php echo $avgRating; ?></div>
                    <div class="stat-label">Avg Rating</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Edit Profile -->
    <section class="edit-profile-section">
        <div class="card form-card wide" style="margin:0 0 32px;">
            <h2 style="font-size:1.3rem;">Edit Profile</h2>
            <p class="form-subtitle">Update your username and bio</p>

            <form method="POST" action="profile.php">
                <input type="hidden" name="update_profile" value="1">

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" 
                           value="<?php echo sanitize($user['username']); ?>" required>
                    <?php if (isset($errors['username'])): ?>
                        <div class="form-error">⚠ <?php echo $errors['username']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="bio">Bio</label>
                    <textarea id="bio" name="bio" class="form-control" placeholder="Tell us about yourself..."
                    ><?php echo sanitize($user['bio'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </section>

    <!-- User Reviews -->
     
    <section class="section" style="padding-top:0;">
        <div class="section-header" style="text-align:left;">
            <h2>📝 Your Reviews</h2>
        </div>

        <?php if (empty($userReviews)): ?>
            <div class="empty-state">
                <div class="empty-icon">💬</div>
                <h3>No Reviews Yet</h3>
                <p>Start exploring movies and share your thoughts!</p>
                <a href="movies.php" class="btn btn-primary">Browse Movies</a>
            </div>
        <?php else: ?>
            <div class="reviews-list">
                <?php foreach ($userReviews as $review): ?>
                <div class="card review-card">
                    <div class="review-header">
                        <div class="review-avatar"><?php echo getInitials($user['username']); ?></div>
                        <div class="review-user-info">
                            <a href="movie.php?id=<?php echo $review['movie_id']; ?>" class="review-username" style="color:var(--primary-light);">
                                🎬 <?php echo sanitize($review['movie_title']); ?>
                            </a>
                            <div class="review-date">
                                <span class="genre-tag" style="font-size:0.65rem;"><?php echo sanitize($review['genre']); ?></span>
                                • <?php echo $review['release_year']; ?> 
                                • <?php echo date('M j, Y', strtotime($review['created_at'])); ?>
                            </div>
                        </div>
                        <?php echo renderStars($review['rating']); ?>
                    </div>
                    <p class="review-content"><?php echo nl2br(sanitize($review['review_text'])); ?></p>
                    <div class="review-actions">
                        <a href="edit_review.php?id=<?php echo $review['id']; ?>" class="btn btn-outline btn-sm">✏️ Edit</a>
                        <button class="btn btn-danger btn-sm" data-delete="delete_review.php?id=<?php echo $review['id']; ?>">🗑️ Delete</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>

<!-- Delete Modal -->

<div class="modal-overlay" id="deleteModal">
    <div class="modal">
        <h3>Delete Review?</h3>
        <p>Are you sure? This cannot be undone.</p>
        <div class="modal-actions">
            <button class="btn btn-outline" id="cancelDelete">Cancel</button>
            <a href="#" class="btn btn-danger" id="confirmDelete">Delete</a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
