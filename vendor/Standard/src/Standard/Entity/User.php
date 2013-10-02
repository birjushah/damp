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
 * @property string $firstname
 * @property string $lastname
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
	 * @ORM\Column(type="string");
	 */
	public $username;
	/**
	 * @ORM\Column(type="string");
	 */
	public $firstname;
	/**
	 * @ORM\Column(type="string");
	 */
	public $lastname;
	/**
     * @ORM\Column(type="string");
     */
    public $email;
	
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
	
}