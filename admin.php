<?php
include_once 'config/database.php';
include_once 'src/Car.php';
session_start();

// simple authentication check
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}


$car = new Car($conn);

// Handle form submissions for adding/editing cars
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_car'])) {
        $car->name = $_POST['name'];
        $car->price = $_POST['price'];

        // handle image upload
        $image_name = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_path = "public/images/" . $image_name;

        if (move_uploaded_file($image_tmp, $image_path)) {
            $car->image = $image_name;
            if ($car->create()) {
                header("Location: admin.php");
            } else {
                echo "<div class='alert alert-danger'>Unable to add car.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Unable to upload image.</div>";
        }
    }
}

$cars = $car->readAll();
$statusCounts = $car->getStatusCounts();

$status_labels = [];
$status_data = [];
foreach ($statusCounts as $row) {
    $status_labels[] = $row['status'];
    $status_data[] = $row['count'];
}

?>

<?php include 'includes/header.php'; ?>

<div class="container">
    <h2>Admin Panel</h2>

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="admin.php">Dashboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="rentals.php">Rentals</a>
        </li>
    </ul>

    <!-- Charts -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Car Status Overview
                </div>
                <div class="card-body">
                    <canvas id="carStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Car Form -->
    <div class="card mb-4">
        <div class="card-header">
            Add New Car
        </div>
        <div class="card-body">
            <form action="admin.php" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">Car Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">Price per day</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Car Image</label>
                    <input type="file" class="form-control" id="image" name="image" required>
                </div>
                <button type="submit" name="add_car" class="btn btn-primary">Add Car</button>
            </form>
        </div>
    </div>

    <!-- Car List -->
    <h3>Car List</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Price/day</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cars as $c) : ?>
                <tr>
                    <td><?php echo $c['id']; ?></td>
                    <td><img src="public/images/<?php echo $c['image']; ?>" alt="<?php echo $c['name']; ?>" width="100"></td>
                    <td><?php echo $c['name']; ?></td>
                    <td>$<?php echo $c['price']; ?></td>
                    <td><span class="badge bg-<?php echo $c['status'] === 'available' ? 'success' : 'warning'; ?>"><?php echo $c['status']; ?></span></td>
                    <td>
                        <a href="edit_car.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-info">Edit</a>
                        <a href="delete_car.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    var car_status_labels = <?php echo json_encode($status_labels); ?>;
    var car_status_data = <?php echo json_encode($status_data); ?>;
</script>
<?php include 'includes/footer.php'; ?>