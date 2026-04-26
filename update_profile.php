<?php 
session_start();
include 'connection.php';
$con = connection();

if(!isset($_SESSION['userid'])){
    header("Location: DigitalLibrary.php");
    exit();
}

$user_id = $_SESSION['userid'];

if(isset($_POST['update'])){
    // sanitize inputs
    $bio = mysqli_real_escape_string($con, $_POST['bio']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $address = mysqli_real_escape_string($con, $_POST['address']);

    $profile_img = null;
    if(isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] == 0){
        $target_dir = 'uploads/';
        $profile_img = basename($_FILES["profile_img"]["name"]);
        $target_file = $target_dir . $profile_img;
        move_uploaded_file($_FILES["profile_img"]["tmp_name"], $target_file);
    }

    // build query
    $sql = "UPDATE profile SET bio='$bio', phone='$phone', address='$address'";
    if($profile_img){
        $sql .= ", profile_img='$profile_img'";
    }
    $sql .= " WHERE account_id='$user_id'";

    if(mysqli_query($con, $sql)){
        header("Location: profile.php");
        exit();
    } else {
        echo "Error updating profile: " . mysqli_error($con);
    }
}
?>
