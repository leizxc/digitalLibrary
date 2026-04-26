<?php

use LDAP\Result;

session_start();
include("connection.php");

$con = connection();

if(isset($_SESSION['userid'])){
    $user_id = $_SESSION['userid'];
    $queryUser = "SELECT profile_img, fname FROM profile WHERE account_id='$user_id'";
    $resultUser = mysqli_query($con,$queryUser);
    $userData = mysqli_fetch_assoc($resultUser);
}
?>

<!DOCTYPE html>
<html lang="en">
    
<head>
    <meta charset="UTF-8"> <!-- FIXED -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <link rel="stylesheet" href="assets/style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
                <li><a href="#" class="active">Home</a></li>
                <li><a href="reservation.php">Reservation</a></li>
                <li><a href="#">BorrowedBooksform</a></li>
                <li><a href="profile.php">Profile</a></li>
            </ul>
            <div class="profile-dropdown">
                <button class="profile-btn">
                    <img src="uploads/<?php echo !empty($userData['profile_img']) ? $userData['profile_img'] : 'default-img.png' ?>" alt="Profile" class="profile-img">
                    <span class="profile-name"><?php echo isset($userData['fname']) ? $userData['fname'] : 'Guest' ; ?></span>
                </button>
                <div class="dropdown-menu">
                <a href="profile.php"><i class="fa fa-user"></i> Profile <span class="arrow">&rsaquo;</span></a>
                <a href="#"><i class="fa fa-cog"></i> Settings & Privacy <span class="arrow">&rsaquo;</span></a>
                <a href="#"><i class="fa fa-question-circle"></i> Help & Support <span class="arrow">&rsaquo;</span></a>
                <div class="divider"></div>
                <a href="homepage.php"><i class="fa fa-sign-out"></i> Logout <span class="arrow">&rsaquo;</span></a>
            </div>
            </div>
        </div>
       </nav>

       <!-- HERO SECTION -->
<section style="text-align:center; padding:60px;">
    <h1>Explore Books Anytime, Anywhere</h1>
    <p>Search, reserve, and access library resources online.</p>
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

        <div class="book-card" data-id="<?php echo $row['id']; ?>">
            <img src="uploads/<?php echo $row['image']; ?>" alt="Book Image">
            <h2><?php echo $row ['id']; ?></h2>
            <h3><?php echo $row ['title']; ?></h3>
            <p>Author: <?php echo $row['author'];?></p>
            <p>Status: <?php echo $row['status'];?></p>
            <p>Book status: <?php echo $row['bookstat'];?></p>

           <?php 
           // kung may hardcopy lalabas yung reserve nbutton
           if(stripos($row['bookstat'],'hardcopy') !== false){
            echo '<a href="reservation.php?book_id='.$row['id'].'"><button>Reserve</button></a>';
           }

           //kung may softcopy start readbutton
           if(stripos($row['bookstat'], 'softcopy')!== false){
            echo'<a href="read.php?id='.$row['id'].'"><button>Read</button></a>';
           }
           ?>
        </div>
   <?php } ?>
</section>
<div id="bookModal" class="modal">
    <div class="modal-content1">
        <span id="closeModal" style="cursor: pointer; float:right">&times;</span>
        <img id="modalImage" src="" alt="book Image" style="width: 120px; height:160px; object-fit:cover; border-radius:8px; ">
        <h2 id="modalTitle"></h2>
        <p id="modalbooksid"></p>
        <p id="modalAuthor"></p>
        <p id="modalStatus"></p>
        <p id="modalBookstat"></p>
        <p id="modalActions"></p>
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

    //book modal
    const bookcards = document.querySelectorAll('.book-card');
    const modal = document.getElementById('bookModal');
    const closeModal = document.getElementById('closeModal');
    const modalImage = document.getElementById('modalImage');
    const modalTitle = document.getElementById('modalTitle');
    const modalAuthor = document.getElementById('modalAuthor');
    const modalStatus = document.getElementById('modalStatus');
    const modalBookstat = document.getElementById('modalBookstat');
    const modalActions = document.getElementById('modalActions');
    const modalBooksId = document.getElementById('modalbooksid');

    bookcards.forEach(card => {
        card.addEventListener('click', (e) => {
            // kung button ang na click, huwag bubuksan ang modal
            if(e.target.tagName.toLowerCase() === 'button'){
                return;
            }
            const bookId = card.getAttribute('data-id');
           fetch(`getBookDetails.php?id=${bookId}`)
           .then(res => res.json())
           .then(book => {
            modalImage.src = `uploads/${book.image}`;
            modalTitle.textContent = book.title;
            modalAuthor.textContent = `Author: ${book.author}`;
            modalStatus.textContent = `Status: ${book.status}`;
            modalBookstat.textContent = `Book Status: ${book.bookstat}`;
            modalBooksId.textContent = `Book Id: ${book.id}`;
    // clear old buttons
    modalActions.innerHTML = "";

    // kung may hardcopy → Reserve button
    if(book.bookstat.toLowerCase().includes("hardcopy")){
        const reserveBtn = document.createElement('button');
        reserveBtn.textContent = "Reserve";
        reserveBtn.className = "login-btn";
        modalActions.appendChild(reserveBtn);
    }

    // kung may softcopy → Start Read button
    if(book.bookstat.toLowerCase().includes("softcopy")){
        const readBtn = document.createElement('button');
        readBtn.textContent = "Start Read";
        readBtn.className = "login-btn";
        modalActions.appendChild(readBtn);
    }

            modal.style.display = "flex";
            });
        });
    });
    closeModal.addEventListener('click',() => modal.style.display = "none");
    window.addEventListener('click', e => {if (e.target === modal) modal.style.display = "none";})
    </script>
</html>
