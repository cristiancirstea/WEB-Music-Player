<?php
require_once './library/database.php';

/**
 * Description of DBClass
 *
 * @author cristi
 */
class DBClass {
    private $_connection;
    public function __construct() 
     {
       try{
            $this->_connection=  dbConnection();
        }
        catch (Exception $e)
        {
            throw new Exception("DB00001 Eroare la crearea conexiunii cu baza de date."/*.$e*/);
            dbCloseConnection();
        }
        
    }   
    
 /**
 * <b>Folosita la toate select-urile din baza!</b><br><br> 
 * Executa un text sql pentru selectarea datelor dintr-un tabel.<br>
 * Dupa executarea query-ului returneaza un vector de vectori asociativi. 
 * 
 * @param string $selectString Textul SQL 
 * 
 * @return Associative_array valoare=$result[index]["NUME_COLOANA"]
 * 
 * @throws Exception 
 */
    public function GetTable($queryStr)
    {
        if (!($this->_connection))
            return null;
        $dataArr=array();
        try
        {
            $ResultTemp = dbQuery($this->_connection, $queryStr );
            $count=0;
            while ($dataRow= dbFetchAssoc($ResultTemp))
            {
                $dataArr[$count]=$dataRow;
                $count++; 
            }
            dbCommit($this->_connection);
            dbFreeResult($ResultTemp);
           //  $file="text.txt";
       // file_put_contents($file,"\n - "."select" , FILE_APPEND | LOCK_EX);
        }
        catch (Exception $e)
        {
            throw new Exception("DB00002 Eroare la selectarea datelor din baza.". $e);
            if ($this->_connection)
           dbRollback($this->_connection); 
        }
            return $dataArr;
    }
    
 /**
 * <b>Folosita la toate executarile de query-uri in baza!</b><br><br>
 * Executa un text sql + commit.<br> 
 * 
 * @param string $executeString Textul SQL 
 * 
 * @return bool Daca s-a executat sau nu query-ul
 * 
 * @throws Exception
 */
    public function ExecuteStatement($executeString)
    {
        if (!($this->_connection))
            return null;
        try
        {
            dbQuery($this->_connection, $executeString );
            $aBool= dbCommit($this->_connection);
            return $aBool;
        }
        catch(Exception $e)
        {
           throw new Exception("DB00003 Eroare la executarea comenzii.".$e);
           if ($this->_connection)
           dbRollback($this->_connection); 
           return FALSE;
        }
    }
    public function __destruct() {
       try
        {
            dbCloseConnection($this->_connection);
        }
        catch(Exception $e)
        {
           throw new Exception("DB00004 Eroare la inchiderea conexiunii cu baza de date.". $e);
           return FALSE;
        } 
        
    }
}

?>
