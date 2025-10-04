<?php view('partials/header', ['title' => 'My Attendance']); ?>
<?php view('partials/sidebar'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h1>My Attendance</h1>
        </div>
        <div class="card-body">
            <p>Here is a summary of your attendance record.</p>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($attendance_records)): ?>
                            <tr>
                                <td colspan="3" class="text-center">No attendance has been recorded for you yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($attendance_records as $record): ?>
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
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<?php view('partials/footer'); ?>