<?php
include_once 'config.php';

// Create connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($sql) === TRUE) {
    // echo "Database created successfully or already exists<br>";
} else {
    echo "Error creating database: " . $conn->error;
}

// Select the database
$conn->select_db(DB_NAME);

// SQL to create tables
$sql_tables = "
CREATE TABLE IF NOT EXISTS `cars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'available',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `rentals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `car_id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `rental_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `car_id` (`car_id`),
  CONSTRAINT `rentals_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

if ($conn->multi_query($sql_tables)) {
    // echo "Tables created successfully or already exist<br>";
    do {
        // Store first result set
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
} else {
    echo "Error creating tables: " . $conn->error;
}

// Insert dummy data
$sql_insert_cars = "
INSERT INTO `cars` (`name`, `image`, `price`, `status`) VALUES
('Toyota Camry', 'car1.jpg', 50.00, 'available'),
('Honda Accord', 'car2.jpg', 55.00, 'available'),
('Ford Mustang', 'car3.jpg', 75.00, 'available')
ON DUPLICATE KEY UPDATE name=name;
";

// Insert a default admin user
$sql_insert_user = "
INSERT INTO `users` (`username`, `password`) VALUES
('admin', '" . password_hash('password', PASSWORD_DEFAULT) . "')
ON DUPLICATE KEY UPDATE username=username;
";


$conn->query($sql_insert_cars);
$conn->query($sql_insert_user);


// return $conn;
?>