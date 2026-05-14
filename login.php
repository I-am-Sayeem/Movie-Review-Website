<?php
$pageTitle = 'Login';
require_once 'includes/auth.php';

if (isLoggedIn()) {
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
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            setFlash('success', 'Welcome back, ' . $user['username'] . '! 🎬');
            header('Location: index.php');
            exit;
        } else {
            $errors['email'] = 'Invalid email or password.';
        }
    }
}


require_once 'includes/header.php';
?>

<div class="auth-page">
    <div class="card form-card">
        <h2>Welcome Back</h2>
        <p class="form-subtitle">Log in to your CineVault account</p>

        <form method="POST" action="login.php" novalidate>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" 
                       placeholder="your@email.com" value="<?php echo sanitize($oldEmail); ?>" required>
                <?php if (isset($errors['email'])): ?>
                    <div class="form-error">⚠ <?php echo $errors['email']; ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" 
                       placeholder="Enter your password" required>
                <?php if (isset($errors['password'])): ?>
                    <div class="form-error">⚠ <?php echo $errors['password']; ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary w-full btn-lg">Log In</button>
        </form>

        <div class="form-footer">
            Don't have an account? <a href="register.php">Sign up</a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
