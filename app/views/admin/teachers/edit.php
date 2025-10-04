<?php view('partials/header', ['title' => 'Edit Teacher']); ?>
<?php view('partials/sidebar'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h1>Edit Teacher: <?php echo e($teacher['fullname']); ?></h1>
        </div>
        <div class="card-body">

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo base_url('admin/updateTeacher/' . $teacher['id']); ?>" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="fullname" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo e($teacher['fullname']); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo e($teacher['email']); ?>" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo e($teacher['username']); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep current password">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="teacher_code" class="form-label">Teacher Code</label>
                            <input type="text" class="form-control" id="teacher_code" name="teacher_code" value="<?php echo e($teacher['teacher_code']); ?>">
                        </div>
                    </div>
                     <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo e($teacher['phone']); ?>">
                        </div>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label for="bio" class="form-label">Bio / Description</label>
                    <textarea class="form-control" id="bio" name="bio" rows="3"><?php echo e($teacher['bio']); ?></textarea>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Update Teacher</button>
                    <a href="<?php echo base_url('admin/teachers'); ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>

        </div>
    </div>
</div>

<?php view('partials/footer'); ?>