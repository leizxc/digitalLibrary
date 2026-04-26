<?php
session_start();
include("connection.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Librarian Panel</title>
  <link rel="stylesheet" href=".\assets\style2.css">
</head>
<body>
<div class="dashboard-container">
  <!-- Sidebar -->
  <div class="sidebar">
    <h2>Librarian Panel</h2>
    <ul>
      <li><a href="#dashboard" onclick="showSection('dashboard')">Dashboard</a></li>
      <li><a href="#add-book" onclick="showSection('add-book')">Add Book</a></li>
      <li><a href="#manage-reservations" onclick="showSection('manage-reservations')">Reservations</a></li>
      <li><a href="#borrowed" onclick="showSection('borrowed')">Borrowed Books</a></li>
      <li><a href="#reports" onclick="showSection('reports')">Reports</a></li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <header>
      <h1>Librarian Dashboard</h1>
    </header>

    <!-- Dashboard Section -->
    <section id="dashboard" class="active">
      <h2>Welcome Librarian</h2>
      <div class="cards">
        <div class="card">Total Books: 1,250</div>
        <div class="card">Active Reservations: 45</div>
        <div class="card">Overdue Books: 12</div>
        <div class="card">Registered Users: 320</div>
      </div>
    </section>

    <!-- Add Book Section -->
    <section id="add-book">
      <h2>Add Book</h2>
      <form action="./add_book.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Book Title" required>
        <input type="text" name="author" placeholder="Author" required>
        <input type="file" name="image" required>
        <select name="status">
          <option value="Available">Available</option>
          <option value="Not Available">Borrowed</option>
        </select>
        <select name="bookstat">
          <option value="Softcopy">Soft copy</option>
          <option value="Hardcopy">Hardcopy</option>
          <option value="Softcopy/Hardcopy">Softcopy/ Hardcopy</option>
        </select>
        <button type="submit">Add Book</button>
      </form>
    </section>

    <!-- Reservations Section -->
    <section id="manage-reservations">
      <h2>Manage Reservations</h2>
    </section>

    <!-- Borrowed Books Section -->
    <section id="borrowed">
      <h2>Borrowed Books</h2>
      <p>hdasuidhasuihdashbdiasbndkjasnbdkasndjaksndasndnasod</p>
    </section>

    <!-- Reports Section -->
    <section id="reports">
      <h2>Reports</h2>
    </section>
  </div>
</div>

<script>
  function showSection(sectionId){
    // hide all sections
    const sections = document.querySelectorAll('section');
    sections.forEach(sec => sec.classList.remove('active'));

    // show selected section
    const target = document.getElementById(sectionId);
    if(target){
      target.classList.add('active');
    }
  }
</script>
</body>
</html>
