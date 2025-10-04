<?php view('partials/header', ['title' => 'Manage Students']); ?>
<?php view('partials/sidebar'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h1>Manage Students</h1>
            <a href="<?php echo base_url('admin/createStudent'); ?>" class="btn btn-primary">Add New Student</a>
        </div>
        <div class="card-body">

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error_message'])): ?>
                 <div class="alert alert-danger" role="alert">
                    <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Student Code</th>
                            <th>Grade</th>
                            <th>Section</th>
                            <th>Homeroom Teacher</th>
                            <th>Parent</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($students)): ?>
                            <tr>
                                <td colspan="8" class="text-center">No students found. Click "Add New Student" to get started.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo e($student['id']); ?></td>
                                    <td><?php echo e($student['fullname']); ?></td>
                                    <td><?php echo e($student['student_code']); ?></td>
                                    <td><?php echo e($student['grade_level']); ?></td>
                                    <td><?php echo e($student['section']); ?></td>
                                    <td><?php echo e($student['teacher_name']); ?></td>
                                    <td><?php echo e($student['parent_name']); ?></td>
                                    <td>
                                        <a href="<?php echo base_url('admin/editStudent/' . $student['id']); ?>" class="btn btn-sm btn-info">Edit</a>
                                        <form action="<?php echo base_url('admin/deleteStudent/' . $student['id']); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this student?');">
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<?php view('partials/footer'); ?>