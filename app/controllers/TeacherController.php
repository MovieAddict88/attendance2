<?php
// app/controllers/TeacherController.php

require_once BASE_PATH . '/app/core/BaseController.php';

class TeacherController extends BaseController {

    public function __construct($config) {
        parent::__construct($config);
        // Authorize access for teachers only
        $this->authorize(['teacher']);
    }

    /**
     * Display the main dashboard for a Teacher.
     */
    public function index() {
        $data = [
            'title' => 'Teacher Dashboard',
            'user_fullname' => $_SESSION['user_fullname']
        ];
        view('teacher/index', $data);
    }

    /**
     * Display the attendance management page.
     */
    public function attendance() {
        $attendance_model = $this->model('Attendance');
        $teacher_id = $_SESSION['user_id'];

        $data = [
            'title' => 'Manage Attendance',
            'students' => $attendance_model->getStudentsByTeacher($teacher_id),
            'date' => date('Y-m-d') // Default to today
        ];
        view('teacher/attendance/index', $data);
    }

    /**
     * Save the attendance records from the form submission.
     */
    public function saveAttendance() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(base_url('teacher/attendance'));
        }

        $attendance_model = $this->model('Attendance');
        $teacher_id = $_SESSION['user_id'];
        $date = $_POST['attendance_date'];
        $statuses = $_POST['status']; // This will be an array of [student_id => status]
        $notes = $_POST['notes']; // This will be an array of [student_id => note]

        $attendance_data = [];
        foreach ($statuses as $student_id => $status) {
            $attendance_data[$student_id] = [
                'status' => $status,
                'note' => $notes[$student_id] ?? ''
            ];
        }

        if ($attendance_model->saveAttendance($teacher_id, $date, $attendance_data)) {
            $_SESSION['success_message'] = "Attendance for {$date} saved successfully.";
        } else {
            $_SESSION['error_message'] = "Failed to save attendance.";
        }

        redirect(base_url('teacher/attendance'));
    }

    /*--- Quiz Management ---*/

    /**
     * Display the quiz management page.
     */
    public function quizzes() {
        $quiz_model = $this->model('Quiz');
        $teacher_id = $_SESSION['user_id'];

        $data = [
            'title' => 'Manage Quizzes',
            'quizzes' => $quiz_model->getAllByTeacher($teacher_id)
        ];
        view('teacher/quizzes/index', $data);
    }

    /**
     * Show the form for creating a new quiz.
     */
    public function createQuiz() {
        $data = [
            'title' => 'Create New Quiz'
        ];
        view('teacher/quizzes/create', $data);
    }

    /**
     * Store a new quiz in the database.
     */
    public function storeQuiz() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(base_url('teacher/quizzes'));
        }

        $quiz_model = $this->model('Quiz');
        $data = [
            'teacher_id' => $_SESSION['user_id'],
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'grade_level' => $_POST['grade_level'],
            'section' => $_POST['section'],
            'due_date' => $_POST['due_date']
        ];

        if ($quiz_model->create($data)) {
            $_SESSION['success_message'] = "Quiz created successfully.";
            redirect(base_url('teacher/quizzes'));
        } else {
            $_SESSION['error_message'] = "Failed to create quiz.";
            redirect(base_url('teacher/createQuiz'));
        }
    }
}