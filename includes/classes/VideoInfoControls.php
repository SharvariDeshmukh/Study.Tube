<?php
require_once("includes/classes/ButtonProvider.php");

class VideoInfoControls{
    private $video,$userLoggedInObj;
    public function __construct($video , $userLoggedInObj){
        $this->video = $video;
        $this->userLoggedInObj = $userLoggedInObj;
    }
    

    public function create(){
        $likeButton=$this->createLikeButton();
        $dislikeButton=$this->createDislikeButton();
        return "<div class='controls'>
                $likeButton
                $dislikeButton
                </div>";
    }

    private function createLikeButton(){
        $text= $this->video->getLikes();
        $videoId=$this->video->getId();
        $action="likeVideo(this,$videoId)";
        $class="likeButton";
        $imageSrc="assets/Icons/thumb-up.png";



        if ($this->video->wasLikedBy()){
            $imageSrc="assets/Icons/thumb-up-active.png";
 
        }
        return ButtonProvider::createButton($text,$imageSrc,$action,$class);

        //Change button image if video is liked already
    }

    private function createDislikeButton(){
        $text= $this->video->getDislikes();
        $videoId=$this->video->getId();
        $action="dislikeVideo(this,$videoId)";
        $class="dislikeButton";
        $imageSrc="assets/Icons/thumb-down.png";

        if ($this->video->wasDislikedBy()){
            $imageSrc="assets/Icons/thumb-down-active.png";
 
        }
        return ButtonProvider::createButton($text,$imageSrc,$action,$class);

    }
}


?>
