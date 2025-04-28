
Built by https://www.blackbox.ai

---

```markdown
# Bale's Room

## Project Overview
Bale's Room is a web application that allows users to register, log in, and access different functionalities based on their roles (user or admin). The application utilizes PHP for server-side logic and MySQL as its database for user management.

## Installation

To set up Bale's Room on your local environment, follow these steps:

1. **Clone the repository:**
   ```bash
   git clone <repository_url>
   cd bales_room
   ```

2. **Set up the MySQL database:**
   - Create a new database named `bales_room`.
   - Run the following SQL command to create the `users` table:
     ```sql
     CREATE TABLE users (
         id INT AUTO_INCREMENT PRIMARY KEY,
         nama VARCHAR(255) NOT NULL,
         email VARCHAR(255) NOT NULL UNIQUE,
         password VARCHAR(255) NOT NULL,
         role ENUM('user', 'admin') NOT NULL DEFAULT 'user'
     );
     ```

3. **Configure your database settings:**
   - Open `config.php` and modify the database credentials as needed:
     ```php
     $host = 'localhost'; // Your database host
     $db   = 'bales_room'; // Your database name
     $user = 'root'; // Your database username
     $pass = ''; // Your database password
     ```

4. **Run a local server:**
   You can use PHP's built-in server to run the application. Navigate to the project directory and execute:
   ```bash
   php -S localhost:8000
   ```

5. **Access the application:**
   Open your web browser and go to `http://localhost:8000`.

## Usage

1. **Register a new account:**
   - Navigate to `register.php` to create a new account.
   - Fill in the registration form with your name, email, and password.

2. **Log in to your account:**
   - Go to `login.php` and enter your credentials.

3. **Access different sections:**
   - Depending on your role (user or admin), you will be redirected to different pages after logging in.
   - Users can access `user/rooms.php`, and admins can access `admin/dashboard.php`.

4. **Log out:**
   - To log out, navigate to `logout.php`.

## Features
- User registration and login functionality.
- Role-based access control (admin and user).
- Simple and clean interface using Tailwind CSS.
- Session management to maintain user login state.

## Dependencies
This project primarily relies on PHP and MySQL. No additional libraries or frameworks are specified in the package.json since it's a PHP-based application.

## Project Structure
Here's a brief overview of the project structure:

```
bales_room/
│
├── config.php          # Database configuration and connection setup
├── functions.php       # User authentication and session functions
├── register.php        # User registration page
├── login.php           # User login page
├── logout.php          # User logout functionality
├── index.php           # Redirection based on user login status
└── user/
    └── rooms.php       # Room management for regular users
└── admin/
    └── dashboard.php    # Admin dashboard
```

## Contributing
If you would like to contribute to the project, please follow these steps:
- Fork the repository
- Create a new branch (`git checkout -b feature/YourFeature`)
- Make your changes and commit them (`git commit -m 'Add MyFeature'`)
- Push to the branch (`git push origin feature/YourFeature`)
- Open a pull request

## License
This project is open-source and available under the MIT License.
```
Make sure to replace `<repository_url>` with the actual URL of your Git repository where the project is hosted.