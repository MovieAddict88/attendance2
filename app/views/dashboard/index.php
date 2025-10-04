<?php view('partials/header', ['title' => 'Dashboard']); ?>
<?php view('partials/sidebar'); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h1>Welcome, <?php echo e($user_fullname); ?>!</h1>
                </div>
                <div class="card-body">
                    <p>You are logged in as a <strong><?php echo e(ucfirst($user_role)); ?></strong>.</p>
                    <p>This is your main dashboard. From here, you can access all the features available to your role.</p>

                    <div class="row mt-4">
                        <!-- Example KPI cards -->
                        <div class="col-md-3">
                            <div class="card text-white bg-primary mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Total Students</h5>
                                    <p class="card-text fs-4">1,250</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-success mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Teachers</h5>
                                    <p class="card-text fs-4">75</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-warning mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">New Messages</h5>
                                    <p class="card-text fs-4">3</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                             <div class="card text-white bg-danger mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Attendance</h5>
                                    <p class="card-text fs-4">95%</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php view('partials/footer'); ?>