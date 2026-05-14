# Movie Review Website

A modern PHP/MySQL movie review platform designed for building a community of film lovers. Users can browse movies, filter by genre/year/rating, add new titles, submit reviews, and manage their own content.

## Key Features

- User authentication: register, login, logout
- Movie catalog with search and filters (genre, year, rating)
- Movie detail pages with average rating and review feed
- Add new movies with optional poster uploads
- Submit, edit, and delete reviews
- Trending movies and latest reviews on the home page
- Clean responsive UI with animations and interactive controls
- Built-in database creation and seed data for demo use

## Getting Started

### Requirements

- PHP 7.4+ (PHP 8 recommended)
- MySQL / MariaDB
- Web server such as Apache or Nginx
- `PDO` and `pdo_mysql` PHP extensions enabled

### Installation

1. Clone or copy the project into your web server root.
2. Open `config/database.php` and update database credentials if needed:
   - `DB_HOST`
   - `DB_NAME`
   - `DB_USER`
   - `DB_PASS`
3. Make sure the `assets/images/posters/` folder is writable if you want to upload movie posters.
4. Open the site in your browser, for example:
   - `http://localhost/Movie-Review-Website/`

> `config/database.php` automatically creates the database schema and seeds sample users, movies, and reviews.

##  Project Structure

- `index.php` — Home page showing trending movies and latest reviews
- `movies.php` — Movie listing with search/filter/pagination
- `movie.php` — Single movie detail page with review submission
- `add_movie.php` — Add a new movie form
- `login.php`, `register.php`, `logout.php` — User authentication flow
- `edit_review.php`, `delete_review.php` — Review management
- `config/database.php` — PDO database connection and schema seed logic
- `includes/auth.php` — Authentication helpers and sanitization utilities
- `assets/css/style.css` — Main styling
- `assets/js/app.js` — Interactive UI behavior

## Demo Accounts

The project includes seeded demo users with the password `demo1234`:

- `cinephile` — `demo@cinevault.com`
- `filmcritic` — `critic@cinevault.com`
- `moviebuff` — `buff@cinevault.com`

## Usage

- Browse movies from the home page or `movies.php`
- Register or log in to add movies and write reviews
- Use filters to find movies by genre, year, or rating
- Click any movie to read reviews and submit your own

## Notes

- Uploaded posters are stored in `assets/images/posters/`
- The database is created automatically on first load if it does not exist
- For production, secure `config/database.php` and disable automatic schema creation if needed

## License

This project is provided as-is for learning and demo purposes.
