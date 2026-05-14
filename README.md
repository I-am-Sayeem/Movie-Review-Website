# CineVault — Movie Review Website

CineVault is a modern, responsive web application for movie enthusiasts to discover, review, and rate their favorite films. Built with PHP and MySQL, it offers a seamless community experience with a premium dark/light mode design.

## 🎬 Features

- **Discover Movies**: Browse a wide collection of movies with details like genre, release year, and synopsis.
- **Reviews & Ratings**: Share your thoughts and rate movies on a 1-5 star scale. Read reviews from other community members.
- **Trending & Latest**: View the top-rated trending movies and the latest community reviews on the home page.
- **User Accounts**: Secure authentication system with registration and login functionalities.
- **User Profiles**: Manage your profile and see your review history.
- **Add New Movies**: Authenticated users can contribute to the database by adding new movies along with posters.
- **Dark & Light Mode**: Built-in, persistent theme toggle to switch between dark and light viewing experiences.
- **Responsive Design**: Fully responsive UI built with modern HTML and CSS, looking great on desktop and mobile.

## 🛠️ Tech Stack

- **Frontend**: HTML5, CSS3 (Vanilla CSS with Custom Properties/Variables), JavaScript (Vanilla for interactivity and theme toggling).
- **Backend**: PHP (Core PDO).
- **Database**: MySQL.

## 🚀 Getting Started

Follow these instructions to set up the project locally.

### Prerequisites

- A local web server stack like [XAMPP](https://www.apachefriends.org/index.html), [WAMP](https://www.wampserver.com/en/), or [MAMP](https://www.mamp.info/).
- PHP 7.4 or higher.
- MySQL server.

### Installation

1. **Clone the repository** (or download the source code):
   ```bash
   git clone https://github.com/I-am-Sayeem/Movie-Review-Website.git
   ```

2. **Move to Web Root**:
   Place the project folder inside your web server's document root (e.g., `htdocs` for XAMPP or `www` for WAMP).

3. **Database Setup**:
   - The application is designed to auto-initialize the database and tables upon first connection.
   - Ensure your MySQL server is running.
   - If needed, verify the database credentials in `config/database.php`:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'cinevault');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     ```
   - *Note: On the first successful connection, the app will create the `cinevault` database, necessary tables (`users`, `movies`, `reviews`), and seed it with demo data!*

4. **Run the Application**:
   - Open your web browser and navigate to `http://localhost/Movie-Review-Website/` (adjust the URL according to your folder name and setup).
   - You can log in using the demo account:
     - **Email**: demo@cinevault.com
     - **Password**: demo1234
   - Or, create a new account to start exploring!

## 📂 Project Structure

```text
├── assets/
│   ├── css/          # Stylesheets (style.css)
│   └── images/       # Posters and other image assets
├── config/
│   └── database.php  # Database connection, auto-setup & seeding
├── includes/
│   ├── auth.php      # Authentication utility functions
│   ├── footer.php    # Global footer template
│   └── header.php    # Global header template (includes navigation)
├── index.php         # Home page (Trending movies, latest reviews)
├── movies.php        # Browse all movies
├── movie.php         # Single movie details and reviews
├── add_movie.php     # Form to add a new movie
├── edit_review.php   # Edit an existing review
├── delete_review.php # Delete a review
├── login.php         # User login page
├── register.php      # User registration page
├── logout.php        # User logout script
└── profile.php       # User profile page
```

## 🤝 Contributing

Contributions, issues, and feature requests are welcome! Feel free to check the issues page if you want to contribute.

## 📄 License

This project is licensed under the MIT License.
