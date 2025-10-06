<?php
include_once 'config/database.php';
include_once 'src/Rental.php';
include_once 'src/Car.php';
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['rental_id']) && isset($_GET['car_id'])) {
    $rental_id = $_GET['rental_id'];
    $car_id = $_GET['car_id'];

    // Update rental record
    $rental = new Rental($conn);
    $rental->id = $rental_id;
    $rental->return_date = date('Y-m-d H:i:s');

    // Update car status
    $car = new Car($conn);
    $car->id = $car_id;
    $car->status = 'available';

    if ($rental->update() && $car->updateStatus()) {
        header("Location: rentals.php?message=Car returned successfully.");
    } else {
        header("Location: rentals.php?message=Failed to return car.");
    }
} else {
    header('Location: rentals.php');
    exit;
}
?>
