<?php
// app/models/Grade.php

class Grade {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get all grades for a specific student.
     *
     * @param int $student_id The ID of the student.
     * @return array A list of grades with associated item details.
     */
    public function getGradesByStudent($student_id) {
        $stmt = $this->pdo->prepare("
            SELECT
                g.grade,
                g.quarter,
                g.created_at,
                COALESCE(q.title, a.title) as item_title,
                CASE
                    WHEN g.quiz_id IS NOT NULL THEN 'Quiz'
                    WHEN g.assignment_id IS NOT NULL THEN 'Assignment'
                    ELSE 'Other'
                END as item_type
            FROM grades g
            LEFT JOIN quizzes q ON g.quiz_id = q.id
            LEFT JOIN assignments a ON g.assignment_id = a.id
            WHERE g.student_id = :student_id
            ORDER BY g.created_at DESC
        ");
        $stmt->execute([':student_id' => $student_id]);
        return $stmt->fetchAll();
    }
}