<?php view('partials/header', ['title' => 'Edit Student']); ?>
<?php view('partials/sidebar'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h1>Edit Student: <?php echo e($student['fullname']); ?></h1>
        </div>
        <div class="card-body">

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo base_url('admin/updateStudent/' . $student['id']); ?>" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="fullname" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo e($student['fullname']); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo e($student['email']); ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo e($student['username']); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep current password">
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="student_code" class="form-label">Student Code/ID</label>
                            <input type="text" class="form-control" id="student_code" name="student_code" value="<?php echo e($student['student_code']); ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="grade_level" class="form-label">Grade Level</label>
                            <input type="number" class="form-control" id="grade_level" name="grade_level" value="<?php echo e($student['grade_level']); ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="section" class="form-label">Section</label>
                            <input type="text" class="form-control" id="section" name="section" value="<?php echo e($student['section']); ?>">
                        </div>
                    </div>
                </div>
                 <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="homeroom_teacher_id" class="form-label">Homeroom Teacher</label>
                            <select class="form-control" id="homeroom_teacher_id" name="homeroom_teacher_id">
                                <option value="">Select a teacher...</option>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?php echo e($teacher['id']); ?>" <?php echo ($teacher['id'] == $student['homeroom_teacher_id']) ? 'selected' : ''; ?>>
                                        <?php echo e($teacher['fullname']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="parent_id" class="form-label">Parent/Guardian</label>
                            <select class="form-control" id="parent_id" name="parent_id">
                                <option value="">Select a parent...</option>
                                <?php foreach ($parents as $parent): ?>
                                    <option value="<?php echo e($parent['id']); ?>" <?php echo ($parent['id'] == $student['parent_id']) ? 'selected' : ''; ?>>
                                        <?php echo e($parent['fullname']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                 <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="birthdate" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="birthdate" name="birthdate" value="<?php echo e($student['birthdate']); ?>">
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Update Student</button>
                    <a href="<?php echo base_url('admin/students'); ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>

        </div>
    </div>
</div>

<?php view('partials/footer'); ?>