<?php
/**
 * Run this file ONCE to update existing movies with poster images.
 * Access via browser: http://localhost/Project/update_posters.php
 * Delete this file after running.
 */
require_once 'config/database.php';

$posters = [
    'The Dark Knight'                    => 'assets/images/posters/dark_knight.png',
    'Inception'                          => 'assets/images/posters/inception.png',
    'Parasite'                           => 'assets/images/posters/parasite.png',
    'The Shawshank Redemption'           => 'assets/images/posters/shawshank.png',
    'Spirited Away'                      => 'assets/images/posters/spirited_away.png',
    'Get Out'                            => 'assets/images/posters/get_out.png',
    'La La Land'                         => 'assets/images/posters/la_la_land.png',
    'The Grand Budapest Hotel'           => 'assets/images/posters/grand_budapest.png',
    'Interstellar'                       => 'assets/images/posters/interstellar.png',
    'Pulp Fiction'                       => 'assets/images/posters/pulp_fiction.png',
    'The Social Network'                 => 'assets/images/posters/social_network.png',
    'Everything Everywhere All at Once'  => 'assets/images/posters/everything_everywhere.png',
    'Demon slayer'                       => 'assets/images/posters/Kimetsu_No_Yaiba_Mugen_Jyo-hen_theatrical_poster.jpg',
];

$stmt = $pdo->prepare("UPDATE movies SET poster = ? WHERE title = ?");
$updated = 0;

foreach ($posters as $title => $path) {
    $stmt->execute([$path, $title]);
    if ($stmt->rowCount() > 0) {
        $updated++;
        echo "✅ Updated: $title → $path<br>";
    } else {
        echo "⚠️ Not found: $title<br>";
    }
}

echo "<br><strong>Done! Updated $updated movies with posters.</strong>";
echo "<br><br><em>You can now delete this file (update_posters.php).</em>";
