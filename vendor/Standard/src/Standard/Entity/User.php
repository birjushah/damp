<?php

namespace Standard\Entity;

use Doctrine\ORM\Mapping as ORM;
use Standard\Entity\Entity;
use Standard;

/**
 * User
 * @ORM\Entity
 * @ORM\Table(name = "user")
 * @ORM\HasLifecycleCallbacks
 *
 * @property datetime $last_updated_at
 * @property datetime $created_at
 * @property datetime $last_updated_by
 * @property datetime $created_by
 * @property string $password
 * @property string $username
 * @property int $group_id
 * @property int $user_id
 */
class User extends Entity {
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	public $user_id;
	
	/**
	 * @ORM\Column(type="integer",name="group_id")
	 * @ORM\ManyToOne(targetEntity="Standard\Entity\Group", inversedBy="group_id")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="group_id")
	 */
	public $group_id;
	
	public $group_name;
	
	/**
	 * @ORM\Column(type="string",name="username");
	 */
	public $username;
	
	/**
	 * @ORM\Column(type="string");
	 */
	public $password;
	
	/**
	 * @ORM\Column(type="datetime");
	 */
	public $created_at;
	
	/**
	 * @ORM\Column(type="integer");
	 */
	public $created_by;
	
	/**
	 * @ORM\Column(type="datetime");
	 */
	public $last_updated_at;
	
	/**
	 * @ORM\Column(type="integer");
	 */
	public $last_updated_by;
	
	public function getGridData (\Zend\Http\PhpEnvironment\Request $request, array $options = array(), $where = null, \Doctrine\ORM\QueryBuilder $select = null)
    {
        $em = $this->getEntityManager();
        
        $gridInitialData = $this->getIntialGridConditions($request, $options, $where, $select);
        
        // Get the where condition
        $where = $gridInitialData["where"];
        
        // Get the count
        $count = $gridInitialData["count"];
        
        // Get offset
        $offset = $gridInitialData["offset"];
        
        // Get the order
        $order = $gridInitialData["order"];
        
        $total = 0;
        $totalFiltered = 0;
        if ($select === null) {
        	$qb = $em->createQueryBuilder(get_class($this));
        	        	
        	$qb->from($this->getTableName(), "u");
        	
        	$qb->leftJoin("user_group","g","on","g.group_id=u.group_id");
        	
        	// Count Total Records
        	$qb->select("count(u.user_id) as total");
        	$resultArray = $em->getConnection()->executeQuery($qb->getDQL())
        						->fetchAll();
        	
        	$total = $resultArray[0]['total'];
        	
        	// Set Query Collumns
        	$qb->select("u.user_id, u.username,g.group_name");
        	
        	$qb->where($where);
        	
        	// Add all the orders to the orderby condition
        	$orders = explode(",", $order);
        	foreach ($orders as $eachOrder) {
        		$eachOrder = trim($eachOrder);
        		if ($eachOrder != null) {
        			list ($orderColumn, $orderType) = explode(" ", $eachOrder);
        			$qb->addOrderBy($orderColumn, $orderType);
        		}
        	}
        	
        	// Set the max result OR limit to the query
        	$qb->setMaxResults($count);
        	
        	// Set the offset for the result
        	$qb->setFirstResult($offset);
        	
        	$resultArray = $em->getConnection()->executeQuery($qb->getDQL())
        						->fetchAll();
        }
        
        $gridData = $this->filterGridResult($options,$resultArray);
        
        $finalGridData["sEcho"] = $request->getPost("sEcho", 1);
        $finalGridData["iTotalRecords"] = $total;
        $finalGridData["iTotalDisplayRecords"] = count($gridData);
        $finalGridData["aaData"] = $gridData;
        
        return $finalGridData;
    }
}