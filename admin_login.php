<?php
$pageTitle = 'Admin Login';
require_once 'includes/auth.php';

// If already logged in as admin, go to dashboard
if (isLoggedIn() && isAdmin()) {
    header('Location: admin_dashboard.php');
    exit;
}

// If logged in as regular user, show access denied
if (isLoggedIn() && !isAdmin()) {
    setFlash('error', 'Access denied. Admin privileges required.');
    header('Location: index.php');
    exit;
}

$errors = [];
$oldEmail = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config/database.php';

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $oldEmail = $email;

    if (empty($email)) {
        $errors['email'] = 'Email is required.';
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = 'admin';
            setFlash('success', 'Welcome back, Admin ' . $user['username'] . '! 🛡️');
            header('Location: admin_dashboard.php');
            exit;
        } else {
            $errors['email'] = 'Invalid admin credentials.';
        }
    }
}

require_once 'includes/header.php';
?>

<!-- Particles Background -->
<div id="particles-bg" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; pointer-events: none;"></div>

<div class="auth-page" style="position: relative; z-index: 1;">
    <div class="card form-card">
        <div class="admin-login-badge">🛡️</div>
        <h2>Admin Login</h2>
        <p class="form-subtitle">CineVault Administration Panel</p>

        <form method="POST" action="admin_login.php" novalidate>
            <div class="form-group">
                <label for="email">Admin Email</label>
                <input type="email" id="email" name="email" class="form-control" 
                       placeholder="admin@cinevault.com" value="<?php echo sanitize($oldEmail); ?>" required>
                <?php if (isset($errors['email'])): ?>
                    <div class="form-error">⚠ <?php echo $errors['email']; ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password">Admin Password</label>
                <input type="password" id="password" name="password" class="form-control" 
                       placeholder="Enter admin password" required>
                <?php if (isset($errors['password'])): ?>
                    <div class="form-error">⚠ <?php echo $errors['password']; ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-admin w-full btn-lg">🔐 Admin Sign In</button>
        </form>

        <div class="form-footer">
            Not an admin? <a href="login.php">Regular login</a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
