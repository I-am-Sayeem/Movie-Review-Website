<?php
require 'config/database.php';

$trendingStmt = $pdo->query("
    SELECT m.*, 
           COALESCE(AVG(r.rating), 0) as avg_rating,
           COUNT(r.id) as review_count
    FROM movies m
    LEFT JOIN reviews r ON m.id = r.movie_id
    GROUP BY m.id
    ORDER BY avg_rating DESC, review_count DESC
    LIMIT 50
");
$trendingMovies = $trendingStmt->fetchAll();

$domeImages = [];
if (!empty($trendingMovies)) {
    foreach ($trendingMovies as $movie) {
        if (!empty($movie['poster'])) {
            $domeImages[] = [
                'src' => $movie['poster'],
                'alt' => $movie['title'],
                'title' => $movie['title'],
                'rating' => round($movie['avg_rating'], 1),
                'genre' => $movie['genre']
            ];
        }
    }
}
echo "Count of domeImages: " . count($domeImages) . "\n";
print_r($domeImages);
