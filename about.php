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
                <li><a href="#about">About</a></li>
            </ul>
        </div>
       </nav>

       <div class="rules">
        <h1>RULES ON BORROWING BOOKS</h1>
        <hr>

        <section>
            <h2>1. Overdue, Replacement, and Other Charges</h2>
            <p>Books not returned on time are charged PhP2.00/day excluding Sundays, Mondays, and Holidays. Regardless of user status, unreturned overdue books as well as unresolved financial accounts in the library, including fines and lost books, may result in temporary loss of borrowing privileges until items are returned or replacement charges and/or fines are paid. In the case of students, unsettled library accounts at the end of the semester are sent to the Registrar’s office for diploma, transcript, grades, and registration holds.</p>
        </section>

        <section>
            <h2>2. Lost Books</h2>
            <p>Any person who loses or fails to return a book within seven (7) days after the due date or recall shall either replace it with the same title or a good photocopy, or pay its current replacement value, or replace it within thirty (30) days by another title to be selected by the librarian. In all cases, the person shall pay a fine equivalent to 50% of the cost of the book.</p>
        </section>

        <section>
            <h2>3. Mutilation</h2>
            <p>Users will be required to pay for the actual cost of repair and other processing charges, and will be required to render community service in the library for one week.Repeated violations will be reported to the Registrar Office for appropriate disciplinary action.</p>
        </section>

        <section>
            <h2>4. Patron Responsibility</h2>
            <p>All patrons are responsible for the materials borrowed from the Library until all these materials are returned. They are also responsible for any charges incurred for overdue, lost, or damaged library materials.</p>
        </section>

        <section>
            <h2>5. Renewals</h2>
            <p>All loans are renewable if no one else has requested the material. Students are limited to one renewal. No student may hold on to a book for more than four (4) consecutive weeks, whether the book is in demand or not. Renewal is done in person or through email on or before the due date at the Circulation Counter.</p>
        </section>

        <section>
            <h2>6. Requests (Recalls, Reservations, and Searches)</h2>
            <p>Requests for book recalls, reservations, and searches are done at the Circulation Counter. Requests, however, are limited to current faculty, staff members, and students.</p>
            <p><strong>Recalls:</strong> All loans are subject to recall. Any book borrowed after a week can be recalled if another borrower requests the material. Upon recall, the item should be returned promptly. Recalled books not returned within two (2) days following the receipt of the notice will be fined PhP10.00 per day thereafter until these are returned.</p>
            <p><strong>Reservations:</strong> Only books that are currently on loan are qualified for reservation. A request placed for an item that is already charged out will automatically curtail the privilege of the existing borrower for book renewal. The priority to take out the material is given to the requestor. Reserved materials will be held for at least one (1) week for pick-up, after which the book is shelved for general circulation.</p>
        </section>

        <section>
            <h2>7. Lost or Damaged Books</h2>
            <p>Users are responsible for all costs associated with lost or damaged materials including overdue fines, handling, and other processing charges. Damaged or lost books must be reported at once and must be replaced within two (2) weeks from the date it was reported at the Administration Office. Failure to replace or pay for the book within the allotted time means suspension of borrowing privileges. If the book is found and returned within one month from the date of payment, the price paid for it is refunded but the corresponding fines will be charged. Payment for lost/damaged books is done at the Administration office.</p>
        </section>
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
