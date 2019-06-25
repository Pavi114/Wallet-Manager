<?php 
include("initial.php");
   $query = "CREATE TABLE IF NOT EXISTS person (
     id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(60) NOT NULL,
    last_name VARCHAR(60) NOT NULL,
    username VARCHAR(60) NOT NULL,
    password VARCHAR(255) NOT NULL,
    balance INT UNSIGNED NOT NULL,
    amount_lent INT UNSIGNED NOT NULL,
    amount_borrowed INT UNSIGNED NOT NULL
    )";

    $create = mysqli_query($con,$query);

    $query = "CREATE TABLE IF NOT EXISTS expense (
               id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
               username VARCHAR(60) NOT NULL,
               title VARCHAR(60) NOT NULL,
               description VARCHAR(240) NOT NULL,
               cost INT UNSIGNED NOT NULL,
               friend_name VARCHAR(200) NOT NULL DEFAULT '',
               amount_owe VARCHAR(200) NOT NULL DEFAULT '0',
               amount_lent INT DEFAULT 0,
               cur_date DATE NOT NULL
                )";

    $create = mysqli_query($con,$query);
    
    $query = "CREATE TABLE IF NOT EXISTS settle(
              id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              borrower VARCHAR(60) NOT NULL,
              lender VARCHAR(60) NOT NULL,
              amt_borrowed INT NOT NULL,
              cur_date DATE NOT NULL
           )";           

     $create = mysqli_query($con,$query);
     
     $query = "CREATE TABLE IF NOT EXISTS activities(
               id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
               username VARCHAR(60) NOT NULL,
               message TEXT NOT NULL,
               cur_date DATE NOT NULL
           )";
     $create = mysqli_query($con,$query);
     
     ?>              