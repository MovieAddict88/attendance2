<?php view('partials/header', ['title' => "Children's Grades"]); ?>
<?php view('partials/sidebar'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h1>Children's Grades</h1>
        </div>
        <div class="card-body">
            <p>Here is a summary of the grades for each of your children.</p>

            <?php if (empty($grades_by_child)): ?>
                <div class="alert alert-info">No children found or no grades recorded yet.</div>
            <?php else: ?>
                <?php foreach ($grades_by_child as $child_name => $grades): ?>
                    <h3 class="mt-4"><?php echo e($child_name); ?></h3>
                    <?php if (empty($grades)): ?>
                        <p>No grades have been recorded for <?php echo e($child_name); ?> yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
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
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                    <hr>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php view('partials/footer'); ?>