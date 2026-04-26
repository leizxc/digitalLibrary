<?php
include("connection.php");
$con = connection();

if(isset($_GET['id'])){
    $id = $_GET['id'];
    $query = "SELECT id, title, author, image, status, bookstat 
              FROM books 
              WHERE id='$id'";
    $result = mysqli_query($con,$query);

    if($result && mysqli_num_rows($result) > 0){
        $book = mysqli_fetch_assoc($result);
        echo json_encode($book);
    } else {
        echo json_encode(["error" => "Book not found"]);
    }
}
?>
