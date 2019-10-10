<?php
class SubscriptionProvider{
private $con, $userLoggedInObj;
    public function __construct($con, $userLoggedInObj)
    {
        $this->con = $con;
        $this->userLoggedInObj = $userLoggedInObj;

        
    }

    public function getVideos(){
        $videos = array();
        $suscriptions  = $this->userLoggedInObj->getSubscriptions();
        if(sizeof($suscriptions)>0){
          
            // $query->bindParam(1,"user1");
            // $query->bindParam(2,"user2");
            // $query->bindParam(3,"user3");

            $condition = "";
            $i=0;

            while($i <sizeof($suscriptions)){
                if($i==0){
                    $condition .= "WHERE uploadedBy=?";
                }
                else{
                    $condition .= "OR uploadedBy=?";
                }
                $i++;
            }
            $videoSql = "SELECT * FROM videos $condition ORDER BY uploadDate DESC";
            $videoQuery = $this->con->prepare($videoSql);
            $i = 1;

            foreach($suscriptions as $sub){
                $subUsername=$sub->getUsername();
                $videoQuery->bindValue($i, $subUsername);
                $i++;
            }

            $videoQuery->execute();
            while($row = $videoQuery->fetch(PDO :: FETCH_ASSOC)){
                $video = new Video($this->con , $row, $this->userLoggedInObj);
                array_push($videos , $video);
            }

        }
        return $videos;
    }

}
?>