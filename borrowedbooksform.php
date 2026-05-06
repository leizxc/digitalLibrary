<?php

use LDAP\Result;

session_start();
include("connection.php");
require_once('phpqrcode/qrlib.php');

$con = connection();

if(isset($_SESSION['userid'])){
    $user_id = $_SESSION['userid'];
    $queryUser = "SELECT profile_img, fname, lname FROM profile WHERE account_id='$user_id'";
    $resultUser = mysqli_query($con,$queryUser);
    $userData = mysqli_fetch_assoc($resultUser);
}

//book form php logic

if(isset($_POST['borrow'])){
    $reservation_id = $_POST['reservation_id'];
    $account_id = $_SESSION['userid'];

}
?>

<!DOCTYPE html>
<html>
    <head>
            <script>
  document.addEventListener("DOMContentLoaded", function() {
    var meta = document.createElement('meta');
    meta.name = "viewport";
    if (/Mobi|Android|iPhone|iPad/i.test(navigator.userAgent)) {
      // mobile or tablet
      meta.content = "width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no";
    } else {
      // desktop
      meta.content = "width=device-width, initial-scale=1.0";
    }
    document.getElementsByTagName('head')[0].appendChild(meta);
  });
</script>
       <link rel="stylesheet" href="./assets/style1.css">
    </head>
    <body>
        <nav class="navbar">
        <div class="navbar-container">
            <a href="homeprofandstud.php" class="navbar-logo">Library</a>
            <div class="search-boxx">
                <input type="text" name="search" id="srch" placeholder="Search a Book">
                <button type="submit"><i class="fa fa-search"></i></button>
                <div id="search-results"></div>
            </div>
            <button class="navbar-toggle">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
            <ul class="navbar-menu">
                <li><a href="homeprofandstud.php" class="active">Home</a></li>
                <li><a href="reservation.php">Reservation</a></li>
                <li><a href="borrowedbooksform.php">BorrowedBooksform</a></li>
                <li><a href="profile.php">Profile</a></li>
            </ul>
            <div class="profile-dropdown">
                <button class="profile-btn">
                    <img src="uploads/<?php echo !empty($userData['profile_img']) ? $userData['profile_img'] : 'default-img.png' ?>" alt="Profile" class="profile-img">
                    <span class="profile-name"><?php echo isset($userData['fname']) ? $userData['fname'] : 'Guest' ; ?></span>
                </button>
                <div class="dropdown-menu">
                    <a href="profile.php">Profile</a>
                    <a href="#">Settings</a>
                    <a href="index.php">Logout</a>
                    </div>
                </div>
            </div>
        </nav>

        <div class="maint-content">
           <header><h1>Your Borrowed Books</h1></header>

           <table>
            <tr>
                <th>Reservation</th>
                <th>Book Title</th>
                <th>Date Borrowed</th>
                <th>Due Date</th>
                <th>status</th>
                <th>Penalty</th>
            </tr>
            <?php 
            $account_id = $_SESSION ['userid'];
            $query = "SELECT r.*, b.title, p.role
                      FROM reservations r
                      JOIN books b ON r.book_id = b.id
                      JOIN profile p ON r.account_id=p.account_id
                      WHERE r.account_id = '$account_id'
                      AND r.borrow_status IN ('Borrowed', 'Overdue', 'Returned');";
            $result = mysqli_query($con,$query);

            while($row = mysqli_fetch_assoc($result)){
                $status = $row['borrow_status'];
                $penalty = '';

                if($row['role'] === 'student' && $row['borrow_status'] === 'Borrowed' && time() >= strtotime($row['due_date'])){
                    $daysoverdue = floor((time() - strtotime($row['due_date'])) / (60*60*24));
                    $penalty = 'P' . ($daysoverdue * 2); // P2 a day
                    $status = 'Overdue';

                //update db
                mysqli_query($con, "UPDATE reservations SET borrow_status='Overdue',
                                    penalty = '$penalty' WHERE reservation_id = '{$row['reservation_id']}'");
                }
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['reservation_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                echo "<td>" . htmlspecialchars($row['date_borrowed']) . "</td>";
                echo "<td>" .date('Y-m-d H:A', strtotime($row['due_date'])) . "</td>";
                echo "<td>" . htmlspecialchars($status) . "</td>";
                echo "<td>" . ($row['role']=='teacher' ? 'No Penalty' : '₱' . $penalty) . "</td>";
                echo "</tr>";
            }
            ?>
           </table>
        </div>

    </body>
    <script>
                        //search bar logic
        const searchInput = document.getElementById('srch');
        const resultsBox = document.getElementById('search-results');

         searchInput.addEventListener('keyup', () => {
        let query = searchInput.value.trim();
        if(query.length > 0){
        fetch('searchbarlogic.php?q=' + query)
        .then(res => res.text())
        .then(data => {
            resultsBox.style.display = 'block';
            resultsBox.innerHTML = data;
        });
    } else {
        resultsBox.style.display = 'none';
    }
});

  //close search results kapag nag click sa iba 
    document.addEventListener('click', function(e){
        const searchBox = document.querySelector('.search-boxx');
        const resultBox = document.getElementById('search-results');

        if(!searchBox.contains(e.target)){
            resultBox.style.display = 'none';
        }
    });

    const profileBtn = document.querySelector('.profile-btn');
    const dropdownMenu = document.querySelector('.dropdown-menu');

    profileBtn.addEventListener('click', (e)=> {
        e.stopPropagation();//para hindi masara agad
        dropdownMenu.classList.toggle('active');
        if(dropdownMenu.classList.contains('active')){
            dropdownMenu.style.display = 'block';
        }else{
            dropdownMenu.style.display = 'none';
        }
    });

     const navbarToggle = document.querySelector('.navbar-toggle');
        const navbarMenu = document.querySelector('.navbar-menu');

        navbarToggle.addEventListener('click', () => {
            navbarToggle.classList.toggle('active');
            navbarMenu.classList.toggle('active');
        });

    </script>