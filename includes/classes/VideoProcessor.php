<?php
class VideoProcessor{
    private $con;
    private $sizeLimit=500000000;
    private $allowedTypes=array("mp4","flv","mkv","vob","ogv","mov","wmv","mpg","avi","ogg");
    private $ffmpegPath;
    private $ffprobePath;

    public function __construct($con){
       
        $this->con=$con;
        $this->ffmpegPath=realpath("ffmpeg/bin/ffmpeg.exe");
        $this->ffprobePath=realpath("ffmpeg/bin/ffprobe.exe");
    }

    public function upload($videoUploadData){
        $targetDir="uploads/videos/";
        $videoData = $videoUploadData->videoDataArray;

        $tempFilePath = $targetDir . uniqid() . basename($videoData["name"]);

        $tempFilePath=str_replace(" ","_",$tempFilePath);

        $isValidData = $this->processData($videoData  ,$tempFilePath);
        if(!$isValidData){
            return false;
        }
        if(move_uploaded_file($videoData["tmp_name"] , $tempFilePath)){
            $finalFilePath= $targetDir. uniqid() . ".mp4";

            if(!$this->insertVideoData($videoUploadData,$finalFilePath)){
                echo("Insert Query Failed");

                return false;
            }

            if(!$this->convertVideoToMp4($tempFilePath,$finalFilePath)){
                echo "Upload Failed";
                return false;
            }
            if(!$this->deleteFile($tempFilePath)){
                echo "Upload Failed";
                return false;
            }
            if(!$this->generateThumbnails($finalFilePath)){
                echo "Upload Failed- couldnt generate thumbnails\n";
                return false;
            }
            return true;

        }
        //echo $tempFilePath;
    }

    private function processData($videoData, $filePath){
        $videoType= pathinfo($filePath, PATHINFO_EXTENSION);

        if(!$this->isValidSize($videoData)){
            echo "File too Large. Can't be more than".$this->sizeLimit;
            return false;
        }

        else if(!$this->isValidType($videoType)){
            echo "Invalid Type";
            return false;
        
        }
        else if($this->hasError($videoData)){
            echo("Error Code : ".$videoData["error"]);
            return false;
        }

        return true;
    }

    private function isValidSize($data){
        return $data["size"] <= $this->sizeLimit;
    }

    private function isValidType($type){
        $lowercased=strtolower($type);
        return in_array($lowercased, $this->allowedTypes);
    }

    private function hasError($data){
        return $data["error"]!=0;
    }
    private function insertVideoData($uploadData, $filePath){
        $query = $this->con->prepare("INSERT INTO videos(title,uploadedBy,Description,Privacy,category,filePath)
         VALUES(:title,:uploadedBy,:Description,:Privacy,:category,:filePath)");
        
        $query->bindParam(":title", $uploadData->title);
        $query->bindParam(":uploadedBy", $uploadData->uploadedBy);
        $query->bindParam(":Description", $uploadData->description);
        $query->bindParam(":Privacy", $uploadData->privacy);
        $query->bindParam(":category", $uploadData->category);
        $query->bindParam(":filePath", $filePath);
       
        return $query->execute();

    }
    public function convertVideoToMp4($tempFilePath, $finalFilePath)
    {
        $cmd="$this->ffmpegPath -i $tempFilePath $finalFilePath 2>&1";
        $outputLog = array();
        exec($cmd, $outputLog, $returnCode);

        if($returnCode !=0){
            foreach($outputLog as $line){
                echo $line ."<br>";
            }
            return false;
        }
        return true;
    }
    private function deleteFile($filePath){
        if(!unlink($filePath)){
            echo("Couldnot delete the file\n");
            return false;
        }
        return true;
    }
    public function generateThumbnails($filePath){
        $thumbnailSize="210x118";
        $numThumbnails=3;
        $pathToThumbnails="uploads/videos/thumbnails";
        $duration=$this->getVideoDuration($filePath);
        echo "duration :$duration";
        $videoId= $this->con->lastInsertId();
        $this->updateDuration($duration,$videoId);

        for($num=1 ; $num<=$numThumbnails ; $num++ ){
            $imageName=uniqid() .".jpg";
            $interval = ($duration * 0.8)/$numThumbnails* $num;
            $fullThumbnailPath="$pathToThumbnails/$videoId-$imageName";
            $cmd="$this->ffmpegPath -i $filePath -ss $interval -s $thumbnailSize -vframes 1 $fullThumbnailPath 2>&1";
            $outputLog = array();
            exec($cmd, $outputLog, $returnCode);
    
            if($returnCode !=0){
                foreach($outputLog as $line){
                    echo $line ."<br>";
                }
            }
           
            $query = $this->con->prepare("INSERT INTO thumbnails(videoId, filePath, selected)
             VALUES(:videoId, :filePath, :selected)");
            $selected= $num==1? 1:0;
            $query->bindParam(":videoId",$videoId);
            $query->bindParam(":filePath",$fullThumbnailPath);
            $query->bindParam(":selected",$selected);
           
            

            $success= $query->execute();
            if(!$success){
                echo "error inserting Thumbnails\n";
            }

        }
        return true;
    }
    public function getVideoDuration($filePath){
        return(int) shell_exec("$this->ffprobePath -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 $filePath");
    }

    private function updateDuration($duration,$videoId){
        $duration=(int)$duration;
        $hours=floor($duration / 3600);
        $mins=floor(($duration -($hours*3600)) / 60);
        $secs=$duration%60;
        if($hours<1){
            $hours="";
        }
        else{
            $hours=$hours.":";
        }
        if($mins<10){
            $mins="0".$mins.":";
        }
        else{
            $mins=$mins.":";
        }
        if($secs<10){
            $secs="0".$secs;
        }
        else{
            $secs=$secs;
        }
        $duration=$hours.$mins.$secs;

        $query = $this->con->prepare("UPDATE videos SET duration=:duration WHERE id=:videoId");
        $query->bindParam(":duration",$duration);
        $query->bindParam(":videoId",$videoId);
        $query->execute();
    }
}

?>
