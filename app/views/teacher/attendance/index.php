<?php view('partials/header', ['title' => 'Manage Attendance']); ?>
<?php view('partials/sidebar'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h1>Manage Attendance</h1>
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

            <form action="<?php echo base_url('teacher/saveAttendance'); ?>" method="POST">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="attendance_date" class="form-label">Select Date</label>
                        <input type="date" id="attendance_date" name="attendance_date" class="form-control" value="<?php echo e($date); ?>">
                    </div>
                    <div class="col-md-8 align-self-end text-end">
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="markAll('present')">Mark All Present</button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="markAll('absent')">Mark All Absent</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Status</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($students)): ?>
                                <tr>
                                    <td colspan="3" class="text-center">You have no students assigned to your homeroom.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td><?php echo e($student['fullname']); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <input type="radio" class="btn-check" name="status[<?php echo $student['id']; ?>]" id="present_<?php echo $student['id']; ?>" value="present" checked>
                                                <label class="btn btn-outline-primary" for="present_<?php echo $student['id']; ?>">Present</label>

                                                <input type="radio" class="btn-check" name="status[<?php echo $student['id']; ?>]" id="absent_<?php echo $student['id']; ?>" value="absent">
                                                <label class="btn btn-outline-primary" for="absent_<?php echo $student['id']; ?>">Absent</label>

                                                <input type="radio" class="btn-check" name="status[<?php echo $student['id']; ?>]" id="late_<?php echo $student['id']; ?>" value="late">
                                                <label class="btn btn-outline-primary" for="late_<?php echo $student['id']; ?>">Late</label>

                                                <input type="radio" class="btn-check" name="status[<?php echo $student['id']; ?>]" id="excused_<?php echo $student['id']; ?>" value="excused">
                                                <label class="btn btn-outline-primary" for="excused_<?php echo $student['id']; ?>">Excused</label>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" name="notes[<?php echo $student['id']; ?>]" class="form-control" placeholder="Optional notes...">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (!empty($students)): ?>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Save Attendance</button>
                </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<script>
function markAll(status) {
    const radios = document.querySelectorAll(`input[type="radio"][value="${status}"]`);
    radios.forEach(radio => radio.checked = true);
}
</script>

<?php view('partials/footer'); ?>