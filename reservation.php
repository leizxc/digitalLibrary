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

if(isset($_POST['reserve'])){
    $book_id = $_POST['book_id'];
    $account_id = $_SESSION['userid'];
    //generate unique reservation id
    $reservation_id = 'RSV-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
    $query = "INSERT INTO reservations (reservation_id, book_id, account_id, date_reserved, status)
              VALUES ('$reservation_id','$book_id', '$account_id', NOW(), 'Pending')";
    $result = mysqli_query($con,$query);

    if($result){

      //kunin book title 
      $bookquery = mysqli_query($con,"SELECT title FROM books WHERE id='$book_id'");
      $bookrow = mysqli_fetch_assoc($bookquery);
      $book_title = $bookrow['title'];

      $user_name = isset($userData['fname']) ? $userData['fname'] . ' ' . $userData['lname'] : '';
      $qrcontent = "Reservation ID: $reservation_id\nBook: $book_title\nName: $user_name";

      if(!file_exists('qrcodes')){
        mkdir('qrcodes');
      }

      $qrfile = "qrcodes/$reservation_id.png";
      QRcode::png($qrcontent, $qrfile, QR_ECLEVEL_L, 4);

      $_SESSION['success'] = "Reservation submitted successfully!";

    }else{              
        $_SESSION ['error'] = "Failed to reserve Book";
    }
    header("Location: reservation.php");
    exit();
}
//kung may book_id sa url, iauto-select
$selectedBook = isset($_GET['book_id'])? $_GET['book_id'] : '' ;
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

        <div class="main-content">
            <header><h1>Reserve A book</h1></header>

            <!-- feedback messages -->
             <?php if (isset($_SESSION['success'])): ?>
             <div class="success-box"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
             <?php endif; ?>

             <?php if (isset($_SESSION['error'])): ?>
             <div class="error-box"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
             <?php endif; ?>
        </div>

        <!--- reservation form -->
        <form action="reservation.php" method="POST" class="form-box">
    <div class="input-group">
        <label for="book_id">Select Book</label>
        <select name="book_id" id="book_id" required>
            <?php 
            $books = mysqli_query($con, "SELECT id, title FROM books WHERE status='available' AND bookstat = 'hardcopy'OR bookstat = 'softcopy/hardcopy'");
            while($row = mysqli_fetch_assoc($books)){
                $selected = ($row['id'] == $selectedBook) ? 'selected' : '';
                echo "<option value='{$row['id']}' $selected>{$row['title']}</option>";
            }
            ?>
        </select>
    </div>
    <button type="submit" name="reserve">Reserve</button>
    </form>

        <!-- show user's reservations -->
         <h2>Your Reservation</h2>
         <table >
            <tr>
                <th>Book Title</th>
                <th>Date Reserved</th>
                <th>Status</th>
                <th>Qr Code</th>
            </tr>
            <?php 
            $account_id = $_SESSION['userid'];
            $query = "SELECT r.*, b.title
                      FROM reservations r
                      JOIN books b ON r.book_id = b.id
                      WHERE r.account_id = '$account_id'";
            $result = mysqli_query($con,$query);

            while ($row = mysqli_fetch_assoc($result)){
                echo " <tr>
                            <td>{$row['title']}</td>
                            <td>{$row['date_reserved']}</td>
                            <td>{$row['status']}</td>
                            <td><a href= 'qrcodes/{$row['reservation_id']}.png' download>
                                <img src = 'qrcodes/{$row['reservation_id']}.png' width = '80' alt = 'QR code'>
                            </tr>";
            }
            ?>
         </table>
    </body>
    <script>

            const navbarToggle = document.querySelector('.navbar-toggle');
        const navbarMenu = document.querySelector('.navbar-menu');

        navbarToggle.addEventListener('click', () => {
            navbarToggle.classList.toggle('active');
            navbarMenu.classList.toggle('active');
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
    </script>
</html>