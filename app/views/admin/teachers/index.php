<?php view('partials/header', ['title' => 'Manage Teachers']); ?>
<?php view('partials/sidebar'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h1>Manage Teachers</h1>
            <a href="<?php echo base_url('admin/createTeacher'); ?>" class="btn btn-primary">Add New Teacher</a>
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
                            <th>Email</th>
                            <th>Teacher Code</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($teachers)): ?>
                            <tr>
                                <td colspan="6" class="text-center">No teachers found. Click "Add New Teacher" to get started.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($teachers as $teacher): ?>
                                <tr>
                                    <td><?php echo e($teacher['id']); ?></td>
                                    <td><?php echo e($teacher['fullname']); ?></td>
                                    <td><?php echo e($teacher['email']); ?></td>
                                    <td><?php echo e($teacher['teacher_code']); ?></td>
                                    <td><?php echo e($teacher['phone']); ?></td>
                                    <td>
                                        <a href="<?php echo base_url('admin/editTeacher/' . $teacher['id']); ?>" class="btn btn-sm btn-info">Edit</a>
                                        <form action="<?php echo base_url('admin/deleteTeacher/' . $teacher['id']); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this teacher?');">
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