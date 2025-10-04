<?php view('partials/header', ['title' => 'My Grades']); ?>
<?php view('partials/sidebar'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h1>My Grades</h1>
        </div>
        <div class="card-body">
            <p>Here is a summary of your grades for quizzes and assignments.</p>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Item Title</th>
                            <th>Type</th>
                            <th>Grade</th>
                            <th>Quarter</th>
                            <th>Date Recorded</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($grades)): ?>
                            <tr>
                                <td colspan="5" class="text-center">No grades have been recorded for you yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($grades as $grade): ?>
                                <tr>
                                    <td><?php echo e($grade['item_title']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo ($grade['item_type'] == 'Quiz') ? 'success' : 'info'; ?>">
                                            <?php echo e($grade['item_type']); ?>
                                        </span>
                                    </td>
                                    <td><strong><?php echo e(number_format($grade['grade'], 2)); ?>%</strong></td>
                                    <td><?php echo e($grade['quarter']); ?></td>
                                    <td><?php echo e(date('M d, Y', strtotime($grade['created_at']))); ?></td>
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