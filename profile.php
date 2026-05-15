<?php
session_start();
include("connection.php");
$con = connection();

//check kung may naka log in
if(!isset($_SESSION['userid'])){
    header("Location: DigitalLibrary.php");
    exit();
}

$user_id = $_SESSION['userid'];

$query = "SELECT a.id, a.fname, a.mname, a.lname, a.email, a.role,
          p.profile_img, p.bio, p.phone, p.address
          FROM account a
          JOIN profile p ON a.id = p.account_id
          WHERE a.id='$user_id'";

$result = mysqli_query($con,$query);
$user = mysqli_fetch_assoc($result);

if(isset($_SESSION['userid'])){
    $user_id = $_SESSION['userid'];
    $queryUser = "SELECT profile_img, fname FROM profile WHERE account_id='$user_id'";
    $resultUser = mysqli_query($con,$queryUser);
    $userData = mysqli_fetch_assoc($resultUser);
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
        <title>My profile</title>
        <link rel="stylesheet" href="assets/style1.css">
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
                    <img src="<?php echo !empty($userData['profile_img']) ? 'uploads/' . $userData['profile_img'] : 'assets/images/people.png' ?>" alt="Profile" class="profile-img">
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

        <section class="profile-wrapper">
    <div class="profile-card">
        <div class="profile-header">
            <img src="<?php echo !empty($user['profile_img']) ? 'uploads/' . $user['profile_img'] : 'assets/images/people.png'; ?>" 
                 alt="Profile Picture" class="profile-avatar">
            <div class="profile-info">
                <h2><?php echo $user['fname'].' '.$user['lname']; ?></h2>
                <p class="profile-role"><?php echo ucfirst($user['role']); ?></p>
            </div>
            <button id="editBtn" class="btn-edit">Edit Profile</button>
        </div>

        <div class="profile-body">
            <h3>About</h3>
            <p><strong>Bio:</strong> <?php echo $user['bio']; ?></p>
            <p><strong>Phone:</strong> <?php echo $user['phone']; ?></p>
            <p><strong>Address:</strong> <?php echo $user['address']; ?></p>
        </div>

        <div class="profile-body" id="editForm" style="display:none;">
            <form method="POST" enctype="multipart/form-data" action="update_profile.php">
                <div class="form-group">
                    <label>Bio</label>
                    <textarea name="bio" class="input-box"><?php echo $user['bio']; ?></textarea>
                </div>

                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" class="input-box" value="<?php echo $user['phone']; ?>">
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" class="input-box" value="<?php echo $user['address']; ?>">
                </div>

                <div class="form-group">
                    <label>Profile Picture</label>
                    <input type="file" name="profile_img" class="file-upload">
                </div>

                <button type="submit" name="update" class="btn-save">Save Changes</button>
            </form>
        </div>
    </div>
</section>

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
    //hahanapin yung button at form
    const editBtn = document.getElementById('editBtn');
    const editForm = document.getElementById('editForm');

    //toggle visibility ng form kapag pinindot ang button
    editBtn.addEventListener('click', () =>{
        if(editForm.style.display === 'none' || editForm.style.display ==='' ){
            editForm.style.display = "block";
            editBtn.textContent = "Cancel";
        }else{
            editForm.style.display = "none";
            editBtn.textContent = "Edit Profile";
        }
    });

            const navbarToggle = document.querySelector('.navbar-toggle');
        const navbarMenu = document.querySelector('.navbar-menu');

        navbarToggle.addEventListener('click', () => {
            navbarToggle.classList.toggle('active');
            navbarMenu.classList.toggle('active');
        });

    </script>
</html>