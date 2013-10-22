<?php
namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;


class User extends \Standard\Entity\User
{   
        public function getGridSql (array $gridInitialData = array())
        {
        	// Get the where condition
        	$where = $gridInitialData["where"];
        
        	// Get the count
        	$count = $gridInitialData["count"];
        
        	// Get offset
        	$offset = $gridInitialData["offset"];
        
        	// Get the order
        	$order = $gridInitialData["order"];
        
        	$em = $this->getEntityManager();
        
        	$to = $count + $offset;
        	if ($order != null) {
        		$order = "ORDER BY " . $order;
        	}
        
        	$sql = "SELECT
        	u.*
        	FROM
        	user u
        	WHERE u.group_id=3 AND {$where} {$order} LIMIT {$offset},{$to}";
        	return $sql;
        }
    
    	public function getFilteredRecordSql (array $gridInitialData = array())
    	{
    	// Get the where condition
    	$where = $gridInitialData["where"];
    
    		$sql = "SELECT
    		count(*) as total_records
    		FROM
    		user u
    		WHERE u.group_id=3 AND  {$where}";
    		return $sql;
    	}
    
    	public function getTotalRecordSql ()
    	{
    	$sql = "SELECT
    		count(*) as total_records
    		FROM
    		user u
    		WHERE u.group_id=3";
    		return $sql;
    }
}