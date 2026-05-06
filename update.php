<?php 
include("connection.php");
$con = connection();

if(isset($_POST['book_id']) && isset($_POST['satatus'])){
    $book_id = $_POST['book_id'];
    $status = $_POST ['status'];

    $query = "UPDATE books SET status='$status' WHERE id='$book_id'";
    mysqli_query($con,$query);

    header("Location: librarian.php#manage-books");
}
?>