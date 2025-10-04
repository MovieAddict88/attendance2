<?php
// app/controllers/AdminController.php

require_once BASE_PATH . '/app/core/BaseController.php';

class AdminController extends BaseController {

    public function __construct($config) {
        parent::__construct($config);
        // This line ensures only users with the 'superadmin' role can access any method in this controller.
        $this->authorize(['superadmin']);
    }

    /**
     * Display the main dashboard for the Super Admin.
     */
    public function index() {
        $data = [
            'title' => 'Admin Dashboard'
        ];
        view('admin/index', $data);
    }

    /**
     * Display the teacher management page.
     */
    public function teachers() {
        $teacher_model = $this->model('Teacher');
        $data = [
            'title' => 'Manage Teachers',
            'teachers' => $teacher_model->getAll()
        ];
        view('admin/teachers/index', $data);
    }

    /**
     * Show the form for creating a new teacher.
     */
    public function createTeacher() {
        $data = [
            'title' => 'Add New Teacher'
        ];
        view('admin/teachers/create', $data);
    }

    /**
     * Store a new teacher in the database.
     */
    public function storeTeacher() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(base_url('admin/teachers'));
        }

        // Basic validation
        $fullname = $_POST['fullname'] ?? '';
        $email = $_POST['email'] ?? '';
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $teacher_code = $_POST['teacher_code'] ?? '';

        if (empty($fullname) || empty($email) || empty($username) || empty($password)) {
            // Handle error - for now, redirect back
            $_SESSION['error_message'] = "Please fill all required fields.";
            redirect(base_url('admin/createTeacher'));
            return;
        }

        $teacher_model = $this->model('Teacher');

        $data = [
            'fullname' => $fullname,
            'email' => $email,
            'username' => $username,
            'password' => $password, // Will be hashed in the model
            'phone' => $phone,
            'teacher_code' => $teacher_code,
            'bio' => $_POST['bio'] ?? ''
        ];

        if ($teacher_model->create($data)) {
            $_SESSION['success_message'] = "Teacher created successfully.";
            redirect(base_url('admin/teachers'));
        } else {
            $_SESSION['error_message'] = "Failed to create teacher. Username or email may already exist.";
            redirect(base_url('admin/createTeacher'));
        }
    }

    /**
     * Show the form for editing a teacher.
     */
    public function editTeacher($id) {
        $teacher_model = $this->model('Teacher');
        $teacher = $teacher_model->findById($id);

        if (!$teacher) {
            $_SESSION['error_message'] = "Teacher not found.";
            redirect(base_url('admin/teachers'));
        }

        $data = [
            'title' => 'Edit Teacher',
            'teacher' => $teacher
        ];
        view('admin/teachers/edit', $data);
    }

    /**
     * Update a teacher's information.
     */
    public function updateTeacher($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(base_url('admin/teachers'));
        }

        $teacher_model = $this->model('Teacher');
        $data = [
            'fullname' => $_POST['fullname'],
            'email' => $_POST['email'],
            'username' => $_POST['username'],
            'password' => $_POST['password'], // Keep empty to not change
            'phone' => $_POST['phone'],
            'teacher_code' => $_POST['teacher_code'],
            'bio' => $_POST['bio']
        ];

        if ($teacher_model->update($id, $data)) {
            $_SESSION['success_message'] = "Teacher updated successfully.";
            redirect(base_url('admin/teachers'));
        } else {
            $_SESSION['error_message'] = "Failed to update teacher.";
            redirect(base_url('admin/editTeacher/' . $id));
        }
    }

    /**
     * Delete a teacher.
     */
    public function deleteTeacher($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(base_url('admin/teachers'));
        }

        $teacher_model = $this->model('Teacher');
        if ($teacher_model->delete($id)) {
            $_SESSION['success_message'] = "Teacher deleted successfully.";
        } else {
            $_SESSION['error_message'] = "Failed to delete teacher. They may be linked to other records.";
        }
        redirect(base_url('admin/teachers'));
    }

    /*--- Student Management ---*/

    /**
     * Display the student management page.
     */
    public function students() {
        $student_model = $this->model('Student');
        $data = [
            'title' => 'Manage Students',
            'students' => $student_model->getAll()
        ];
        view('admin/students/index', $data);
    }

    /**
     * Show the form for creating a new student.
     */
    public function createStudent() {
        $student_model = $this->model('Student');
        $data = [
            'title' => 'Add New Student',
            'teachers' => $student_model->getUsersByRole('teacher'),
            'parents' => $student_model->getUsersByRole('parent')
        ];
        view('admin/students/create', $data);
    }

    /**
     * Store a new student in the database.
     */
    public function storeStudent() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(base_url('admin/students'));
        }

        $student_model = $this->model('Student');
        $data = [
            'fullname' => $_POST['fullname'],
            'email' => $_POST['email'],
            'username' => $_POST['username'],
            'password' => $_POST['password'],
            'student_code' => $_POST['student_code'],
            'grade_level' => $_POST['grade_level'],
            'section' => $_POST['section'],
            'homeroom_teacher_id' => $_POST['homeroom_teacher_id'] ?: null,
            'birthdate' => $_POST['birthdate'] ?: null,
            'parent_id' => $_POST['parent_id'] ?: null,
        ];

        if ($student_model->create($data)) {
            $_SESSION['success_message'] = "Student created successfully.";
            redirect(base_url('admin/students'));
        } else {
            $_SESSION['error_message'] = "Failed to create student. Username or email may already exist.";
            redirect(base_url('admin/createStudent'));
        }
    }

    /**
     * Show the form for editing a student.
     */
    public function editStudent($id) {
        $student_model = $this->model('Student');
        $student = $student_model->findById($id);

        if (!$student) {
            $_SESSION['error_message'] = "Student not found.";
            redirect(base_url('admin/students'));
        }

        $data = [
            'title' => 'Edit Student',
            'student' => $student,
            'teachers' => $student_model->getUsersByRole('teacher'),
            'parents' => $student_model->getUsersByRole('parent')
        ];
        view('admin/students/edit', $data);
    }

    /**
     * Update a student's information.
     */
    public function updateStudent($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(base_url('admin/students'));
        }

        $student_model = $this->model('Student');
        $data = [
            'fullname' => $_POST['fullname'],
            'email' => $_POST['email'],
            'username' => $_POST['username'],
            'password' => $_POST['password'], // Keep empty to not change
            'student_code' => $_POST['student_code'],
            'grade_level' => $_POST['grade_level'],
            'section' => $_POST['section'],
            'homeroom_teacher_id' => $_POST['homeroom_teacher_id'] ?: null,
            'birthdate' => $_POST['birthdate'] ?: null,
            'parent_id' => $_POST['parent_id'] ?: null,
        ];

        if ($student_model->update($id, $data)) {
            $_SESSION['success_message'] = "Student updated successfully.";
            redirect(base_url('admin/students'));
        } else {
            $_SESSION['error_message'] = "Failed to update student.";
            redirect(base_url('admin/editStudent/' . $id));
        }
    }

    /**
     * Delete a student.
     */
    public function deleteStudent($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(base_url('admin/students'));
        }

        $student_model = $this->model('Student');
        if ($student_model->delete($id)) {
            $_SESSION['success_message'] = "Student deleted successfully.";
        } else {
            $_SESSION['error_message'] = "Failed to delete student. They may be linked to other records.";
        }
        redirect(base_url('admin/students'));
    }
}