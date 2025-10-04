<?php view('partials/header', ['title' => 'Manage Quizzes']); ?>
<?php view('partials/sidebar'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h1>Manage Quizzes</h1>
            <a href="<?php echo base_url('teacher/createQuiz'); ?>" class="btn btn-primary">Create New Quiz</a>
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
                            <th>Title</th>
                            <th>Grade Level</th>
                            <th>Section</th>
                            <th>Due Date</th>
                            <th>Created On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($quizzes)): ?>
                            <tr>
                                <td colspan="6" class="text-center">No quizzes found. Click "Create New Quiz" to get started.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($quizzes as $quiz): ?>
                                <tr>
                                    <td><?php echo e($quiz['title']); ?></td>
                                    <td><?php echo e($quiz['grade_level']); ?></td>
                                    <td><?php echo e($quiz['section']); ?></td>
                                    <td><?php echo e(date('M d, Y', strtotime($quiz['due_date']))); ?></td>
                                    <td><?php echo e(date('M d, Y', strtotime($quiz['created_at']))); ?></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-primary">View</a>
                                        <a href="#" class="btn btn-sm btn-info">Edit</a>
                                        <a href="#" class="btn btn-sm btn-danger">Delete</a>
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