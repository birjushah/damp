<?php

namespace Insurance\Entity;

use Doctrine\ORM\Mapping as ORM;
use Standard\Entity\Entity;

/**
 * User
 * @ORM\Entity
 * @ORM\Table(name = "insurance")
 */
class Insurance extends Entity {
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	public $insurance_id;
	/**
	 * @ORM\Column(type="string");
	 */
	public $name;
	/**
	 * @ORM\Column(type="string");
	 */
	public $address;
	/**
	 * @ORM\Column(type="string");
	 */
	public $phone;
	/**
     * @ORM\Column(type="string");
     */
    public $reference;
	
	/**
	 * @ORM\Column(type="string");
	 */
	public $email;
	/**
	 * @ORM\Column(type="string");
	 */
	public $picture;
	/**
	 * @ORM\Column(type="integer");
	 */
	public $mobile;
	/**
	 * @ORM\Column(type="string");
	 */
	public $latitude;
	/**
	 * @ORM\Column(type="string");
	 */
	public $longitude;
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
		i.*
		FROM
		insurance i
		WHERE {$where} {$order} LIMIT {$offset},{$to}";
		return $sql;
		}
	
		public function getFilteredRecordSql (array $gridInitialData = array())
		{
		// Get the where condition
		$where = $gridInitialData["where"];
	
			$sql = "SELECT
			count(*) as total_records
			FROM
			user i
			WHERE {$where}";
			return $sql;
		}
	
		public function getTotalRecordSql ()
		{
			$sql = "SELECT
			count(*) as total_records
				FROM
				insurance i";
				return $sql;
		}
}