<?php
// app/controllers/StudentController.php

require_once BASE_PATH . '/app/core/BaseController.php';

class StudentController extends BaseController {

    public function __construct($config) {
        parent::__construct($config);
        // Authorize access for students only
        $this->authorize(['student']);
    }

    /**
     * Display the main dashboard for a Student.
     */
    public function index() {
        $data = [
            'title' => 'Student Dashboard',
            'user_fullname' => $_SESSION['user_fullname']
        ];
        view('student/index', $data);
    }

    /**
     * Display the student's grades page.
     */
    public function grades() {
        $grade_model = $this->model('Grade');
        $student_id = $_SESSION['user_id'];

        $data = [
            'title' => 'My Grades',
            'grades' => $grade_model->getGradesByStudent($student_id)
        ];
        view('student/grades', $data);
    }

    /**
     * Display the student's attendance page.
     */
    public function attendance() {
        $attendance_model = $this->model('Attendance');
        $student_id = $_SESSION['user_id'];

        $data = [
            'title' => 'My Attendance',
            'attendance_records' => $attendance_model->getAttendanceByStudentId($student_id)
        ];
        view('student/attendance', $data);
    }
}