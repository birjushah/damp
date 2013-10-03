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
     * @ORM\Column(type="integer");
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
	 * @ORM\Column(type="integer");
	 */
	public $latitude;
	/**
	 * @ORM\Column(type="integer");
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
	
}