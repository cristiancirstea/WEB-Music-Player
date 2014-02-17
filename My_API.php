<?php

include_once './API.php';
require_once './library/DBClass.php';
include "./library/getid3/getid3.php";
/**
 * Verifica daca stringul e bun pt a fi transmis in Firebird.
 * 
 * <em>Deocamdata verifica doar apostroafele</em>
 * 
 * @param String $str string-ul de verificat
 * 
 * @return String string-ul bun
 */
function DBCheckString($str)
{
    $result=str_replace("'", "''", $str);
    return $result;
}
 function TestVB($text)
 {
     $sql="insert into test values ('$text')";
     $db= new DBClass();
     $db->ExecuteStatement($sql);
     unset($db);
     return "BRAVO";
 }
function MyFormatFloat($aFloat,$decimals = 2,$dec_point = '.',$thousands_sep = ',' )
{
    return number_format($aFloat,$decimals,$dec_point,$thousands_sep);
}
function MyFormatDate($aDateString,$format='d.m.Y') /* 'H:i' - pt ora*/
{
   $aDate=new DateTime(''.$aDateString);
   return $aDate->format($format);
}

    function psMicroTime()
{
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}
 /**
  * Genereaza un id unic in functie de timp de 21 de caractere.
  * 
  * @return String
  */
 function GenerareRandID()
    {
        $chars=str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ');
        shuffle($chars);
        $theChars=implode('',  array_slice($chars, rand(0, 22),3));
        $theTime=(string)psMicroTime();
            if (strlen($theTime)<15)
                for ($i=0;$i<(15-strlen($theTime));$i++)
                   $theTime.=0;
            if (strlen($theTime)>15)
                $theTime=  substr ($theTime, 0,15);
        return $theTime.$theChars."PIC"; 
    }
function GetAlbumArt($path1)
{
    global $rootFolder;
    $Path=$rootFolder.$path1;
    $getID3 = new getID3;
    $OldThisFileInfo = $getID3->analyze($Path);
    if(isset($OldThisFileInfo['comments']['picture'][0])){
    $Image='data:'.$OldThisFileInfo['comments']['picture'][0]['image_mime'].
              ';charset=utf-8;base64,'.
              base64_encode($OldThisFileInfo['comments']['picture'][0]['data']);
    }
    else
    $Image="./library/img/default.jpg";
    return $Image;
}
class MyAPI extends API
{
    protected $User;
    protected $_goodUser;
    protected $_userID;
    public function __construct($request, $origin) {
        parent::__construct($request);
        $this->_goodUser=false;
        $this->_userID=0;
        
//        
//          echo "<br>".$this->file;
       if (strtoupper($this->method)=="PUT")
        {
           
           $putParams=array();
           if (!$this->request)
            $this->request=array();
           $putParams=  json_decode($this->file,true);
           $this->request=array_merge( $this->request,$putParams);
        }
        $this->_goodUser=true;
        //!!!!!!!!!!!! + Autentificare !!!!!!!!!!!!!!!
    }
    
    protected function next($params)
    {
       $db= new DBClass();
         if (count($params)===0)
         {
            $sql="select id_mel,order_id from playlist_mel where playing>0 limit 1";
            $data=$db->GetTable($sql);
            if (count($data)==0)
            {
                 $sql="select * from playlist_mel  "
                         . " "
                         . " "
                         . " order by order_id,title limit 1";
            }
            else
            {
                   $sql="select * from playlist_mel where "
                         . " order_id >".$data[0]["order_id"]
                         . " "
                         . " order by order_id,title limit 1";
            }
         }
         else
         {
              switch ($params[0]) {
                 case "id":
                      $sql="select * from playlist_mel where "
                         . " order_id >(select order_id from playlist_mel where id_mel=$params[1]) "
                         . " "
                         . " order by order_id,title limit 1";
                     break;
                 case "title":
                      $sql="select * from playlist_mel where title=$params[1] limit 1";
                     break;
                 default:
                     throw new Exception("Ceva necunoscut!");
                     break;
             }
         }
         $data=$db->GetTable($sql);
          if (count($data)==0)
         {
             $sql="select * from playlist_mel order by order_id,title limit 1"; 
         }
         $data=$db->GetTable($sql);
         $sqlUpdate="update playlist set playing=0 where playing>0;";
         $aBool=$db->ExecuteStatement($sqlUpdate);
         if ($aBool)
         {
            $sqlUpdate="update playlist set playing=3 where id_mel=".$data[0]["id_mel"];
            $aBool2=$db->ExecuteStatement($sqlUpdate); 
         }
         $data[0]["album_art"]=  GetAlbumArt($data[0]["path"]);
          unset($db);
         return $data;
    }
    protected function pause($params)
    {
        $db= new DBClass();
        $sqlUpdate="update playlist set playing=2 where playing=1;";
         $aBool=$db->ExecuteStatement($sqlUpdate);
         unset($db);
         return array("success"=>$aBool);
    }
    protected function play($params)
    {
        /* 0-not playing
         * 1-playing
         * 2-paused
         * 3-selected
         */
         $db= new DBClass();
         if (count($params)===0)
         {
            $sql="select * from playlist_mel where playing>0 order by playing,order_id,title limit 1";
            $setPlaying=1;
         }
         else
         {
             switch ($params[0]) {
                 case "id":
                      $sql="select * from playlist_mel where id_mel=$params[1]  order by order_id,title limit 1";
                     $setPlaying=1;
                     break;
                 case "title":
                      $sql="select * from playlist_mel where nume=$params[1] order by order_id,title limit 1";
                     $setPlaying=1;
                     break;
                 default:
                     throw new Exception("Ceva necunoscut!");
                     break;
             }
         }
         $data=$db->GetTable($sql);
         if (count($data)==0)
         {
             $sql="select * from playlist_mel order by order_id,title limit 1"; 
             $setPlaying=3;
         }
         $data=$db->GetTable($sql);
         $sqlUpdate="update playlist set playing=0 where playing>0;";
         $aBool=$db->ExecuteStatement($sqlUpdate);
         if ($aBool)
         {
            $sqlUpdate="update playlist set playing=$setPlaying where id_mel=".$data[0]["id_mel"];
            $aBool2=$db->ExecuteStatement($sqlUpdate); 
         }
         $data[0]["album_art"]=  GetAlbumArt($data[0]["path"]);
         unset($db);
         return $data;
    }
    protected function prev($params)
    {
        $db= new DBClass();
         if (count($params)===0)
         {
            $sql="select id_mel,order_id from playlist_mel where playing>0 limit 1";
            $data=$db->GetTable($sql);
            if (count($data)==0)
            {
                 $sql="select * from playlist_mel  "
                         . " "
                         . " "
                         . " order by order_id desc ,title limit 1";
            }
            else
            {
                   $sql="select * from playlist_mel where "
                         . " order_id<".$data[0]["order_id"]
                         . " "
                         . " order by order_id desc,title limit 1";
            }
         }
         else
         {
              switch ($params[0]) {
                 case "id":
                      $sql="select * from playlist_mel where "
                         . " order_id <(select order_id from playlist_mel where id_mel=$params[1]) "
                         . " "
                         . " order by order_id desc,title limit 1";
                     break;
                 case "title":
                      $sql="select * from playlist_mel where title=$params[1] limit 1";
                     break;
                 default:
                     throw new Exception("Ceva necunoscut!");
                     break;
             }
         }
         $data=$db->GetTable($sql);
          if (count($data)==0)
         {
             $sql="select * from playlist_mel order by order_id desc,title limit 1"; 
         }
         $data=$db->GetTable($sql);
         $sqlUpdate="update playlist set playing=0 where playing>0;";
         $aBool=$db->ExecuteStatement($sqlUpdate);
         if ($aBool)
         {
            $sqlUpdate="update playlist set playing=3 where id_mel=".$data[0]["id_mel"];
            $aBool2=$db->ExecuteStatement($sqlUpdate); 
         }
         $data[0]["album_art"]=  GetAlbumArt($data[0]["path"]);
          unset($db);
         return $data;
    }
    
    protected function playlist($params)
    {
         $db= new DBClass();
         if (count($params)===0)
         {
            $sql="select * from playlist_mel order by order_id,title";
         }
         $data=$db->GetTable($sql);
         if (count($data)==0)
             throw new Exception("Empty playlist!");
         for ($i = 0; $i < count($data); $i++) {
             $data[$i]["album_art"]=  GetAlbumArt($data[$i]["path"]);
         }
         unset($db);
         return $data;
    }
 }
?>
