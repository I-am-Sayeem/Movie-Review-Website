<?php
$pageTitle = 'Home';
require_once 'includes/header.php';

// Fetch stats
$totalMovies = $pdo->query("SELECT COUNT(*) FROM movies")->fetchColumn();
$totalReviews = $pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

// Trending movies (top 6 by average rating, minimum 1 review)
$trendingStmt = $pdo->query("
    SELECT m.*, 
           COALESCE(AVG(r.rating), 0) as avg_rating,
           COUNT(r.id) as review_count
    FROM movies m
    LEFT JOIN reviews r ON m.id = r.movie_id
    GROUP BY m.id
    HAVING review_count > 0
    ORDER BY avg_rating DESC, review_count DESC
    LIMIT 6
");
$trendingMovies = $trendingStmt->fetchAll();

// Latest reviews with movie + user info
$latestStmt = $pdo->query("
    SELECT r.*, 
           u.username, 
           m.title as movie_title, m.id as movie_id
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    JOIN movies m ON r.movie_id = m.id
    ORDER BY r.created_at DESC
    LIMIT 6
");
$latestReviews = $latestStmt->fetchAll();
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1 class="animate-in">
            Discover & Review<br>
            <span class="gradient-text">Your Favorite Movies</span>
        </h1>
        <p class="animate-in animate-delay-1">
            Join our community of movie lovers. Share your thoughts, explore ratings, 
            and find your next favorite film.
        </p>
        <div class="hero-search animate-in animate-delay-2">
            <span class="search-icon">🔍</span>
            <input type="text" id="heroSearch" placeholder="Search movies by title..." autocomplete="off">
        </div>
        <div class="hero-stats animate-in animate-delay-3">
            <div class="hero-stat">
                <div class="stat-number" data-count="<?php echo $totalMovies; ?>">0</div>
                <div class="stat-label">Movies</div>
            </div>
            <div class="hero-stat">
                <div class="stat-number" data-count="<?php echo $totalReviews; ?>">0</div>
                <div class="stat-label">Reviews</div>
            </div>
            <div class="hero-stat">
                <div class="stat-number" data-count="<?php echo $totalUsers; ?>">0</div>
                <div class="stat-label">Members</div>
            </div>
        </div>
    </div>
</section>

<!-- Trending Section -->
<?php if (!empty($trendingMovies)): ?>
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>🔥 Trending Now</h2>
            <p>Top rated movies by our community</p>
        </div>
        <div class="trending-grid">
            <?php foreach ($trendingMovies as $i => $movie): ?>
            <a href="movie.php?id=<?php echo $movie['id']; ?>" class="trending-card card animate-on-scroll">
                <span class="trending-rank"><?php echo $i + 1; ?></span>
                <?php if ($movie['poster']): ?>
                    <img src="<?php echo sanitize($movie['poster']); ?>" alt="<?php echo sanitize($movie['title']); ?>" class="trending-poster">
                <?php else: ?>
                    <div class="trending-placeholder">
                        <span class="t-icon">🎬</span>
                        <span class="t-title"><?php echo sanitize($movie['title']); ?></span>
                    </div>
                <?php endif; ?>
                <div class="trending-info">
                    <h4><?php echo sanitize($movie['title']); ?></h4>
                    <div class="trending-meta">
                        <span class="genre-tag"><?php echo sanitize($movie['genre']); ?></span>
                        <?php echo renderStars($movie['avg_rating']); ?>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Latest Reviews Section -->
<?php if (!empty($latestReviews)): ?>
<section class="section" style="padding-top: 0;">
    <div class="container">
        <div class="section-header">
            <h2>💬 Latest Reviews</h2>
            <p>What our community is saying</p>
        </div>
        <div class="latest-reviews-grid">
            <?php foreach ($latestReviews as $review): ?>
            <div class="card review-card animate-on-scroll">
                <a href="movie.php?id=<?php echo $review['movie_id']; ?>" class="review-movie-title">
                    🎬 <?php echo sanitize($review['movie_title']); ?>
                </a>
                <?php echo renderStars($review['rating']); ?>
                <p class="review-content">
                    <?php echo sanitize(mb_strimwidth($review['review_text'], 0, 150, '...')); ?>
                </p>
                <div class="review-header" style="margin-bottom: 0;">
                    <div class="review-avatar"><?php echo getInitials($review['username']); ?></div>
                    <div class="review-user-info">
                        <div class="review-username"><?php echo sanitize($review['username']); ?></div>
                        <div class="review-date"><?php echo date('M j, Y', strtotime($review['created_at'])); ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="movies.php" class="btn btn-outline">Browse All Movies →</a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
