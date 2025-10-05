<?php
include_once 'config/database.php';
include_once 'src/Car.php';
include_once 'src/Rental.php';

$car = new Car($conn);
$rental = new Rental($conn);

if (isset($_GET['id'])) {
    $car->id = $_GET['id'];
    $car->readOne();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rental->car_id = $_POST['car_id'];
    $rental->customer_name = $_POST['customer_name'];
    $rental->rental_date = date('Y-m-d');

    $car->id = $_POST['car_id'];
    $car->status = 'rented';

    if ($rental->create() && $car->updateStatus()) {
        header("Location: index.php");
    } else {
        echo "<div class='alert alert-danger'>Unable to rent car.</div>";
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container">
    <h2>Rent a Car</h2>
    <div class="row">
        <div class="col-md-6">
            <img src="public/images/<?php echo $car->image; ?>" class="img-fluid" alt="<?php echo $car->name; ?>">
        </div>
        <div class="col-md-6">
            <h3><?php echo $car->name; ?></h3>
            <p>$<?php echo $car->price; ?> / day</p>
            <form action="rent.php" method="post">
                <input type="hidden" name="car_id" value="<?php echo $car->id; ?>">
                <div class="mb-3">
                    <label for="customer_name" class="form-label">Your Name</label>
                    <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                </div>
                <button type="submit" class="btn btn-success">Rent Now</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>