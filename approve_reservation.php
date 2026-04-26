<?php
session_start();
include("connection.php");
$con = connection();

if(isset($_POST['action'])){
    $reservation_id = $_POST['reservation_id'];

    if($_POST['action'] == 'approve'){
        // APPROVE: status = Approved, borrow_status = Borrowed
        $query = "UPDATE reservations 
                  SET status='Approved',
                      date_borrowed=NOW(),
                      due_date=DATE_ADD(NOW(), INTERVAL 7 DAY),
                      borrow_status='Borrowed'
                  WHERE reservation_id='$reservation_id'";
        if(!mysqli_query($con, $query)){
            die("Error approving: " . mysqli_error($con));
        }
    } 
    elseif($_POST['action'] == 'decline'){
        // DECLINE: status = Declined, borrow_status = Declined
        $query = "UPDATE reservations 
                  SET status='Declined',
                      borrow_status='Declined'
                  WHERE reservation_id='$reservation_id'";
        if(!mysqli_query($con, $query)){
            die("Error declining: " . mysqli_error($con));
        }
    }
}

// balik sa Librarian Panel
header("Location: librarian.php#manage-reservations");
exit;
?>
