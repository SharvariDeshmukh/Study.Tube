<?php
require_once("../includes/config.php");
if(isset($_POST['userTo']) && isset($_POST['userFrom'])){
    $userTo = $_POST['userTo'];
    $userFrom = $_POST['userFrom'];
    //if user is subbed

    $query = $con->prepare("SELECT * FROM subscribers where userTo=:userTo and userFrom=:userFrom ");
    $query->bindParam("userTo", $userTo);
    $query->bindParam("userFrom",$userFrom);
    $query->execute();

    if($query->rowCount()==0){
        $query = $con->prepare("INSERT  INTO subscribers(userTo, userFrom) VALUES(:userTo , :userFrom)");
        $query->bindParam("userTo",$userTo);
        $query->bindParam("userFrom",$userFrom);
        $query->execute();

    }
    else{
        //then delete
        $query = $con->prepare("DELETE FROM subscribers where userTo=:userTo and userFrom=:userFrom");
        $query->bindParam("userTo",$userTo);
        $query->bindParam("userFrom",$userFrom);
        $query->execute();

    }
    $query = $con->prepare("SELECT * FROM subscribers where userTo=:userTo ");
    $query->bindParam("userTo", $userTo);
    $query->execute();   
    echo $query->rowCount();
    //if not subbed then sub
    //then insert
    //new no of subss
}
else{
    echo "one param missing!";
}








?>