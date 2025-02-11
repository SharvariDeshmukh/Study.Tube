<?php

// require_once("includes/classes/VideoPlayer.php");
// require_once("includes/classes/VideoInfo.php");
class Video{
    private $con , $sqlData , $userLoggedInObj;

    public function __construct($con, $input, $userLoggedInObj){
        $this->con=$con;
        $this->userLoggedInObj=$userLoggedInObj;

        if(is_array($input)){
            $this->sqlData=$input;
        }
        else{

            
                $query=$this->con->prepare("SELECT * FROM videos WHERE id= :id");
                $query->bindParam(":id", $input);
                $query->execute();

                $this->sqlData= $query->fetch(PDO::FETCH_ASSOC);

        }

    }

    public function getLikes(){
        $videoId=$this->getId();
        
        $query=$this->con->prepare("SELECT  count(*) as 'count' FROM likes WHERE videoId= :videoId");
        $query->bindParam(":videoId", $videoId);
        $query->execute();

        $data= $query->fetch(PDO::FETCH_ASSOC);

        return $data["count"];
    }

    public function getDislikes(){
        $query=$this->con->prepare("SELECT  count(*) as 'count' FROM dislikes WHERE videoId= :videoId");
        $videoId=$this->getId();
        $query->bindParam(":videoId",$videoId);
        $query->execute();

        $data= $query->fetch(PDO::FETCH_ASSOC);

        return $data["count"];
    }
    public function getId(){
        return $this->sqlData["id"];

    }

    public function getDuration(){
        return $this->sqlData["duration"];

    }
    public function getViews(){
        return $this->sqlData["views"];

    }


    public function getUploadedBy(){
        return $this->sqlData["uploadedBy"] ;

    }

    public function getTilte(){
        return $this->sqlData["title"];

    }

    public function getFilePath(){
        return $this->sqlData["filePath"];
    }


    public function getDescription(){
        return $this->sqlData["Description"];

    }
    public function getCategory(){
        return $this->sqlData["category"];

    }
    public function getUploadDate(){
        $date=$this->sqlData["uploadDate"];
        return date("M j, Y", strtotime($date));

    }
    public function getTimeStamp(){
        $date=$this->sqlData["uploadDate"];
        return date("M jS, Y", strtotime($date));

    }
    public function getProfilePic(){
        return $this->sqlData["filePath"];

    }

    public function incrementViews(){
        $query = $this->con->prepare("UPDATE videos SET views=views+1 WHERE id=id");
        $videoId=$this->getId();
        $query->bindParam(":id", $videoId);

        
        $query->execute();

        $this->sqlData["views"] = $this->sqlData["views"]+1;
        }

    public function like(){
                
        $id=$this->getId();
        $username = $this->userLoggedInObj->getUsername();

        if($this->wasLikedBy()){
            $query = $this->con->prepare("DELETE FROM likes WHERE username=:username AND videoId=:videoId");
            $username = $this->userLoggedInObj->getUsername();
            $query->bindParam(":username", $username);
            $query->bindParam(":videoId", $id);
            $query->execute();
            //user has already liked
            $result  =array(
                "likes" => -1 ,
                "dislikes" => 0
            );
            return json_encode($result);
        }
        else{
            $query = $this->con->prepare("DELETE FROM dislikes WHERE username=:username AND videoId=:videoId");
            $username = $this->userLoggedInObj->getUsername();
            $query->bindParam(":username", $username);
            $query->bindParam(":videoId", $id);
            $query->execute();
            $count = $query->rowCount();
            
            //not liked
            $query=$this->con->prepare("INSERT INTO likes(username, videoId) VALUES (:username ,:videoId)");
            $username = $this->userLoggedInObj->getUsername();
            $query->bindParam(":username", $username);
            $query->bindParam(":videoId", $id);
            $query->execute();

            $result  =array(
                "likes" => 1,
                "dislikes" => 0 - $count
            );
            return json_encode($result);
        }
    }
    public function getComments(){
        $query = $this->con->prepare("SELECT * FROM comments WHERE videoId=:videoId AND responseTo=0 ORDER BY datePosted DESC");
        $id=$this->getId();
        $query->bindParam(":videoId", $id);
        $query->execute();

        $comments  =array();
        while($row = $query->fetch(PDO::FETCH_ASSOC)){
            $comment = new Comment($this->con, $row, $this->userLoggedInObj, $id);
            array_push($comments, $comment);
        }
        return $comments;

    }
    public function wasLikedBy(){
        $id=$this->getId();
        $query=$this->con->prepare("SELECT * FROM likes WHERE username=:username AND videoId=:videoId");
        $username = $this->userLoggedInObj->getUsername();
        $query->bindParam(":username", $username);
        $query->bindParam(":videoId", $id);

        $query->execute();

        return $query->rowCount() > 0;
        
    }

    public function wasDislikedBy(){
        $id=$this->getId();
        $query=$this->con->prepare("SELECT * FROM dislikes WHERE username=:username AND videoId=:videoId");
        $username = $this->userLoggedInObj->getUsername();
        $query->bindParam(":username", $username);
        $query->bindParam(":videoId", $id);

        $query->execute();

        return $query->rowCount() > 0;
        
    }

    public function getNumberOfComments(){
        $query = $this->con->prepare("SELECT * FROM comments WHERE videoId=:videoId");
        $id=$this->getId();
        $query->bindParam(":videoId", $id);
        $query->execute();

        return $query->rowCount();
        
    }

    public function dislike(){
                
        $id=$this->getId();
        $username = $this->userLoggedInObj->getUsername();

        if($this->wasDislikedBy()){
            $query = $this->con->prepare("DELETE FROM dislikes WHERE username=:username AND videoId=:videoId");
            $username = $this->userLoggedInObj->getUsername();
            $query->bindParam(":username", $username);
            $query->bindParam(":videoId", $id);
            $query->execute();
            //user has already liked
            $result  =array(
                "likes" => 0 ,
                "dislikes" => -1
            );
            return json_encode($result);
        }
        else{
            $query = $this->con->prepare("DELETE FROM likes WHERE username=:username AND videoId=:videoId");
            $username = $this->userLoggedInObj->getUsername();
            $query->bindParam(":username", $username);
            $query->bindParam(":videoId", $id);
            $query->execute();
            $count = $query->rowCount();
            
            //not liked
            $query=$this->con->prepare("INSERT INTO dislikes(username, videoId) VALUES (:username ,:videoId)");
            $username = $this->userLoggedInObj->getUsername();
            $query->bindParam(":username", $username);
            $query->bindParam(":videoId", $id);
            $query->execute();

            $result  =array(
                "likes" => 0 - $count,
                "dislikes" => 1
            );
            return json_encode($result);
        }
    }

    public function getThumbnail(){
        $query = $this->con->prepare("SELECT filePath FROM thumbnails WHERE videoId=:videoId AND selected=1");
        $videoId =$this->getId();
        $query->bindParam(":videoId", $videoId);
        $query->execute();

        return $query->fetchColumn();


    }
}




?>