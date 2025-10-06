# PHP Car Rental System

This is a modern, responsive car rental management system built with PHP and MySQL. It features automatic database and table creation, a user-friendly interface for browsing cars, and a secure admin panel for managing the car inventory and monitoring rentals.

## Features

- **Automatic Database Setup:** The application automatically creates the required database and tables on its first run.
- **Responsive Design:** A fluid layout built with Bootstrap that works on all screen sizes.
- **Admin Dashboard:** A secure area for administrators to manage the car inventory.
- **CRUD Functionality:** Admins can add, view, edit, and delete cars.
- **Rental Monitoring:** The admin dashboard includes a chart to visualize the status of cars (Available vs. Rented).
- **Dummy Data:** The system is pre-populated with sample cars to demonstrate its features.

## Prerequisites

To run this application locally, you will need a web server environment that supports PHP and MySQL, such as:
- XAMPP
- WAMP
- MAMP
- A custom LEMP/LAMP stack

## Installation and Setup

1.  **Clone the repository** to your local web server's root directory (e.g., `htdocs` in XAMPP).
    ```bash
    git clone <repository-url>
    ```

2.  **Configure the database connection:**
    - Navigate to the `config/` directory.
    - Rename `config.php.example` to `config.php`.
    - Open the new `config.php` file and update the following lines with your local MySQL database credentials:
      ```php
      define('DB_SERVER', 'localhost');
      define('DB_USERNAME', 'your_username'); // e.g., 'root'
      define('DB_PASSWORD', 'your_password'); // e.g., ''
      define('DB_NAME', 'car_rental');
      ```
      The script will automatically create the `car_rental` database for you, but the user must have the necessary permissions.

3.  **Access the application:**
    - Start your web server (e.g., Apache and MySQL in XAMPP).
    - Open your web browser and navigate to the project's URL (e.g., `http://localhost/your-project-folder/`).

4.  **Admin Login:**
    - To access the admin panel, navigate to `http://localhost/your-project-folder/login.php`.
    - Use the default credentials:
      - **Username:** `admin`
      - **Password:** `password`

That's it! The application should now be fully functional. The database, tables, and sample data will be created automatically when you first visit any page.