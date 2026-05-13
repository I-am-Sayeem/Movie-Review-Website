<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function getCurrentUserId(): ?int {
    return $_SESSION['user_id'] ?? null;
}

function getCurrentUsername(): ?string {
    return $_SESSION['username'] ?? null;
}

function getInitials(string $name): string {
    $parts = explode(' ', trim($name));
    if (count($parts) >= 2) {
        return strtoupper($parts[0][0] . $parts[1][0]);
    }
    return strtoupper(substr($name, 0, 2));
}

function sanitize(string $str): string {
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function getAverageRating(PDO $pdo, int $movieId): float {
    $stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating FROM reviews WHERE movie_id = ?");
    $stmt->execute([$movieId]);
    $result = $stmt->fetch();
    return round($result['avg_rating'] ?? 0, 1);
}

function getReviewCount(PDO $pdo, int $movieId): int {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reviews WHERE movie_id = ?");
    $stmt->execute([$movieId]);
    return (int) $stmt->fetchColumn();
}

function renderStars(float $rating, bool $interactive = false, string $name = 'rating'): string {
    $html = '<div class="stars-display' . ($interactive ? ' stars-interactive' : '') . '">';
    for ($i = 1; $i <= 5; $i++) {
        $filled = $i <= round($rating) ? ' filled' : '';
        if ($interactive) {
            $html .= '<input type="radio" name="' . $name . '" value="' . $i . '" id="star' . $i . '" ' . ($i == round($rating) ? 'checked' : '') . ' hidden>';
            $html .= '<label for="star' . $i . '" class="star-label' . $filled . '" data-value="' . $i . '">★</label>';
        } else {
            $html .= '<span class="star' . $filled . '">★</span>';
        }
    }
    if (!$interactive && $rating > 0) {
        $html .= '<span class="rating-number">' . number_format($rating, 1) . '</span>';
    }
    $html .= '</div>';
    return $html;
}
