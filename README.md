# userinfo

# User Information CRUD App (PHP + HTML + CSS + JS)

This is a simple User Information system built with **PHP**, **MySQL**, and a ** HTML/JS** frontend.
-PHP (Core)
- MySQL (Database)
- HTML, CSS, and  JavaScript (Frontend)
- REST-style API (via `api.php`)

- Add a new user (name, email, password, date of birth)
- Edit and update existing users
- Delete users

- 1. Go to the XAMPP installation directory:
   - Path: `C:\xampp\htdocs\`
2. Create a new folder:
   -  `Goqii-test`
3. Copy all project files into this folder:
   - `index.html`
   - `api.php`
   - Create a database named: `goqiidb`
   - the table query is ---
   - CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `dob` DATE NOT NULL
);
the localhost link is http://localhost/Goqii-test/index.html

   git clone https://github.com/pinu1990/userinfo
