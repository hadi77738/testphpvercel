<?php
// File: includes/header.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) : 'Idiomatch' ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <header class="navbar">
        <a href="index.php" class="logo">Idiomatch</a>
        
        <ul class="nav-links" id="nav-links">
            <li><a href="index.php">Beranda</a></li>
            <li><a href="quiz.php">Quiz</a></li>
            <li><a href="login.php">Login</a></li>
        </ul>
        
        <button class="hamburger" id="hamburger">&#9776;</button>
    </header>

    <div class="container">

