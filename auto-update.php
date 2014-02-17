<?php
include "./library/DBClass.php";
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
    $result=str_replace("'", "\'", $str);
    return $result;
}
global $rootFolder;
$arEntries=array();
$firstInsert=true;
$orderID=999;
if ($handle = opendir($rootFolder)) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry!=="." && $entry!=="..")
        {
           if (($pos=strpos($entry,'.mp3')) !== false) {
                $db= new DBClass();
                $sql="select count(*) as nr from playlist p,melodii m where p.id_mel=m.id_mel "
                        . "and m.path='".DBCheckString($entry)."'";
                $row=$db->GetTable($sql);
                //insert
                if ($row[0]["nr"]==0)
                {
                    //get max order_id
                    if ($firstInsert)
                    {
                        $sqlOrderID="select max(order_id)as max_ord from playlist";
                        $dataOrderID=$db->GetTable($sqlOrderID);
                        if(count($dataOrderID)==0)
                        {
                            $orderID=1;
                        }
                        else
                        {
                            $orderID=  intval($dataOrderID[0]["max_ord"])+1;
                        }
                        $firstInsert=false;
                    }
                    $getID3 = new getID3;
                    $ThisFileInfo = $getID3->analyze($rootFolder.$entry);
                    getid3_lib::CopyTagsToComments($ThisFileInfo);
                    //header("Content-Type: application/json");
                    //echo json_encode($ThisFileInfo,JSON_PRETTY_PRINT);
                    //echo (!empty($ThisFileInfo['comments_html']['artist']) ? implode('<BR>', $ThisFileInfo['comments_html']['artist']) : '');
                    
                    $title=substr($entry, 0, $pos);
                    $album='';
                    $artist='';
                    if (array_key_exists('comments_html', $ThisFileInfo))
                    {
                        if (array_key_exists('title', $ThisFileInfo['comments_html']))
                        {
                            $title=DBCheckString($ThisFileInfo['comments_html']['title'][0]);
                        }
                    if (array_key_exists('album', $ThisFileInfo['comments_html']))
                        {
                            $album=DBCheckString($ThisFileInfo['comments_html']['album'][0]);
                        }
                        if (array_key_exists('artist', $ThisFileInfo['comments_html']))
                        {
                            $artist=DBCheckString($ThisFileInfo['comments_html']['artist'][0]);
                        }
                    }
                    $sqlInsert="insert into melodii(artist,title,album,path) "
                            . " values('".$artist."','".$title
                            ."','".$album."','".DBCheckString($entry)."')";
                    
                    $aBool=$db->ExecuteStatement($sqlInsert);
                    if ($aBool)
                    {
                        $sql="select id_mel from melodii where path='".DBCheckString($entry)."'";
                        $row=$db->GetTable($sql);
                        $id_mel=$row[0]["id_mel"];
                        $id_pl=1;
                        $sql_pl="insert into playlist (id_pl,id_mel,order_id) values ($id_pl,$id_mel,$orderID);";
                        $aBoolPl=$db->ExecuteStatement($sql_pl);
                        $orderID++;
                        echo " <b>$entry</b>";
                    }
                    else
                    {
                        echo "eroare la inserare";
                    }
                }
                else
                {
                    echo $entry;
                }
                $arEntries[count($arEntries)]=$entry;
                unset($db);
                echo "</br>";
            }  
        }
    }
}
 else {
     echo "Nu s-a putut deschide fisierul!";
}

$db= new DBClass();
$strList="(";
    for ($i=0;$i<count($arEntries);$i++)
    {
        if ($i==0)
        { 
            $strList.="'$arEntries[$i]'";
        }
     else {
           $strList.= ','."'$arEntries[$i]'";
        } 
    }
    $strList.=')';
$sql="select id_mel,path from melodii where path not in $strList";
$rows=$db->GetTable($sql);
 if (count($rows)>0)
 {
    $idList="(";
    for ($i=0;$i<count($rows);$i++)
    {
        echo "<strike>".$rows[$i]["path"]."</strike></br>";
        if ($i==0)
        { 
            $idList.=$rows[$i]["id_mel"];
        }
     else {
           $idList.= ','.$rows[$i]["id_mel"];
        } 
    }
    $idList.=')';
   $aBoolDelete=$db->ExecuteStatement("delete from playlist where id_mel  in $idList;");
   $aBoolDelete2=$db->ExecuteStatement("delete from melodii where id_mel in $idList;");    
 }

unset($db);
?>
