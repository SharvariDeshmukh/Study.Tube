<?php 
require_once("includes/config.php");
require_once("includes/classes/User.php");
require_once("includes/classes/ButtonProvider.php");
require_once("includes/classes/Video.php");
require_once("includes/classes/VideoGrid.php");
require_once("includes/classes/VideoGridItem.php");
require_once("includes/classes/SubscriptionProvider.php");
require_once("includes/classes/NavigationMenuProvider.php");

$usernameLoggedIn= User::isLoggedIn()? $_SESSION["userLoggedIn"] :"" ;
$userLoggedInObj = new User($con, $usernameLoggedIn);
?>
<!DOCTYPE html>
<html lang="en">
<head>

    

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>StudyTube</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="assets/js/commonAction.js"></script>
    <script src="assets/js/userActions.js"></script>
</head>
<body>
    <div id="pageContainer">
        <div id="mastHeadContainer">
            <button class="navShowHide">
                <img src="assets/Icons/menu.png">

            </button>
            <a class="logoContainer" href="index.php"> 
               <img src="assets/Icons/StudyTube.png" alt="siteLogo">
            </a>
            <div class="searchBarContainer">
                <form action="search.php" method="GET">
                    <input type="text" class="searchBar" name="term" placeholder="Search...">
                    <button class="searchButton">
                        <img src="assets/Icons/Search.png">
                    </button>
                </form>

            </div>
            <div class="rightIcons">
            <?php
                if(User::isLoggedIn()){
                    echo "<a href='upload.php'>
                        <img class='upload' src='assets/Icons/upload.png'>
                    </a>";
                }
                
                ?>
               <?php
               echo ButtonProvider::createUserProfileNavigationButton($con, $userLoggedInObj->getUserName());
               ?>
            </div> 
        </div>
        <div id="sideNavContainer" style="display:none;">
            <?php
                $navigationProvider = new NavigationMenuProvider($con , $userLoggedInObj);
                echo $navigationProvider->create();
            
            ?>
        </div>
        <div id="mainSectionContainer">
            <div id="mainContentContainer">
            