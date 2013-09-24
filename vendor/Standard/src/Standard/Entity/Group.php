<?php

namespace Standard\Entity;

use Doctrine\ORM\Mapping as ORM;
use Standard\Entity\Entity;

/**
 * Group
 * @ORM\Entity
 * @ORM\Table(name = "user_group")
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 *
 * @property datetime $last_updated_at
 * @property datetime $created_at
 * @property datetime $last_updated_by
 * @property datetime $created_by
 * @property string $group_name
 * @property int $group_id
 */
class Group extends Entity {
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @ORM\OneToMany(targetEntity="Standard\Entity\User", mappedBy="group_id")
	 */
	public $group_id;
	
	/**
	 * @ORM\Column(type="string",name="group_name");
	 */
	public $group_name;
	
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