<?php
// app/models/Student.php

class Student {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get all students with their user and parent details.
     */
    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT
                s.id,
                u.fullname,
                u.email,
                s.student_code,
                s.grade_level,
                s.section,
                p.fullname as parent_name,
                t.fullname as teacher_name
            FROM students s
            JOIN users u ON s.id = u.id
            LEFT JOIN users p ON s.parent_id = p.id
            LEFT JOIN users t ON s.homeroom_teacher_id = t.id
            WHERE u.role = 'student'
            ORDER BY u.fullname ASC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Create a new student.
     */
    public function create($data) {
        try {
            $this->pdo->beginTransaction();

            // 1. Create the user record for the student
            $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
            $user_stmt = $this->pdo->prepare(
                "INSERT INTO users (username, password, role, email, fullname)
                 VALUES (:username, :password, 'student', :email, :fullname)"
            );
            $user_stmt->execute([
                ':username' => $data['username'],
                ':password' => $hashed_password,
                ':email' => $data['email'],
                ':fullname' => $data['fullname']
            ]);
            $student_user_id = $this->pdo->lastInsertId();

            // 2. Create the student record
            $student_stmt = $this->pdo->prepare(
                "INSERT INTO students (id, student_code, grade_level, section, homeroom_teacher_id, birthdate, parent_id)
                 VALUES (:id, :student_code, :grade_level, :section, :homeroom_teacher_id, :birthdate, :parent_id)"
            );
            $student_stmt->execute([
                ':id' => $student_user_id,
                ':student_code' => $data['student_code'],
                ':grade_level' => $data['grade_level'],
                ':section' => $data['section'],
                ':homeroom_teacher_id' => $data['homeroom_teacher_id'],
                ':birthdate' => $data['birthdate'],
                ':parent_id' => $data['parent_id']
            ]);

            $this->pdo->commit();
            return true;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Student creation failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all users with a specific role.
     */
    public function getUsersByRole($role) {
        $stmt = $this->pdo->prepare("SELECT id, fullname FROM users WHERE role = :role ORDER BY fullname ASC");
        $stmt->execute([':role' => $role]);
        return $stmt->fetchAll();
    }

    /**
     * Find a student by their ID.
     */
    public function findById($id) {
        $stmt = $this->pdo->prepare("
            SELECT u.*, s.*
            FROM users u
            JOIN students s ON u.id = s.id
            WHERE u.id = :id AND u.role = 'student'
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Update a student's details.
     */
    public function update($id, $data) {
        try {
            $this->pdo->beginTransaction();

            // 1. Update users table
            $user_sql = "UPDATE users SET fullname = :fullname, email = :email, username = :username";
            $user_params = [
                ':id' => $id,
                ':fullname' => $data['fullname'],
                ':email' => $data['email'],
                ':username' => $data['username']
            ];
            if (!empty($data['password'])) {
                $user_sql .= ", password = :password";
                $user_params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            $user_sql .= " WHERE id = :id";
            $user_stmt = $this->pdo->prepare($user_sql);
            $user_stmt->execute($user_params);

            // 2. Update students table
            $student_stmt = $this->pdo->prepare(
                "UPDATE students SET student_code = :student_code, grade_level = :grade_level, section = :section,
                 homeroom_teacher_id = :homeroom_teacher_id, birthdate = :birthdate, parent_id = :parent_id
                 WHERE id = :id"
            );
            $student_stmt->execute([
                ':id' => $id,
                ':student_code' => $data['student_code'],
                ':grade_level' => $data['grade_level'],
                ':section' => $data['section'],
                ':homeroom_teacher_id' => $data['homeroom_teacher_id'],
                ':birthdate' => $data['birthdate'],
                ':parent_id' => $data['parent_id']
            ]);

            $this->pdo->commit();
            return true;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Student update failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a student.
     */
    public function delete($id) {
        try {
            // ON DELETE CASCADE will handle related tables
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id AND role = 'student'");
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Student deletion failed: " . $e->getMessage());
            return false;
        }
    }
}