<?php
// app/controllers/ParentController.php

require_once BASE_PATH . '/app/core/BaseController.php';

class ParentController extends BaseController {

    public function __construct($config) {
        parent::__construct($config);
        // Authorize access for parents only
        $this->authorize(['parent']);
    }

    /**
     * Display the main dashboard for a Parent.
     */
    private $parent_model;

    public function __construct($config) {
        parent::__construct($config);
        $this->parent_model = $this->model('ParentModel');
    }

    public function index() {
        $data = [
            'title' => 'Parent Dashboard',
            'user_fullname' => $_SESSION['user_fullname']
        ];
        view('parent/index', $data);
    }

    /**
     * Display the grades for all of the parent's children.
     */
    public function grades() {
        $parent_id = $_SESSION['user_id'];
        $children = $this->parent_model->getChildren($parent_id);

        $grade_model = $this->model('Grade');
        $grades_by_child = [];
        foreach ($children as $child) {
            $grades_by_child[$child['fullname']] = $grade_model->getGradesByStudent($child['id']);
        }

        $data = [
            'title' => "Children's Grades",
            'grades_by_child' => $grades_by_child
        ];
        view('parent/grades', $data);
    }

    /**
     * Display the attendance for all of the parent's children.
     */
    public function attendance() {
        $parent_id = $_SESSION['user_id'];
        $children = $this->parent_model->getChildren($parent_id);

        $attendance_model = $this->model('Attendance');
        $attendance_by_child = [];
        foreach ($children as $child) {
            $attendance_by_child[$child['fullname']] = $attendance_model->getAttendanceByStudentId($child['id']);
        }

        $data = [
            'title' => "Children's Attendance",
            'attendance_by_child' => $attendance_by_child
        ];
        view('parent/attendance', $data);
    }
}