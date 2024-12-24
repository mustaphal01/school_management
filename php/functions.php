<?php
require_once 'config.php';

// Student Functions
function addStudent($firstName, $lastName, $email, $grade) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO students (first_name, last_name, email, grade) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$firstName, $lastName, $email, $grade]);
    } catch(PDOException $e) {
        return false;
    }
}

function getStudents() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT * FROM students ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        return [];
    }
}

function deleteStudent($id) {
    global $pdo;
    try {
        // First delete from student_classes
        $stmt = $pdo->prepare("DELETE FROM student_classes WHERE student_id = ?");
        $stmt->execute([$id]);
        
        // Then delete the student
        $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
        return $stmt->execute([$id]);
    } catch(PDOException $e) {
        return false;
    }
}

// Teacher Functions
function addTeacher($firstName, $lastName, $email, $subject) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO teachers (first_name, last_name, email, subject) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$firstName, $lastName, $email, $subject]);
    } catch(PDOException $e) {
        return false;
    }
}

function getTeachers() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT * FROM teachers ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        return [];
    }
}

function deleteTeacher($id) {
    global $pdo;
    try {
        // First update classes to remove this teacher
        $stmt = $pdo->prepare("UPDATE classes SET teacher_id = NULL WHERE teacher_id = ?");
        $stmt->execute([$id]);
        
        // Then delete the teacher
        $stmt = $pdo->prepare("DELETE FROM teachers WHERE id = ?");
        return $stmt->execute([$id]);
    } catch(PDOException $e) {
        return false;
    }
}

// Class Functions
function addClass($className, $teacherId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO classes (class_name, teacher_id) VALUES (?, ?)");
        return $stmt->execute([$className, $teacherId]);
    } catch(PDOException $e) {
        return false;
    }
}

function getClasses() {
    global $pdo;
    try {
        $stmt = $pdo->query("
            SELECT c.*, CONCAT(t.first_name, ' ', t.last_name) as teacher_name 
            FROM classes c 
            LEFT JOIN teachers t ON c.teacher_id = t.id
            ORDER BY c.class_name
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        return [];
    }
}

function deleteClass($id) {
    global $pdo;
    try {
        // First delete from student_classes
        $stmt = $pdo->prepare("DELETE FROM student_classes WHERE class_id = ?");
        $stmt->execute([$id]);
        
        // Then delete the class
        $stmt = $pdo->prepare("DELETE FROM classes WHERE id = ?");
        return $stmt->execute([$id]);
    } catch(PDOException $e) {
        return false;
    }
}

// Student-Class Assignment Functions
function assignStudentToClass($studentId, $classId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO student_classes (student_id, class_id) VALUES (?, ?)");
        return $stmt->execute([$studentId, $classId]);
    } catch(PDOException $e) {
        return false;
    }
}

function removeStudentFromClass($studentId, $classId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("DELETE FROM student_classes WHERE student_id = ? AND class_id = ?");
        return $stmt->execute([$studentId, $classId]);
    } catch(PDOException $e) {
        return false;
    }
}

// Utility Functions
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function isEmailUnique($email, $table, $excludeId = null) {
    global $pdo;
    try {
        $sql = "SELECT COUNT(*) FROM $table WHERE email = ?";
        $params = [$email];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() === 0;
    } catch(PDOException $e) {
        return false;
    }
}