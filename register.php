<?php
$pageTitle = 'Sign Up';
require_once 'includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$errors = [];
$old = ['username' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config/database.php';

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    $old['username'] = $username;
    $old['email'] = $email;

    // Validation
    if (strlen($username) < 3 || strlen($username) > 50) {
        $errors['username'] = 'Username must be 3-50 characters.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors['username'] = 'Username can only contain letters, numbers, and underscores.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    if (strlen($password) < 6) {
        $errors['password'] = 'Password must be at least 6 characters.';
    }

    if ($password !== $confirmPassword) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        // Check uniqueness
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors['username'] = 'Username or email already taken.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $hash]);
            setFlash('success', 'Account created successfully! Please log in.');
            header('Location: login.php');
            exit;
        }
    }
}

require_once 'includes/header.php';
?>

<div class="auth-page">
    <div class="card form-card">
        <h2>Create Account</h2>
        <p class="form-subtitle">Join the CineVault community</p>

        <form method="POST" action="register.php" novalidate>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" 
                       placeholder="Choose a username" value="<?php echo sanitize($old['username']); ?>" required>
                <?php if (isset($errors['username'])): ?>
                    <div class="form-error">⚠ <?php echo $errors['username']; ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" 
                       placeholder="your@email.com" value="<?php echo sanitize($old['email']); ?>" required>
                <?php if (isset($errors['email'])): ?>
                    <div class="form-error">⚠ <?php echo $errors['email']; ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" 
                       placeholder="At least 6 characters" required>
                <?php if (isset($errors['password'])): ?>
                    <div class="form-error">⚠ <?php echo $errors['password']; ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" 
                       placeholder="Repeat your password" required>
                <?php if (isset($errors['confirm_password'])): ?>
                    <div class="form-error">⚠ <?php echo $errors['confirm_password']; ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary w-full btn-lg">Sign Up</button>
        </form>

        <div class="form-footer">
            Already have an account? <a href="login.php">Log in</a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
