<?php
include("connection.php");
$con = connection();

$q = $_GET['q'];

$sql = "SELECT * FROM books WHERE title LIKE '%$q%' OR author LIKE '%$q%' LIMIT 5 ";
$result = mysqli_query($con,$sql);

if(mysqli_num_rows($result) > 0){
    while ($row = mysqli_fetch_assoc($result)){
        echo "
        <div class='search-item'>
        <img src='uploads/".$row['image']." ' alt='Book Cover'>
        <div class='search-info'>
        <h4>".$row['title']."</h4>
        <p>".$row['author']." • ".$row['status']."</p>
        </div>
        </div>
        ";
    }
} else {
    echo "<div class='search-item no-result'>No results found </div>";
}
?>