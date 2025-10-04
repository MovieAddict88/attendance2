<?php
// app/models/Assignment.php

class Assignment {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get all assignments created by a specific teacher.
     *
     * @param int $teacher_id The ID of the teacher.
     * @return array A list of assignments.
     */
    public function getAllByTeacher($teacher_id) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM assignments
            WHERE teacher_id = :teacher_id
            ORDER BY created_at DESC
        ");
        $stmt->execute([':teacher_id' => $teacher_id]);
        return $stmt->fetchAll();
    }

    /**
     * Create a new assignment.
     *
     * @param array $data The data for the new assignment.
     * @return bool True on success, false on failure.
     */
    public function create($data) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO assignments (teacher_id, title, description, file_path, grade_level, section, due_date)
                VALUES (:teacher_id, :title, :description, :file_path, :grade_level, :section, :due_date)
            ");

            $stmt->execute([
                ':teacher_id' => $data['teacher_id'],
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':file_path' => $data['file_path'],
                ':grade_level' => $data['grade_level'],
                ':section' => $data['section'],
                ':due_date' => $data['due_date'] ?: null
            ]);

            return true;
        } catch (PDOException $e) {
            error_log("Assignment creation failed: " . $e->getMessage());
            return false;
        }
    }
}