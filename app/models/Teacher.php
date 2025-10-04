<?php
// app/models/Teacher.php

class Teacher {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get all teachers with their user details.
     *
     * @return array An array of teacher records.
     */
    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT u.id, u.fullname, u.email, u.username, t.teacher_code, t.phone
            FROM users u
            JOIN teachers t ON u.id = t.id
            WHERE u.role = 'teacher'
            ORDER BY u.fullname ASC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Create a new teacher.
     * This involves creating a user record and a teacher record in a transaction.
     *
     * @param array $data The data for the new teacher.
     * @return bool True on success, false on failure.
     */
    public function create($data) {
        try {
            $this->pdo->beginTransaction();

            // 1. Create the user record
            $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);

            $user_stmt = $this->pdo->prepare("
                INSERT INTO users (username, password, role, email, fullname)
                VALUES (:username, :password, 'teacher', :email, :fullname)
            ");

            $user_stmt->execute([
                ':username' => $data['username'],
                ':password' => $hashed_password,
                ':email' => $data['email'],
                ':fullname' => $data['fullname']
            ]);

            $user_id = $this->pdo->lastInsertId();

            // 2. Create the teacher record
            $teacher_stmt = $this->pdo->prepare("
                INSERT INTO teachers (id, teacher_code, phone, bio)
                VALUES (:id, :teacher_code, :phone, :bio)
            ");

            $teacher_stmt->execute([
                ':id' => $user_id,
                ':teacher_code' => $data['teacher_code'],
                ':phone' => $data['phone'],
                ':bio' => $data['bio']
            ]);

            $this->pdo->commit();
            return true;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            // Check for duplicate entry error
            if ($e->errorInfo[1] == 1062) {
                // Duplicate username or email
                return false;
            }
            // For other errors, you might want to log them
            error_log("Teacher creation failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Find a teacher by their ID.
     *
     * @param int $id The user ID of the teacher.
     * @return array|false The teacher data, or false if not found.
     */
    public function findById($id) {
        $stmt = $this->pdo->prepare("
            SELECT u.id, u.fullname, u.email, u.username, t.teacher_code, t.phone, t.bio
            FROM users u
            JOIN teachers t ON u.id = t.id
            WHERE u.id = :id AND u.role = 'teacher'
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Update a teacher's details.
     *
     * @param int $id The user ID of the teacher.
     * @param array $data The data to update.
     * @return bool True on success, false on failure.
     */
    public function update($id, $data) {
        try {
            $this->pdo->beginTransaction();

            // 1. Update the users table
            $user_sql = "UPDATE users SET fullname = :fullname, email = :email, username = :username";
            $user_params = [
                ':id' => $id,
                ':fullname' => $data['fullname'],
                ':email' => $data['email'],
                ':username' => $data['username']
            ];

            // Only update password if a new one is provided
            if (!empty($data['password'])) {
                $user_sql .= ", password = :password";
                $user_params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            $user_sql .= " WHERE id = :id";
            $user_stmt = $this->pdo->prepare($user_sql);
            $user_stmt->execute($user_params);

            // 2. Update the teachers table
            $teacher_stmt = $this->pdo->prepare("
                UPDATE teachers SET teacher_code = :teacher_code, phone = :phone, bio = :bio
                WHERE id = :id
            ");
            $teacher_stmt->execute([
                ':id' => $id,
                ':teacher_code' => $data['teacher_code'],
                ':phone' => $data['phone'],
                ':bio' => $data['bio']
            ]);

            $this->pdo->commit();
            return true;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Teacher update failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a teacher.
     *
     * @param int $id The user ID of the teacher to delete.
     * @return bool True on success, false on failure.
     */
    public function delete($id) {
        try {
            // The ON DELETE CASCADE constraint on the teachers table will handle deleting the teacher-specific record.
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id AND role = 'teacher'");
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Teacher deletion failed: " . $e->getMessage());
            return false;
        }
    }
}