<?php
include ("connection.php");
$con = connection();

if (isset($_POST['title'], $_POST['author'], $_POST['status'], $_POST['bookstat'])) {
    
    $title = $_POST['title'];
    $author = $_POST['author'];
    $status = $_POST['status'];
    $bookstat = $_POST['bookstat'];

    // image upload
    $image = $_FILES['image']['name'] ?? '';
    $tmp = $_FILES['image']['tmp_name'] ?? '';
    if (!empty($image)) {
        move_uploaded_file($tmp, "uploads/" . $image);
    }

    // softcopy upload
    $softcopy_file = $_FILES['softcopy_file']['name'] ?? '';
    $tmp_softcopy = $_FILES['softcopy_file']['tmp_name'] ?? '';
    if (!empty($softcopy_file)) {
        // siguraduhin may folder na uploads/softcopies
        if (!is_dir("uploads/softcopies")) {
            mkdir("uploads/softcopies", 0777, true);
        }
        move_uploaded_file($tmp_softcopy, "uploads/softcopies/" . $softcopy_file);
    }

    // sanitize
    $title = mysqli_real_escape_string($con, $title);
    $author = mysqli_real_escape_string($con, $author);
    $status = mysqli_real_escape_string($con, $status);
    $bookstat = mysqli_real_escape_string($con, $bookstat);
    $image = mysqli_real_escape_string($con, $image);
    $softcopy_file = mysqli_real_escape_string($con, $softcopy_file);

    // query with softcopy_file
    $query = "INSERT INTO books (title, author, image, status, bookstat, softcopy_file)
              VALUES ('$title','$author','$image','$status','$bookstat','$softcopy_file')";

    mysqli_query($con, $query);

    header("Location: librarian.php");
    exit();
}
?>
