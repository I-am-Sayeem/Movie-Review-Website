<<<<<<< HEAD
# CineVault

CineVault is a modern, responsive web application for movie enthusiasts to discover, review, and rate their favorite films. Built with Core PHP and MySQL, it provides a seamless community experience for film lovers.

## Features

- **Movie Discovery:** Browse a wide collection of movies with detailed information including genre, release year, and synopsis.
- **Reviews & Ratings:** Share your thoughts and rate movies on a 1-5 star scale. Read reviews from other community members.
- **Trending System:** View the top-rated trending movies and the latest community reviews directly on the home page.
- **User Authentication:** Secure user registration, login, and session management.
- **User Profiles:** Manage your profile and view your past review history.
- **Crowdsourced Content:** Authenticated users can contribute to the database by adding new movies and uploading posters.
- **Dark/Light Mode:** Built-in, persistent theme toggle to switch between dark and light viewing experiences.
- **Responsive Design:** Fully responsive UI built with modern HTML and CSS, optimized for desktop and mobile devices.

## Tech Stack

- **Frontend:** HTML5, CSS3 (Custom Properties & Flexbox/Grid), Vanilla JavaScript
- **Backend:** Core PHP (PHP 7.4+)
- **Database:** MySQL (via PDO)

## Getting Started

Follow these instructions to set up the project on your local machine for development and testing.

### Prerequisites

- A local web server stack such as XAMPP, WAMP, or MAMP.
- PHP 7.4 or higher.
- MySQL server.

### Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/I-am-Sayeem/Movie-Review-Website.git
   ```

2. **Move to Web Root:**
   Place the cloned project folder inside your web server's document root (e.g., `htdocs` for XAMPP or `www` for WAMP).

3. **Database Configuration:**
   The application is designed to automatically initialize the database and tables upon the first connection.
   
   Ensure your MySQL server is running. If your local MySQL setup requires a password, verify and update the database credentials in `config/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'cinevault');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

4. **Run the Application:**
   Open your web browser and navigate to the project directory (e.g., `http://localhost/Movie-Review-Website/`).
   
   *Note: On your first visit, the system will automatically create the `cinevault` database, build all necessary tables, and inject demo data.*

### Demo Account

To test the platform immediately, you can log in using the following demo credentials:
- **Email:** demo@cinevault.com
- **Password:** demo1234

## Project Structure

```text
Movie-Review-Website/
├── assets/
│   ├── css/          # Core stylesheets and theme variables
│   └── images/       # Movie posters and UI graphics
├── config/
│   └── database.php  # Database connection and auto-setup script
├── includes/
│   ├── auth.php      # Session and authentication helpers
│   ├── footer.php    # Global footer template
│   └── header.php    # Global header and navigation template
├── index.php         # Home page (Trending movies, latest reviews)
├── movies.php        # Complete movie catalog
├── movie.php         # Single movie details and reviews
├── add_movie.php     # Form to add a new movie
├── profile.php       # User dashboard
├── login.php         # User login page
├── register.php      # User registration page
└── logout.php        # User logout script
```

## Contributing

Contributions, issues, and feature requests are welcome.
1. Fork the project.
2. Create your feature branch (`git checkout -b feature/NewFeature`).
3. Commit your changes (`git commit -m 'Add NewFeature'`).
4. Push to the branch (`git push origin feature/NewFeature`).
5. Open a Pull Request.

## License

This project is licensed under the MIT License.
=======
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
>>>>>>> ff31af1f105313a2aace1bd505b943d3b78c60e9
