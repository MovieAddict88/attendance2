<?php
include_once 'config/database.php';
include_once 'src/Car.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

$car = new Car($conn);
$cars = $car->readAll($search, $status);
?>
<?php include 'includes/header.php'; ?>

<div class="container">
    <h1 class="my-4">Find Your Ride</h1>
    <form action="index.php" method="get" class="mb-4">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <input type="text" name="search" class="form-control" placeholder="Search by car name..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <select name="status" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="Available" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Available') ? 'selected' : ''; ?>>Available</option>
                        <option value="Rented" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Rented') ? 'selected' : ''; ?>>Rented</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-block">Filter</button>
            </div>
        </div>
    </form>

    <div class="row">
        <?php if (empty($cars)) : ?>
            <div class="col">
                <p class="text-center">No cars found that match your criteria.</p>
            </div>
        <?php else : ?>
            <?php foreach ($cars as $row) : ?>
                <div class="col-md-4 mb-4">
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
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>