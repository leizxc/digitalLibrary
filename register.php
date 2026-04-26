<?php
require_once 'connection.php';
session_start();

// Create connection
$con = connection();

// SIGN UP
if (isset($_POST['signUp'])) {
    $fname = $_POST['fname'] ?? '';
    $mname = $_POST['mname'] ?? '';
    $lname = $_POST['lname'] ?? '';
    $email = $_POST['email'] ?? '';
    $role = $_POST['role']?? '';
    $password = $_POST['password'] ?? '';

    // Hash password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $stmt = $con->prepare("SELECT id FROM account WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    //error box kung meron na email sa db
    if($stmt->num_rows>0){
    $_SESSION['signup_error'] = 'Email already exists';
    $_SESSION['form'] = 'signup'; 
    header("Location: DigitalLibrary.php");
    exit();
    }else {
        // Insert new account
        $stmt = $con->prepare("INSERT INTO account (fname, mname, lname, email, role,  password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $fname, $mname, $lname, $email, $role, $passwordHash);

        if ($stmt->execute()) {
            $_SESSION['signup_success'] = 'Registration successful!';
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

    $stmt = $con->prepare("SELECT id, email, password, role FROM account WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (password_verify($password, $row['password'])) {
        // ✅ set sessions
        $_SESSION['userid'] = $row['id'];   // ito ang gagamitin sa profile.php
        $_SESSION['email'] = $row['email'];
        $_SESSION['role'] = $row['role'];

        // redirect based on role
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
