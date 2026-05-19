<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'cinevault');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    // First connect without DB to create it if needed
    $pdo_init = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo_init->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo_init->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo_init = null;

    // Connect to the actual database
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    // Auto-create tables if they don't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role ENUM('user','admin') NOT NULL DEFAULT 'user',
            avatar VARCHAR(255) DEFAULT NULL,
            bio TEXT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;

        CREATE TABLE IF NOT EXISTS movies (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            genre VARCHAR(100) NOT NULL,
            release_year INT NOT NULL,
            description TEXT,
            poster VARCHAR(255) DEFAULT NULL,
            added_by INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (added_by) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB;

        CREATE TABLE IF NOT EXISTS reviews (
            id INT AUTO_INCREMENT PRIMARY KEY,
            movie_id INT NOT NULL,
            user_id INT NOT NULL,
            rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
            review_text TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_review (movie_id, user_id),
            FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    ");

    // Add role column if it doesn't exist (for existing databases)
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN role ENUM('user','admin') NOT NULL DEFAULT 'user' AFTER password");
    } catch (PDOException $e) {
        // Column already exists, ignore
    }

    // Ensure at least one admin exists (for existing databases)
    $adminCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
    if ($adminCount == 0) {
        $adminPass = password_hash('admin1234', PASSWORD_DEFAULT);
        try {
            $pdo->exec("INSERT INTO users (username, email, password, role, bio) VALUES
                ('admin', 'admin@cinevault.com', '$adminPass', 'admin', 'CineVault Administrator 🛡️')
            ");
        } catch (PDOException $e) {
            // Admin user may already exist with 'user' role, upgrade it
            $pdo->exec("UPDATE users SET role = 'admin' WHERE username = 'admin'");
        }
    }

    // Seed sample data if the users table is empty
    $count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($count == 0) {
        // Create admin user (password: admin1234)
        $adminPass = password_hash('admin1234', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO users (username, email, password, role, bio) VALUES
            ('admin', 'admin@cinevault.com', '$adminPass', 'admin', 'CineVault Administrator 🛡️')
        ");

        // Create demo users (password: demo1234)
        $demoPass = password_hash('demo1234', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO users (username, email, password, role, bio) VALUES
            ('cinephile', 'demo@cinevault.com', '$demoPass', 'user', 'Movie lover & reviewer. 🎬'),
            ('filmcritic', 'critic@cinevault.com', '$demoPass', 'user', 'Professional film analyst.'),
            ('moviebuff', 'buff@cinevault.com', '$demoPass', 'user', 'Watching movies since 1999.')
        ");

        // Seed movies (admin=1, cinephile=2, filmcritic=3, moviebuff=4)
        $pdo->exec("INSERT INTO movies (title, genre, release_year, description, poster, added_by) VALUES
            ('The Dark Knight', 'Action', 2008, 'When the menace known as the Joker wreaks havoc and chaos on the people of Gotham, Batman must accept one of the greatest psychological and physical tests of his ability to fight injustice.', 'assets/images/posters/dark_knight.png', 1),
            ('Inception', 'Sci-Fi', 2010, 'A thief who steals corporate secrets through the use of dream-sharing technology is given the inverse task of planting an idea into the mind of a C.E.O.', 'assets/images/posters/inception.png', 1),
            ('Parasite', 'Thriller', 2019, 'Greed and class discrimination threaten the newly formed symbiotic relationship between the wealthy Park family and the destitute Kim clan.', 'assets/images/posters/parasite.png', 1),
            ('The Shawshank Redemption', 'Drama', 1994, 'Over the course of several years, two convicts form a friendship, seeking consolation and, eventually, redemption through basic compassion.', 'assets/images/posters/shawshank.png', 1),
            ('Spirited Away', 'Animation', 2001, 'During her family\\'s move to the suburbs, a sullen 10-year-old girl wanders into a world ruled by gods, witches, and spirits.', 'assets/images/posters/spirited_away.png', 1),
            ('Get Out', 'Horror', 2017, 'A young African-American visits his white girlfriend\\'s parents for the weekend, where his simmering uneasiness about their reception of him eventually reaches a boiling point.', 'assets/images/posters/get_out.png', 1),
            ('La La Land', 'Romance', 2016, 'While navigating their careers in Los Angeles, a pianist and an actress fall in love while attempting to reconcile their aspirations for the future.', 'assets/images/posters/la_la_land.png', 1),
            ('The Grand Budapest Hotel', 'Comedy', 2014, 'A writer encounters the owner of an aging high-class hotel, who tells him of his early years serving as a lobby boy in the hotel\\'s glorious years.', 'assets/images/posters/grand_budapest.png', 1),
            ('Interstellar', 'Sci-Fi', 2014, 'A team of explorers travel through a wormhole in space in an attempt to ensure humanity\\'s survival.', 'assets/images/posters/interstellar.png', 1),
            ('Pulp Fiction', 'Thriller', 1994, 'The lives of two mob hitmen, a boxer, a gangster and his wife, and a pair of diner bandits intertwine in four tales of violence and redemption.', 'assets/images/posters/pulp_fiction.png', 1),
            ('The Social Network', 'Drama', 2010, 'Harvard student Mark Zuckerberg creates the social networking site that would become known as Facebook.', 'assets/images/posters/social_network.png', 1),
            ('Everything Everywhere All at Once', 'Sci-Fi', 2022, 'An aging Chinese immigrant is swept up in an insane adventure, where she alone can save what\\'s important to her by connecting with the lives she could have led.', 'assets/images/posters/everything_everywhere.png', 1)
        ");

        // Seed reviews (cinephile=2, filmcritic=3, moviebuff=4)
        $pdo->exec("INSERT INTO reviews (movie_id, user_id, rating, review_text) VALUES
            (1, 2, 5, 'A masterpiece of superhero cinema. Heath Ledger\\'s Joker is iconic and unforgettable. The dark, gritty tone elevates this far beyond a typical comic book movie.'),
            (1, 3, 5, 'Christopher Nolan at his finest. The action sequences are breathtaking and the moral dilemmas keep you thinking long after the credits roll.'),
            (1, 4, 4, 'Incredible performances all around. The only reason I give it 4 stars is because the third act drags slightly, but otherwise near-perfect.'),
            (2, 2, 5, 'Mind-bending and visually stunning. The concept of dreams within dreams is executed flawlessly. Hans Zimmer\\'s score is hauntingly beautiful.'),
            (2, 3, 4, 'A complex narrative that rewards multiple viewings. DiCaprio delivers a powerful performance. Some may find it overly complicated.'),
            (3, 3, 5, 'A brilliant social commentary wrapped in a thrilling narrative. Bong Joon-ho masterfully blends genres to create something truly unique.'),
            (3, 4, 5, 'Deserved every Oscar it won. The tension builds perfectly and the twists are shocking yet logical. A modern classic.'),
            (4, 2, 5, 'The greatest movie ever made, period. Tim Robbins and Morgan Freeman deliver performances that touch the soul. Hope is a powerful thing.'),
            (4, 4, 5, 'A timeless story of hope and friendship. Every scene is perfectly crafted. This movie gets better with every viewing.'),
            (5, 4, 5, 'Miyazaki\\'s imagination knows no bounds. The animation is breathtaking and the story is both whimsical and deeply moving.'),
            (5, 2, 4, 'A beautiful and fantastical journey. The world-building is incredible, though the pacing can feel slow at times.'),
            (6, 3, 4, 'Jordan Peele\\'s directorial debut is a masterclass in social horror. Smart, unsettling, and deeply relevant.'),
            (7, 4, 4, 'A gorgeous love letter to dreamers and artists. The musical numbers are enchanting, though the ending may divide audiences.'),
            (7, 2, 5, 'Ryan Gosling and Emma Stone have incredible chemistry. The cinematography is absolutely stunning. A modern musical masterpiece.'),
            (8, 2, 4, 'Wes Anderson\\'s most charming film. The visual style is impeccable and Ralph Fiennes is hilarious. A delightful cinematic treat.'),
            (9, 3, 5, 'An epic space odyssey that combines hard science with raw emotion. The docking scene had me on the edge of my seat. Nolan\\'s most ambitious work.'),
            (9, 2, 4, 'Visually spectacular and emotionally resonant. The science is mostly accurate which adds to the immersion. A must-see in IMAX.'),
            (10, 4, 5, 'Tarantino\\'s magnum opus. The non-linear storytelling, razor-sharp dialogue, and unforgettable characters make this a film for the ages.'),
            (10, 2, 5, 'Every scene crackles with energy and wit. The soundtrack is perfect. Samuel L. Jackson and John Travolta are electric together.'),
            (11, 2, 4, 'David Fincher crafts a compelling tale of ambition and betrayal. Jesse Eisenberg is perfect as Zuckerberg. Aaron Sorkin\\'s script is razor-sharp.'),
            (12, 3, 5, 'A wildly creative and deeply emotional film. Michelle Yeoh is phenomenal. It somehow manages to be both absurd and profoundly moving.')
        ");
    }

} catch (PDOException $e) {
    die('<div style="font-family:monospace;color:#ff6b6b;background:#1a1a2e;padding:30px;margin:20px;border-radius:12px;">
        <h2>⚠️ Database Connection Error</h2>
        <p>' . $e->getMessage() . '</p>
        <p>Make sure MySQL is running and the credentials in <code>config/database.php</code> are correct.</p>
    </div>');
}
