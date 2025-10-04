<?php view('partials/header', ['title' => "Children's Attendance"]); ?>
<?php view('partials/sidebar'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h1>Children's Attendance</h1>
        </div>
        <div class="card-body">
            <p>Here is a summary of the attendance records for each of your children.</p>

            <?php if (empty($attendance_by_child)): ?>
                <div class="alert alert-info">No children found or no attendance recorded yet.</div>
            <?php else: ?>
                <?php foreach ($attendance_by_child as $child_name => $records): ?>
                    <h3 class="mt-4"><?php echo e($child_name); ?></h3>
                    <?php if (empty($records)): ?>
                        <p>No attendance has been recorded for <?php echo e($child_name); ?> yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($records as $record): ?>
                                        <tr>
                                            <td><?php echo e(date('M d, Y', strtotime($record['date']))); ?></td>
                                            <td>
                                                <?php
                                                    $status = e($record['status']);
                                                    $badge_class = 'bg-secondary';
                                                    if ($status == 'present') $badge_class = 'bg-success';
                                                    if ($status == 'absent') $badge_class = 'bg-danger';
                                                    if ($status == 'late') $badge_class = 'bg-warning text-dark';
                                                    if ($status == 'excused') $badge_class = 'bg-info';
                                                ?>
                                                <span class="badge <?php echo $badge_class; ?>"><?php echo ucfirst($status); ?></span>
                                            </td>
                                            <td><?php echo e($record['note']); ?></td>
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