<?php 
require_once("includes/header.php");
require_once("includes/classes/VideoUploadData.php");
require_once("includes/classes/VideoProcessor.php");
require_once("includes/classes/VideoDetailsFormProvider.php");


if(!isset($_POST["uploadButton"])){
    echo "No file sent to page.";
    exit();
}
//1. Create file upload data
$videoUpoadData = new VideoUploadData(
        $_FILES["fileInput"],
        $_POST["titleInput"],
        $_POST["descriptionInput"],
        $_POST["privacyInput"],
        $_POST["categoriesInput"],
        $userLoggedInObj->getUserName()
);
//2.Process video upload data
$videoProcessor = new VideoProcessor($con);
$wasSuccessful=$videoProcessor->upload($videoUpoadData);
//3.Check if upload was successful
if($wasSuccessful){
    echo("Upload Successful");
}
    
?>