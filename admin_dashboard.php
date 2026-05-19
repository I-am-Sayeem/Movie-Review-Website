<?php
$pageTitle = 'Admin Dashboard';
require_once 'includes/auth.php';
requireAdmin();
require_once 'config/database.php';

// Fetch stats
$totalMovies = $pdo->query("SELECT COUNT(*) FROM movies")->fetchColumn();
$totalReviews = $pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();

// Fetch all movies with stats
$moviesStmt = $pdo->query("
    SELECT m.*, 
           u.username as added_by_name,
           COALESCE(AVG(r.rating), 0) as avg_rating,
           COUNT(r.id) as review_count
    FROM movies m
    LEFT JOIN users u ON m.added_by = u.id
    LEFT JOIN reviews r ON m.id = r.movie_id
    GROUP BY m.id
    ORDER BY m.created_at DESC
");
$movies = $moviesStmt->fetchAll();

// Fetch all users
$usersStmt = $pdo->query("
    SELECT u.*, 
           COUNT(r.id) as review_count
    FROM users u
    LEFT JOIN reviews r ON u.id = r.user_id
    GROUP BY u.id
    ORDER BY u.created_at DESC
");
$users = $usersStmt->fetchAll();

require_once 'includes/header.php';
?>

<div class="container">
    <!-- Admin Header -->
    <div class="page-header flex-between">
        <div>
            <h1><span class="emoji">🛡️</span> Admin Dashboard</h1>
            <p style="color: var(--text-secondary); margin-top: 4px;">Manage movies, users & content</p>
        </div>
        <a href="add_movie.php" class="btn btn-primary">+ Add Movie</a>
    </div>

    <!-- Stats Cards -->
    <div class="admin-stats-grid">
        <div class="admin-stat-card">
            <div class="admin-stat-icon">🎬</div>
            <div class="admin-stat-info">
                <div class="admin-stat-number"><?php echo $totalMovies; ?></div>
                <div class="admin-stat-label">Total Movies</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon">💬</div>
            <div class="admin-stat-info">
                <div class="admin-stat-number"><?php echo $totalReviews; ?></div>
                <div class="admin-stat-label">Total Reviews</div>
            </div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-icon">👥</div>
            <div class="admin-stat-info">
                <div class="admin-stat-number"><?php echo $totalUsers; ?></div>
                <div class="admin-stat-label">Registered Users</div>
            </div>
        </div>
    </div>

    <!-- Movie Management -->
    <section class="admin-section">
        <div class="admin-section-header">
            <h2>🎬 Movie Management</h2>
            <span class="admin-badge"><?php echo $totalMovies; ?> movies</span>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Poster</th>
                        <th>Title</th>
                        <th>Genre</th>
                        <th>Year</th>
                        <th>Rating</th>
                        <th>Reviews</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($movies as $movie): ?>
                    <tr>
                        <td>
                            <div class="admin-poster-thumb">
                                <?php if ($movie['poster']): ?>
                                    <img src="<?php echo sanitize($movie['poster']); ?>" alt="<?php echo sanitize($movie['title']); ?>">
                                <?php else: ?>
                                    <span class="poster-thumb-placeholder">🎬</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <a href="movie.php?id=<?php echo $movie['id']; ?>" class="admin-movie-link">
                                <?php echo sanitize($movie['title']); ?>
                            </a>
                        </td>
                        <td><span class="genre-tag"><?php echo sanitize($movie['genre']); ?></span></td>
                        <td><?php echo $movie['release_year']; ?></td>
                        <td>
                            <span class="admin-rating">
                                ⭐ <?php echo number_format($movie['avg_rating'], 1); ?>
                            </span>
                        </td>
                        <td><?php echo $movie['review_count']; ?></td>
                        <td>
                            <div class="admin-actions">
                                <a href="movie.php?id=<?php echo $movie['id']; ?>" class="btn btn-outline btn-sm" title="View">👁️</a>
                                <button class="btn btn-danger btn-sm" 
                                        data-delete-movie="delete_movie.php?id=<?php echo $movie['id']; ?>"
                                        title="Delete">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- User Management -->
    <section class="admin-section">
        <div class="admin-section-header">
            <h2>👥 User Management</h2>
            <span class="admin-badge"><?php echo count($users); ?> users</span>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Avatar</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Reviews</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td>
                            <div class="admin-user-avatar"><?php echo getInitials($u['username']); ?></div>
                        </td>
                        <td>
                            <strong><?php echo sanitize($u['username']); ?></strong>
                        </td>
                        <td style="color: var(--text-secondary);"><?php echo sanitize($u['email']); ?></td>
                        <td>
                            <?php if ($u['role'] === 'admin'): ?>
                                <span class="admin-role-badge role-admin">🛡️ Admin</span>
                            <?php else: ?>
                                <span class="admin-role-badge role-user">👤 User</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $u['review_count']; ?></td>
                        <td style="color: var(--text-muted); font-size: 0.85rem;">
                            <?php echo date('M j, Y', strtotime($u['created_at'])); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<!-- Delete Movie Modal -->
<div class="modal-overlay" id="deleteMovieModal">
    <div class="modal">
        <h3>🗑️ Delete Movie?</h3>
        <p>This will permanently delete the movie and <strong>all its reviews</strong>. This action cannot be undone.</p>
        <div class="modal-actions">
            <button class="btn btn-outline" id="cancelDeleteMovie">Cancel</button>
            <a href="#" class="btn btn-danger" id="confirmDeleteMovie">Delete Movie</a>
        </div>
    </div>
</div>

<script>
// Delete movie modal
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('deleteMovieModal');
    const confirmBtn = document.getElementById('confirmDeleteMovie');
    const cancelBtn = document.getElementById('cancelDeleteMovie');

    document.querySelectorAll('[data-delete-movie]').forEach(btn => {
        btn.addEventListener('click', function() {
            const url = this.getAttribute('data-delete-movie');
            confirmBtn.href = url;
            modal.classList.add('active');
        });
    });

    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            modal.classList.remove('active');
        });
    }

    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.remove('active');
            }
        });
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
