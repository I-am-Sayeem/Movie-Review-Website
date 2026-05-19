<?php
$pageTitle = 'Add Movie';
require_once 'includes/auth.php';
requireAdmin();

$errors = [];
$old = ['title' => '', 'genre' => '', 'release_year' => '', 'description' => ''];

$genres = ['Action', 'Animation', 'Comedy', 'Documentary', 'Drama', 'Horror', 'Romance', 'Sci-Fi', 'Thriller'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config/database.php';

    $title = trim($_POST['title'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    $releaseYear = intval($_POST['release_year'] ?? 0);
    $description = trim($_POST['description'] ?? '');

    $old = compact('title', 'genre', 'description');
    $old['release_year'] = $releaseYear;

   // Validation
if (strlen($title) < 1) {
    $errors['title'] = 'Movie title is required.';
}
if (!in_array($genre, $genres)) {
    $errors['genre'] = 'Please select a valid genre.';
}
if ($releaseYear < 1888 || $releaseYear > (int)date('Y') + 5) {
    $errors['release_year'] = 'Please enter a valid release year.';
}
if (strlen($description) < 10) {
    $errors['description'] = 'Description must be at least 10 characters.';


    }


    // Handle poster upload
    $posterPath = null;
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $fileType = mime_content_type($_FILES['poster']['tmp_name']);
        
        if (!in_array($fileType, $allowed)) {
            $errors['poster'] = 'Only JPG, PNG, WebP, and GIF images are allowed.';
        } elseif ($_FILES['poster']['size'] > 5 * 1024 * 1024) {
            $errors['poster'] = 'Image must be under 5MB.';
        } else {
            $ext = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid('poster_') . '.' . $ext;
            $targetPath = 'assets/images/posters/' . $fileName;
            
            if (move_uploaded_file($_FILES['poster']['tmp_name'], $targetPath)) {
                $posterPath = $targetPath;
            } else {
                $errors['poster'] = 'Failed to upload image.';
            }
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO movies (title, genre, release_year, description, poster, added_by) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $genre, $releaseYear, $description, $posterPath, getCurrentUserId()]);
        $newId = $pdo->lastInsertId();
        setFlash('success', 'Movie added successfully! 🎬');
        header("Location: movie.php?id=$newId");
        exit;
    }
}

require_once 'includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1><span class="emoji">🎬</span> Add a Movie</h1>
    </div>

    <div class="card form-card wide">
        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $field => $err): ?>
                <div class="form-error mb-1">⚠ <?php echo $err; ?></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <form method="POST" action="add_movie.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Movie Title</label>
                <input type="text" id="title" name="title" class="form-control" 
                       placeholder="Enter the movie title" value="<?php echo sanitize($old['title']); ?>" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="genre">Genre</label>
                    <select id="genre" name="genre" class="form-control" required>
                        <option value="">Select a genre</option>
                        <?php foreach ($genres as $g): ?>
                            <option value="<?php echo $g; ?>" <?php echo $old['genre'] === $g ? 'selected' : ''; ?>>
                                <?php echo $g; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="release_year">Release Year</label>
                    <input type="number" id="release_year" name="release_year" class="form-control" 
                           placeholder="e.g. 2024" min="1888" max="<?php echo date('Y') + 5; ?>"
                           value="<?php echo $old['release_year'] ?: ''; ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" 
                          placeholder="Write a brief synopsis of the movie..."
                ><?php echo sanitize($old['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Movie Poster (optional)</label>
                <div class="file-upload">
                    <input type="file" name="poster" accept="image/*">
                    <div class="upload-icon">📁</div>
                    <p>Click or drag an image to upload</p>
                    <div class="file-name" style="display:none;"></div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-full">Add Movie</button>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
