<?php
include_once 'config/database.php';
include_once 'src/Car.php';

$car = new Car($conn);
$cars = $car->readAll();
?>
<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="row">
        <?php foreach ($cars as $row) : ?>
            <div class="col-md-4">
                <div class="card car-card">
                    <img src="public/images/<?php echo $row['image']; ?>" class="card-img-top" alt="<?php echo $row['name']; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row['name']; ?></h5>
                        <p class="card-text">$<?php echo $row['price']; ?> / day</p>
                        <a href="rent.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Rent Now</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>