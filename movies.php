<?php
$pageTitle = 'Browse Movies';
require_once 'includes/header.php';

// Require login to access movies
if (!isLoggedIn()) {
    setFlash('error', 'Please log in to browse movies.');
    header('Location: login.php');
    exit;
}

// Get filter parameters
$search = trim($_GET['search'] ?? '');
$genre = trim($_GET['genre'] ?? '');
$year = trim($_GET['year'] ?? '');
$rating = trim($_GET['rating'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 12;
$offset = ($page - 1) * $perPage;

// Build query
$where = [];
$params = [];

if ($search) {
    $where[] = "m.title LIKE ?";
    $params[] = "%$search%";
}

if ($genre) {
    $where[] = "m.genre = ?";
    $params[] = $genre;
}

if ($year) {
    $where[] = "m.release_year = ?";
    $params[] = $year;
}

$havingClause = '';
if ($rating) {
    $havingClause = "HAVING avg_rating >= ?";
    $params[] = $rating;
}

$whereString = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Count total
$countSql = "SELECT COUNT(*) FROM (
    SELECT m.id, COALESCE(AVG(r.rating), 0) as avg_rating
    FROM movies m
    LEFT JOIN reviews r ON m.id = r.movie_id
    $whereString
    GROUP BY m.id
    $havingClause
) as sub";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalMovies = $countStmt->fetchColumn();
$totalPages = ceil($totalMovies / $perPage);

// Fetch movies
$sql = "SELECT m.*, 
        COALESCE(AVG(r.rating), 0) as avg_rating,
        COUNT(r.id) as review_count
    FROM movies m
    LEFT JOIN reviews r ON m.id = r.movie_id
    $whereString
    GROUP BY m.id
    $havingClause
    ORDER BY m.created_at DESC
    LIMIT $perPage OFFSET $offset";
$params2 = $params; // same params
$stmt = $pdo->prepare($sql);
$stmt->execute($params2);

$movies = $stmt->fetchAll();

// Get distinct genres & years for filters
$genres = $pdo->query("SELECT DISTINCT genre FROM movies ORDER BY genre")->fetchAll(PDO::FETCH_COLUMN);
$years = $pdo->query("SELECT DISTINCT release_year FROM movies ORDER BY release_year DESC")->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="container">
    <div class="page-header flex-between">
        <h1><span class="emoji">🎬</span> Browse Movies</h1>
        <?php if (isAdmin()): ?>
            <a href="add_movie.php" class="btn btn-primary">+ Add Movie</a>
        <?php endif; ?>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <div class="search-wrap">
            <span class="search-icon">🔍</span>
            <input type="text" id="movieSearch" placeholder="Search by title..." 
                   value="<?php echo sanitize($search); ?>">
        </div>
        <select id="genreFilter" class="filter-select">
            <option value="">All Genres</option>
            <?php foreach ($genres as $g): ?>
                <option value="<?php echo sanitize($g); ?>" <?php echo $genre === $g ? 'selected' : ''; ?>>
                    <?php echo sanitize($g); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <select id="yearFilter" class="filter-select">
            <option value="">All Years</option>
            <?php foreach ($years as $y): ?>
                <option value="<?php echo $y; ?>" <?php echo $year == $y ? 'selected' : ''; ?>>
                    <?php echo $y; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <select id="ratingFilter" class="filter-select">
            <option value="">Any Rating</option>
            <option value="4" <?php echo $rating == '4' ? 'selected' : ''; ?>>4+ Stars</option>
            <option value="3" <?php echo $rating == '3' ? 'selected' : ''; ?>>3+ Stars</option>
            <option value="2" <?php echo $rating == '2' ? 'selected' : ''; ?>>2+ Stars</option>
        </select>
    </div>

    <p class="results-info"><?php echo $totalMovies; ?> movie<?php echo $totalMovies !== 1 ? 's' : ''; ?> found</p>

    <?php if (empty($movies)): ?>
        <div class="empty-state">
            <div class="empty-icon">🎬</div>
            <h3>No Movies Found</h3>
            <p>Try adjusting your search or filters, or add a new movie!</p>
            <?php if (isAdmin()): ?>
                <a href="add_movie.php" class="btn btn-primary">+ Add a Movie</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="movie-grid">
            <?php foreach ($movies as $movie): ?>
            <a href="movie.php?id=<?php echo $movie['id']; ?>" class="card movie-card">
                <div class="poster-wrap">
                    <?php if ($movie['poster']): ?>
                        <img src="<?php echo sanitize($movie['poster']); ?>" alt="<?php echo sanitize($movie['title']); ?>">
                    <?php else: ?>
                        <div class="poster-placeholder">
                            <span class="placeholder-icon">🎬</span>
                            <span class="placeholder-title"><?php echo sanitize($movie['title']); ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="poster-overlay">
                        <div class="overlay-rating">
                            <?php echo renderStars($movie['avg_rating']); ?>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h3><?php echo sanitize($movie['title']); ?></h3>
                    <div class="card-meta">
                        <span class="genre-tag"><?php echo sanitize($movie['genre']); ?></span>
                        <span class="year-tag"><?php echo $movie['release_year']; ?></span>
                    </div>
                    <div class="card-rating">
                        <?php echo renderStars($movie['avg_rating']); ?>
                        <span class="review-count"><?php echo $movie['review_count']; ?> review<?php echo $movie['review_count'] !== 1 ? 's' : ''; ?></span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php
            $queryParams = $_GET;
            if ($page > 1):
                $queryParams['page'] = $page - 1;
            ?>
                <a href="movies.php?<?php echo http_build_query($queryParams); ?>">← Prev</a>
            <?php else: ?>
                <span class="disabled">← Prev</span>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++):
                $queryParams['page'] = $i;
            ?>
                <?php if ($i === $page): ?>
                    <span class="active"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="movies.php?<?php echo http_build_query($queryParams); ?>"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php
            if ($page < $totalPages):
                $queryParams['page'] = $page + 1;
            ?>
                <a href="movies.php?<?php echo http_build_query($queryParams); ?>">Next →</a>
            <?php else: ?>
                <span class="disabled">Next →</span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
