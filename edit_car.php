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
    $car->readOne();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $car->id = $_POST['id'];
    $car->name = $_POST['name'];
    $car->price = $_POST['price'];
    $car->status = $_POST['status'];

    if ($car->update()) {
        header("Location: admin.php");
    } else {
        echo "<div class='alert alert-danger'>Unable to update car.</div>";
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container">
    <h2>Edit Car</h2>
    <form action="edit_car.php" method="post">
        <input type="hidden" name="id" value="<?php echo $car->id; ?>">
        <div class="mb-3">
            <label for="name" class="form-label">Car Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo $car->name; ?>" required>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Price per day</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo $car->price; ?>" required>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status">
                <option value="available" <?php echo $car->status == 'available' ? 'selected' : ''; ?>>Available</option>
                <option value="rented" <?php echo $car->status == 'rented' ? 'selected' : ''; ?>>Rented</option>
            </select>
        </div>
        <button type="submit" name="edit_car" class="btn btn-primary">Update Car</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>