<?php
// app/models/Attendance.php

class Attendance {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get all students assigned to a specific homeroom teacher.
     *
     * @param int $teacher_id The ID of the homeroom teacher.
     * @return array A list of students.
     */
    public function getStudentsByTeacher($teacher_id) {
        $stmt = $this->pdo->prepare("
            SELECT u.id, u.fullname, s.student_code
            FROM students s
            JOIN users u ON s.id = u.id
            WHERE s.homeroom_teacher_id = :teacher_id
            ORDER BY u.fullname ASC
        ");
        $stmt->execute([':teacher_id' => $teacher_id]);
        return $stmt->fetchAll();
    }

    /**
     * Save attendance records for multiple students for a specific date.
     * It will first delete any existing records for that day to prevent duplicates.
     *
     * @param int $teacher_id The ID of the teacher submitting the attendance.
     * @param string $date The date of the attendance.
     * @param array $statuses An associative array of [student_id => status].
     * @return bool True on success, false on failure.
     */
    public function saveAttendance($teacher_id, $date, $statuses) {
        try {
            $this->pdo->beginTransaction();

            // First, delete any existing records for this teacher and date to avoid duplicates
            $delete_stmt = $this->pdo->prepare("DELETE FROM attendance WHERE teacher_id = :teacher_id AND date = :date");
            $delete_stmt->execute([':teacher_id' => $teacher_id, ':date' => $date]);

            // Now, insert the new records
            $insert_stmt = $this->pdo->prepare(
                "INSERT INTO attendance (student_id, teacher_id, date, status, note)
                 VALUES (:student_id, :teacher_id, :date, :status, :note)"
            );

            foreach ($statuses as $student_id => $details) {
                $insert_stmt->execute([
                    ':student_id' => $student_id,
                    ':teacher_id' => $teacher_id,
                    ':date' => $date,
                    ':status' => $details['status'],
                    ':note' => $details['note'] ?? ''
                ]);
            }

            $this->pdo->commit();
            return true;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Attendance saving failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all attendance records for a specific student.
     *
     * @param int $student_id The ID of the student.
     * @return array A list of attendance records.
     */
    public function getAttendanceByStudentId($student_id) {
        $stmt = $this->pdo->prepare("
            SELECT date, status, note
            FROM attendance
            WHERE student_id = :student_id
            ORDER BY date DESC
        ");
        $stmt->execute([':student_id' => $student_id]);
        return $stmt->fetchAll();
    }
}