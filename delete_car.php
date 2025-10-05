<?php
include_once 'config/database.php';
include_once 'src/Car.php';
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

$car = new Car($conn);

if (isset($_GET['id'])) {
    $car->id = $_GET['id'];
    if ($car->delete()) {
        header("Location: admin.php");
    } else {
        echo "Unable to delete car.";
    }
}
?>