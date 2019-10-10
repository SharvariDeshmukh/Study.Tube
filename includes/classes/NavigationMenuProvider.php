<?php

class NavigationMenuProvider{
    private $con, $userLoggedInObj;

    public function __construct($con, $userLoggedInObj){
        $this->con=$con;
        $this->userLoggedInObj=$userLoggedInObj;
    }

    public function create(){
        $menuHtml = $this->createNavItem("Home","assets/icons/home.png" , "index.php");
        $menuHtml .= $this->createNavItem("Trending","assets/icons/trends.png" , "trending.php");

       if(User::isLoggedIn()){
            $menuHtml .= $this->createNavItem("Subscriptions","assets/icons/Subscription.png" , "index.php");
            $menuHtml .= $this->createNavItem("Liked Videos","assets/icons/thumb-up.png" , "LikedVideos.php");
            $menuHtml .= $this->createNavItem("Settings","assets/icons/Setting.png" , "Settings.php");
            $menuHtml .= $this->createNavItem("Logout","assets/icons/Logout.png" , "Logout.php");
            $menuHtml .=$this->createSubscriptionSection();
       }
       
       return "<div class='navigationItems'>
                    $menuHtml
                </div>";
    }
     private function createNavItem($text, $icon, $link){
     return "<div class='navigationItem'>
                <a href='$link'>
                    <img src='$icon'>
                <span>$text</span>
                </a>
            </div>";

     }

     private function createSubscriptionSection(){
        $subscriptions = $this->userLoggedInObj->getSubscriptions();
        $html = "<span class='heading'>Subscriptions</span>";
        foreach($subscriptions as $subs){
            $subUsername = $subs->getUsername();
            $html .=$this->createNavItem($subUsername, $subs->getProfilePic(), "profile.php?username=$subUsername");
        }
        return $html;
     }
}







?>