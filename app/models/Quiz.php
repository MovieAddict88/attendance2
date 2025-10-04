<?php
// app/models/Quiz.php

class Quiz {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get all quizzes created by a specific teacher.
     *
     * @param int $teacher_id The ID of the teacher.
     * @return array A list of quizzes.
     */
    public function getAllByTeacher($teacher_id) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM quizzes
            WHERE teacher_id = :teacher_id
            ORDER BY created_at DESC
        ");
        $stmt->execute([':teacher_id' => $teacher_id]);
        return $stmt->fetchAll();
    }

    /**
     * Create a new quiz.
     *
     * @param array $data The data for the new quiz.
     * @return bool True on success, false on failure.
     */
    public function create($data) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO quizzes (teacher_id, title, description, grade_level, section, due_date)
                VALUES (:teacher_id, :title, :description, :grade_level, :section, :due_date)
            ");

            $stmt->execute([
                ':teacher_id' => $data['teacher_id'],
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':grade_level' => $data['grade_level'],
                ':section' => $data['section'],
                ':due_date' => $data['due_date'] ?: null
            ]);

            return true;
        } catch (PDOException $e) {
            error_log("Quiz creation failed: " . $e->getMessage());
            return false;
        }
    }
}