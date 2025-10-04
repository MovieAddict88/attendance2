<?php view('partials/header', ['title' => 'Parent Dashboard']); ?>
<?php view('partials/sidebar'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h1>Parent Dashboard</h1>
        </div>
        <div class="card-body">
            <p>Welcome, <?php echo e($user_fullname); ?>! Here you can monitor your child's academic progress.</p>

            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-clipboard-user fa-3x mb-3"></i>
                            <h5 class="card-title">Child's Attendance</h5>
                            <p class="card-text">View your child's attendance records.</p>
                            <a href="#" class="btn btn-primary">View Attendance</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-line fa-3x mb-3"></i>
                            <h5 class="card-title">Child's Grades</h5>
                            <p class="card-text">Check your child's latest grades and performance.</p>
                            <a href="#" class="btn btn-primary">View Grades</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php view('partials/footer'); ?>