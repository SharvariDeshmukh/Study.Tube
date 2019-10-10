<?php


class CommentSection{
    private $video,$con,$userLoggedInObj;
    public function __construct( $con  , $video , $userLoggedInObj){
        $this->video = $video;
        $this->con = $con;
        $this->userLoggedInObj = $userLoggedInObj;
    }
    

    public function create(){
      return $this->createCommentSection();
    }

    public function createCommentSection(){

        $numComments = $this->video->getNumberOfComments();
        $postedBy = $this->userLoggedInObj->getUsername();
        $videoId= $this->video->getId();

        $profileButton = ButtonProvider :: createUserProfileButton($this->con , $postedBy);

        $commentAction = "postComment(this,\"$postedBy\",$videoId, null, \"comments\" )";

        $commentButton = ButtonProvider :: createButton("COMMENT", null,$commentAction, "postComment" );

        $comments = $this->video->getComments();
        $commentItems="";
        foreach($comments as $comment){
            $commentItems.=$comment->create();
        }

        return "<div class='commentSection' >
                    <div class='header'>
                        <span class = 'commentCount' > $numComments Comments </span>

                        <div class='commentForm'>
                            $profileButton
                            <textarea class='commentBodyClass' placeholder = 'Add a public comment'></textarea>
                            $commentButton
                        </div>
                    </div>

                        <div class='comments'>
                        $commentItems
                        </div>
                </div>";

    }
    


    
}





?>