<?php 
require_once("includes/header.php");
require_once("includes/classes/Video.php");
require_once("includes/classes/VideoPlayer.php");
require_once("includes/classes/VideoInfo.php");
require_once("includes/classes/Comment.php");
require_once("includes/classes/CommentSection.php");

if(!isset($_GET["id"])){
    echo "Page Not Found";
    exit();
}

$video = new Video($con, $_GET["id"], $userLoggedInObj);
$video->incrementViews();
//echo $video->getTilte();

?>

<script src="assets/js/videoPlayerActions.js"></script>
<script src="assets/js/commentActions.js"></script>
<div class="watchLeftColumn">

    <?php
        $videoPlayer=new VideoPlayer($video);
        echo $videoPlayer->create(true);

        $videoPlayer=new VideoInfo($con,$video,$userLoggedInObj);
        echo $videoPlayer->create();

        $commentSection=new CommentSection($con,$video,$userLoggedInObj);
        echo $commentSection->create();
    ?>
</div>


<div class='suggestions'>
    <?php
    
    $videoGrid = new VideoGrid($con, $userLoggedInObj);
    echo $videoGrid->create(NULL, null, false);
    
    ?>
</div>


<?php require_once("includes/footer.php");?>