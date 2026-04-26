<?php
include ("connection.php");
$con = connection();

//check kung may data
if (isset($_POST['title'], $_POST['author'],$_POST['status'],$_POST['bookstat'])){
    
    $title = $_POST['title'];
    $author = $_POST['author'];
    $status = $_POST['status'];
    $bookstat = $_POST['bookstat'];

    //check img if img exists

    $image = $_FILES ['image']['name'] ?? '';
    $tmp = $_FILES ['image']['tmp_name'] ?? '';

    if(!empty($image)){
        move_uploaded_file($tmp,"uploads/" . $image);
    }

    //logic sql 

    $title = mysqli_real_escape_string($con,$title);
    $author = mysqli_real_escape_string($con,$author);
    $status = mysqli_real_escape_string($con,$status);
    $bookstat = mysqli_real_escape_string($con,$bookstat);
    $image = mysqli_real_escape_string($con,$image);

    $query = "INSERT INTO books (title, author, image, status, bookstat)
              VALUES ('$title','$author','$image','$status','$bookstat')";

              mysqli_query($con,$query);

              header("Location: homepage.php");
              exit();
}