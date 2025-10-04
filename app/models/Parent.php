<?php
// app/models/Parent.php

class ParentModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get all children associated with a specific parent.
     *
     * @param int $parent_id The user ID of the parent.
     * @return array A list of their children's details.
     */
    public function getChildren($parent_id) {
        $stmt = $this->pdo->prepare("
            SELECT u.id, u.fullname, s.grade_level, s.section
            FROM students s
            JOIN users u ON s.id = u.id
            WHERE s.parent_id = :parent_id
            ORDER BY u.fullname ASC
        ");
        $stmt->execute([':parent_id' => $parent_id]);
        return $stmt->fetchAll();
    }
}