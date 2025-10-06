<?php
include_once 'config/database.php';
include_once 'src/Rental.php';
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

$rental = new Rental($conn);
$rentals = $rental->readAll();

?>

<?php include 'includes/header.php'; ?>

<div class="container">
    <h2>Rental Management</h2>

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link" href="admin.php">Dashboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="rentals.php">Rentals</a>
        </li>
    </ul>

    <?php if (isset($_GET['message'])) : ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($_GET['message']); ?>
        </div>
    <?php endif; ?>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Rental ID</th>
                <th>Car Name</th>
                <th>Customer</th>
                <th>Rental Date</th>
                <th>Return Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rentals as $r) : ?>
                <tr>
                    <td><?php echo $r['id']; ?></td>
                    <td><?php echo $r['car_name']; ?></td>
                    <td><?php echo $r['customer_name']; ?></td>
                    <td><?php echo $r['rental_date']; ?></td>
                    <td><?php echo $r['return_date'] ? $r['return_date'] : 'Not Returned'; ?></td>
                    <td>
                        <span class="badge bg-<?php echo $r['return_date'] ? 'secondary' : 'primary'; ?>">
                            <?php echo $r['return_date'] ? 'Returned' : 'Rented'; ?>
                        </span>
                    </td>
                    <td>
                        <?php if (!$r['return_date']) : ?>
                            <a href="return_car.php?rental_id=<?php echo $r['id']; ?>&car_id=<?php echo $r['car_id']; ?>" class="btn btn-sm btn-success">Return Car</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
