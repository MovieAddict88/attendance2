<?php view('partials/header', ['title' => 'Teacher Dashboard']); ?>
<?php view('partials/sidebar'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h1>Teacher Dashboard</h1>
        </div>
        <div class="card-body">
            <p>Welcome, <?php echo e($user_fullname); ?>! This is your central hub for managing your classes.</p>

            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-clipboard-user fa-3x mb-3"></i>
                            <h5 class="card-title">Manage Attendance</h5>
                            <p class="card-text">Mark daily attendance for your sections.</p>
                            <a href="#" class="btn btn-primary">Take Attendance</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-file-alt fa-3x mb-3"></i>
                            <h5 class="card-title">Create Quiz</h5>
                            <p class="card-text">Design and assign new quizzes to your students.</p>
                            <a href="#" class="btn btn-primary">New Quiz</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-tasks fa-3x mb-3"></i>
                            <h5 class="card-title">Post Assignment</h5>
                            <p class="card-text">Upload assignments for your classes.</p>
                            <a href="#" class="btn btn-primary">New Assignment</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php view('partials/footer'); ?>