<?php view('partials/header', ['title' => 'Student Dashboard']); ?>
<?php view('partials/sidebar'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h1>Student Dashboard</h1>
        </div>
        <div class="card-body">
            <p>Welcome, <?php echo e($user_fullname); ?>! Here you can check your academic progress.</p>

            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-tasks fa-3x mb-3"></i>
                            <h5 class="card-title">My Assignments</h5>
                            <p class="card-text">View upcoming and submitted assignments.</p>
                            <a href="#" class="btn btn-primary">View Assignments</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-line fa-3x mb-3"></i>
                            <h5 class="card-title">My Grades</h5>
                            <p class="card-text">Check your latest quiz and assignment grades.</p>
                            <a href="#" class="btn btn-primary">View Grades</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-bullhorn fa-3x mb-3"></i>
                            <h5 class="card-title">Announcements</h5>
                            <p class="card-text">See the latest news and updates from the school.</p>
                            <a href="#" class="btn btn-primary">View Announcements</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php view('partials/footer'); ?>