<?php
require_once("ButtonProvider.php");

class CommentControls{
    private $con, $comment , $userLoggedInObj;
    public function __construct( $con, $comment , $userLoggedInObj){
        $this->con = $con;
        $this->userLoggedInObj = $userLoggedInObj;
        $this->comment = $comment;
    }
    

    public function create(){
        $likeButton=$this->createLikeButton();
        $dislikeButton=$this->createDislikeButton();
        $replyButton = $this->createReplyButton();
        $replySection = $this->createReplySection();
        $likesCount = $this->createLikesCount();
        return "<div class='controls'>
                $replyButton
                $likesCount
                $likeButton
                $dislikeButton
                </div>
                $replySection";
    }

    private function createReplyButton(){
        $text = "REPLY";
        $action = "toggleReply(this)";
        return ButtonProvider ::  createButton($text, null, $action , null);
    }
    private function createReplySection(){
       
        $postedBy = $this->userLoggedInObj->getUsername();
        $videoId= $this->comment->getVideoId();
        $commentId= $this->comment->getId();

        $profileButton = ButtonProvider :: createUserProfileButton($this->con , $postedBy);

        $cancelButtonAction = "toggleReply(this)";
        $cancelButton = ButtonProvider::createButton("Cancel", null,$cancelButtonAction, "cancelComment" );

        $postButtonAction = "postComment(this, \"$postedBy\", $videoId,$commentId, \"repliesSection\")" ;
        $postButton = ButtonProvider::createButton("Reply", null, $postButtonAction, "postComment" );


        return "<div class='commentForm hidden'>
                    $profileButton
                    <textarea class='commentBodyClass' placeholder = 'Add a public comment'></textarea>
                    $cancelButton
                    $postButton
                </div>";

    }
    private function createLikesCount(){
        $text = $this->comment->getLikes();

        if($text == 0) $text = "";

        return "<span class = 'likesCount'>$text</span>";
    }
    private function createLikeButton(){
        $videoId=$this->comment->getVideoId();
        $commentId=$this->comment->getId();
        $action="likeComment($commentId, this,$videoId)";
        $class="likeButton";
        
        $imageSrc="assets/Icons/thumb-up.png";



        if ($this->comment->wasLikedBy()){
            $imageSrc="assets/Icons/thumb-up-active.png";
 
        }
        return ButtonProvider::createButton("",$imageSrc,$action,$class);

        //Change button image if video is liked already
    }

    private function createDislikeButton(){
        $videoId=$this->comment->getId();
        $commentId=$this->comment->getVideoId();
        $action="dislikeVideo($commentId, this,$videoId)";
        $class="dislikeButton";
        $imageSrc="assets/Icons/thumb-down.png";

        if ($this->comment->wasDislikedBy()){
            $imageSrc="assets/Icons/thumb-down-active.png";
 
        }
        return ButtonProvider::createButton("",$imageSrc,$action,$class);

    }
}


?>
