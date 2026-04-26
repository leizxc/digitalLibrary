<?php

use LDAP\Result;

session_start();
include("connection.php");

$con = connection();
?>

<!DOCTYPE html>
<html lang="en">
    
<head>
    <meta charset="UTF-8"> <!-- FIXED -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <link rel="stylesheet" href="./assets/style1.css">
</head>
    <body>
       <nav class="navbar">
        <div class="navbar-container">
            <a href="homepage.php" class="navbar-logo">Library</a>
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
                <li><a href="index.php" class="active">Home</a></li>
                <li><a href="#reservation">Reservation</a></li>
                <li><a href="#borrow">BorrowedBooksform</a></li>
                <li><a href="DigitalLibrary.php">LogIn</a></li>
                <li><a href="about.php">About</a></li>
            </ul>
        </div>
       </nav>

       <!-- HERO SECTION -->
<section style="text-align:center; padding:60px;">
    <h1>Explore Books Anytime, Anywhere</h1>
    <p>Search, reserve, and access library resources online.</p>
    
    <a href="DigitalLibrary.php">
        <button name="login" class="login">
            Get Started
        </button>
    </a>
</section>

<!-- BOOK PREVIEW -->
<section class="library">
    <h2>Available Books</h2>

    <div class="books">
    <?php
    $query = "SELECT * FROM books ";
    $result = mysqli_query($con, $query);

    while($row = mysqli_fetch_assoc($result)){
        ?>

        <div class="book-card">
            <img src="uploads/<?php echo $row['image']; ?>" alt="Book Image">
            <h3><?php echo $row ['title']; ?></h3>
            <p>Author: <?php echo $row['author'];?></p>
            <p>Status: <?php echo $row['status'];?></p>
            <p>Book status: <?php echo $row['bookstat'];?></p>

            <a href="DigitalLibrary.php">
                <button>Login to Reserve</button>
            </a>
        </div>
   <?php } ?>
</section>

<div id="loginModal" class="modal">
    <div class="modal-content">
        <h3>Please Login First</h3>
        <p>You need to Login to Access this feature</p>
        <a href="DigitalLibrary.php"><button class="login-btn">Go to Login </button></a>
    </div>
</div>
</body>
<script>
        const navbarToggle = document.querySelector('.navbar-toggle');
        const navbarMenu = document.querySelector('.navbar-menu');

        navbarToggle.addEventListener('click', () => {
            navbarToggle.classList.toggle('active');
            navbarMenu.classList.toggle('active');
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

    //close search results kapag nag click sa iba 
    document.addEventListener('click', function(e){
        const searchBox = document.querySelector('.search-boxx');
        const resultBox = document.getElementById('search-results');

        if(!searchBox.contains(e.target)){
            resultBox.style.display = 'none';
        }
    });
    //loginpopup

    function showloginpopup(e){
        e.preventDefault();
        document.getElementById('loginModal').style.display = 'flex';
    }

    //attach sa mga menu links
    document.querySelector('a[href="#reservation"]').addEventListener('click',showloginpopup);
    document.querySelector('a[href="#borrow"]').addEventListener('click',showloginpopup);

    //close modal
    window.addEventListener('click', function(e){
        const modal = document.getElementById('loginModal');
        if(e.target===modal){
            modal.style.display = 'none'
        }
    });
    </script>
</html>
