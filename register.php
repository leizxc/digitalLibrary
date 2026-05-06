<?php
require_once 'connection.php';
session_start();

$con = connection();

// SIGN UP
if (isset($_POST['signUp'])) {
    $fname = $_POST['fname'] ?? '';
    $mname = $_POST['mname'] ?? '';
    $lname = $_POST['lname'] ?? '';
    $email = $_POST['email'] ?? '';
    $role = $_POST['role'] ?? '';
    $password = $_POST['password'] ?? '';

    // Hash password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $stmt = $con->prepare("SELECT id FROM account WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['signup_error'] = 'Email already exists';
        $_SESSION['form'] = 'signup'; 
        header("Location: DigitalLibrary.php");
        exit();
    } else {
        // status logic: Student = Approved, Teacher = Pending
        $status = ($role === 'teacher') ? 'Pending' : 'Approved';

        // Insert new account
        $stmt = $con->prepare("INSERT INTO account (fname, mname, lname, email, role, password, status) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $fname, $mname, $lname, $email, $role, $passwordHash, $status);

        if ($stmt->execute()) {
            $account_id = $stmt->insert_id;

            // kung teacher, handle file upload
            if ($role === "teacher" && isset($_FILES['teacher_id']) && $_FILES['teacher_id']['error'] === UPLOAD_ERR_OK) {
                $targetDir = "uploads/teacher_ids/";
                if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

                $fileName = time() . "_" . basename($_FILES["teacher_id"]["name"]);
                $teacher_id_path = $targetDir . $fileName;

                if (move_uploaded_file($_FILES["teacher_id"]["tmp_name"], $teacher_id_path)) {
                    $stmt2 = $con->prepare("INSERT INTO teacher_verification (account_id, file_path) VALUES (?, ?)");
                    $stmt2->bind_param("is", $account_id, $teacher_id_path);
                    $stmt2->execute();
                    $stmt2->close();
                } else {
                    error_log("Upload failed for teacher ID");
                }
            }

            $_SESSION['signup_success'] = ($role === 'teacher') 
                ? 'Registration successful! Waiting for admin approval.' 
                : 'Registration successful!';
            $_SESSION['form'] = 'signup';
            header("Location: DigitalLibrary.php");
            exit();
        } else {
            $_SESSION['signup_error'] = "Something went Wrong!";
            header("Location: DigitalLibrary.php");
            exit();
        }
    }
    $stmt->close();
}

// SIGN IN
if (isset($_POST['signIn'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $con->prepare("SELECT id, email, password, role, status FROM account WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            if ($row['status'] !== 'Approved') {
                $_SESSION['error'] = 'Your account is still pending approval.';
                header("Location: DigitalLibrary.php");
                exit();
            }

            $_SESSION['userid'] = $row['id'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = $row['role'];

            if ($row['role'] === 'student' || $row['role'] === 'teacher') {
                header("Location: homeprofandstud.php");
                exit();
            } elseif ($row['role'] === 'admin') {
                header("Location: admin.php");
                exit();
            } elseif ($row['role'] === 'librarian') {
                header("Location: librarian.php");
                exit();
            }
        } else {
            $_SESSION['error'] = 'Incorrect password!';
            header("Location: DigitalLibrary.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Email not found!";
        header("Location: DigitalLibrary.php");
        exit();
    }

    $stmt->close();
}

$con->close();
?>
