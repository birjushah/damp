<?php

namespace Standard\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Standard\StaticOptions\StaticOptions as StaticOptions;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class Entity {
	protected $_class_vars = null;
	protected $_serviceLocator;
	protected $_em = null;
	protected $_classMetaData = null;
	protected static $_entity_id_backup;
	
	
	protected $_gridOriginalColumns = false;
    
    protected $_gridReplaceColumns = false;
	
	/**
	 * Constructor
	 *
	 * @param array $options        	
	 */
	public function __construct(array $options = array()) {
	}
	
	/**
	 * @ORM\PrePersist
	 */
	final public function onPrePersist() {
		if (! $this->isCurrentNamespace ()) {
			$current_date_time = new \DateTime();
		    $current_user_id = $this->getCurrentUser()->getUserId();
		    if($this->hasVariable('created_at')){
		        $this->set('created_at', $current_date_time);
		    }
		    if($this->hasVariable('created_by')){
		    	$this->set('created_by', $current_user_id);
		    }
		    if($this->hasVariable('last_updated_at')){
		    	$this->set('last_updated_at', $current_date_time);
		    }
			if($this->hasVariable('last_updated_by')){
		    	$this->set('last_updated_by', $current_user_id);
		    }
		} 
	}
	
	/**
	 * @ORM\PostPersist
	 */
	final public function onPostPersist() {
		
	}
	
	/**
	 * @ORM\PreUpdate
	 */
	final public function onPreUpdate() {
		if (! $this->isCurrentNamespace ()) {
			$current_date_time = new \DateTime();
			$current_user_id = $this->getCurrentUser()->getUserId();
			if($this->hasVariable('last_updated_at')){
				$this->set('last_updated_at', $current_date_time);
			}
			if($this->hasVariable('last_updated_by')){
				$this->set('last_updated_by', $current_user_id);
			}
		}
	}
	
	/**
	 * @ORM\PreRemove
	 */
	final public function onPreRemove() {
		
	}
	/**
	 * @ORM\PostRemove
	 */
	final public function onPostRemove() {
		
	}
	
	/**
	 * Uses reflection to set the and get the value of private variables
	 * of the derived class
	 *
	 * @return Instance of current model
	 */
	final private function _setClassVars() {
		$this->_class_vars = array_keys ( get_object_vars ( $this ) );
		$this->_class_vars = array_diff ( $this->_class_vars, array_keys ( get_class_vars ( __CLASS__ ) ) );
		return $this;
	}
	
	/**
	 * Get the class variables of current instance not the variables inherited
	 *
	 * @return multitype:
	 */
	final private function _getClassVars() {
		if ($this->_class_vars == null) {
			$this->_setClassVars ();
		}
		return $this->_class_vars;
	}
	/**
	 * Get the value of variable asked for
	 *
	 * @param string $var        	
	 */
	public function get($var) {
		$_class_vars = $this->_getClassVars ();
		if (in_array ( $var, $_class_vars )) {
			return $this->{$var};
		}
		throw new \Exception ( "$var : No such variable declared" );
	}
	
	/**
	 * Set the value of variable
	 *
	 * @param string $var        	
	 * @param mixed $value        	
	 * @return Instance of this model
	 */
	public function set($var, $value) {
		$_class_vars = $this->_getClassVars ();
		if (in_array ( $var, $_class_vars )) {
			return $this->{$var} = $value;
		}
		throw new \Exception ( "$var : No such variable declared" );
	}
	
	/**
	 * Create setters and getters by default
	 *
	 * @param String $method        	
	 * @param Mixed $arguments        	
	 * @throws Zend_Exception
	 * @return Instance of current model
	 */
	final public function __call($method, $arguments) {
		// Automatic Set and Get Methods
		$type = substr ( $method, 0, 3 );
		$classMethod = substr ( $method, 3 );
		$variableName = $this->_createVariable ( $classMethod );
		$_class_vars = $this->_getClassVars ();
		if (in_array ( $variableName, $_class_vars )) {
			if ($type == "get") {
				return $this->{$variableName};
			} elseif ($type == "set") {
				$this->{$variableName} = isset ( $arguments [0] ) ? $arguments [0] : "";
				return $this;
			} else {
				throw new \Exception ( 'Invalid Method: ' . $method . '()' );
			}
		} else {
			throw new \Exception ( 'Invalid Property: ' . $variableName );
		}
	}
	
	/**
	 * Create variable according to the conventions
	 *
	 * @param string $method        	
	 * @return string
	 */
	private function _createVariable($method) {
		return substr ( strtolower ( preg_replace ( '/[A-Z]/', "_$0", $method ) ), 1 );
	}
	
	/**
	 * Returns the value of all declared variables in array form
	 *
	 * @return multitype:NULL
	 */
	final public function getArrayCopy() {
		$modelArray = array ();
		$_class_vars = $this->_getClassVars ();
		$_object_vars = get_object_vars ( $this );
		foreach ( $_object_vars as $key => $value ) {
			if (in_array ( $key, $_class_vars )) {
				$modelArray [$key] = $value;
			}
		}
		return $modelArray;
	}
	
	/**
	 * Set the options provided according to the variables
	 *
	 * @param array $options        	
	 * @return Instance of current model
	 */
	public function exchangeArray(array $options,$allow_null=false) {
		$_class_vars = $this->_getClassVars ();
		
		foreach ( $options as $key => $value ) {
			if (in_array ( $key, $_class_vars )) {
				if ($value != "" && $value != null || $allow_null) {
					$this->{$key} = $value;
				}
			}
		}
		return $this;
	}
	
	/**
	 * Check if the variable exists in inherited class
	 *
	 * @param string $variableName        	
	 * @return boolean
	 */
	public function hasVariable($variableName) {
		$_class_vars = $this->_getClassVars ();
		if (in_array ( $variableName, $_class_vars )) {
			return true;
		}
		return false;
	}
	
	/**
	 * Get the service locator from static options
	 */
	public function getServiceLocator() {
		if ($this->_serviceLocator == null) {
			$this->_serviceLocator = StaticOptions::getServiceLocator ();
		}
		return $this->_serviceLocator;
	}
	
	/**
	 * Get entity manager from service locator
	 */
	public function getEntityManager() {
		if ($this->_em == null) {
			$this->_em = $this->getServiceLocator ()->get ( 'Doctrine\ORM\EntityManager' );
		}
		return $this->_em;
	}
	/**
	 * Get the metadata of the current instance
	 */
	protected function getClassMetaData() {
		if ($this->_classMetaData == null)
			$this->_classMetaData = StaticOptions::getClassMetaData ( get_class ( $this ) );
		return $this->_classMetaData;
	}
	
	/**
	 * check if the instance is of the namespace of entity
	 *
	 * @return boolean
	 */
	private function isCurrentNamespace() {
		return (strpos ( get_class ( $this ), __NAMESPACE__ ) !== false);
	}
	
	/**
	 * Get current table name
	 */
	protected function getTableName() {
		return $this->getClassMetaData ()->getTableName ();
	}
	
	/**
	 * Get the database name for primary key
	 */
	private function getPrimaryKeyColumnName() {
		return $this->getClassMetaData ()->getSingleIdentifierColumnName ();
	}
	
	/**
	 * Get primary key value for current table
	 *
	 * @return number
	 */
	private function getPrimaryKey() {
		$tablePrimaryKeyColumnName = $this->getClassMetaData ()->getSingleIdentifierFieldName ();
		$primaryKey = ( int ) $this->{$tablePrimaryKeyColumnName};
		return $primaryKey;
	}
	
	private function getCurrentUser() {
		return StaticOptions::getCurrentUser ();
	}
	
	/**
     * Get the grid data according to the request parameters
     *
     * @param \Zend\Http\PhpEnvironment\Request $request            
     */
public function getGridData (\Zend\Http\PhpEnvironment\Request $request, array $options = array())
    {
        $gridInitialData = $this->getIntialGridConditions($request, $options);
        
        $total = 0;
        $totalFiltered = 0;
        
        $sql = $this->getGridSql($gridInitialData);
        
        $totalRecordSql = $this->getTotalRecordSql($gridInitialData);
        
        $filteredRecordSql = $this->getFilteredRecordSql($gridInitialData);
        
        if ($sql instanceof \Doctrine\ORM\QueryBuilder) {
            $selectQuery = $sql->getDQL();
        } else 
            if (is_string($sql)) {
                $selectQuery = $sql;
            } else {
                throw new \PDOException("Invalid SQL Query: " . $sql, 500);
            }
        
        $this->_dtResultArray = $resultArray = $this->getEntityManager()
            ->getConnection()
            ->executeQuery($selectQuery)
            ->fetchAll();
        
        $gridData = $this->filterGridResult();
        
        // Calculate total Total Results without any where clause
        $totalRecordResult = $this->getEntityManager()
            ->getConnection()
            ->executeQuery($totalRecordSql)
            ->fetchAll();
        $totalRecordResult = array_pop($totalRecordResult);
        if(!$totalRecordResult){
            $totalRecords = 0;
        } else {
            $totalRecords = $totalRecordResult['total_records'];
        }
        
        // Calculate Filtered Records with filtered where clause
        $filteredRecordResult = $this->getEntityManager()
        ->getConnection()
        ->executeQuery($filteredRecordSql)
        ->fetchAll();
        
        $filteredRecordResult = array_pop($filteredRecordResult);
        if(!$filteredRecordResult){
            $filteredRecords = 0;
        } else {
            $filteredRecords = $totalRecordResult['total_records'];
        }
        
        $finalGridData["sEcho"] = $request->getPost("sEcho", 1);
        $finalGridData["iTotalRecords"] = $totalRecords;
        $finalGridData["iTotalDisplayRecords"] = $filteredRecords;
        $finalGridData["aaData"] = $gridData;
        return $finalGridData;
    }

    public function getIntialGridConditions (\Zend\Http\PhpEnvironment\Request $request, array $options = array())
    {
        // Store the options in common variable in class
        $this->_dtOptions = $options;
        
        // Calculate Columns required
        $originalColumns = $request->getPost('sColumns', "*");
        
        // Array of original colums requested from the datatables
        $originalColumns = explode(",", $originalColumns);
        
        $this->_dtOriginalColumns = $originalColumns;
        
        // Get the columns which should not be included in sql query and should
        // hold the array result of query to be executed. Normally one column is
        // usefull for the purpose
        $queryResultColumns = isset($options["column"]) && isset($options["column"]["query_result"]) ? $options["column"]["query_result"] : array();
        
        // Filter colums to list that are only required for sql query
        $this->_dtQueryColumns = $columns = array_filter($originalColumns, function  ($value) use( $queryResultColumns)
        {
            return ($value != "" && ! in_array($value, $queryResultColumns));
        });
        
        // Applying Sorting
        $order = "";
        
        // Get the sort columns
        $iSortingCols = $request->getPost('iSortingCols');
        
        for ($i = 0; $i < intval($iSortingCols); $i ++) {
            if ($request->getPost("bSortable_" . $request->getPost('iSortCol_' . $i), false)) {
                $order .= $columns[$request->getPost('iSortCol_' . $i)] . " " . $request->getPost('sSortDir_' . $i) . ", ";
            }
        }
        // Change sOrder back to null
        $order = $order == "" ? "" : substr_replace($order, "", - 2);
        
        // Extract Searching Fields
        // Extract the columns that are requested by datatbles for searching and
        // don't have null or empty value
        $allParams = $request->getPost()->toArray();
        $searchParams = array_filter($allParams, function  ($key) use( &$allParams)
        {
            if (strpos(key($allParams), "search_") !== false && $allParams[key($allParams)] != "") {
                next($allParams);
                return true;
            } else {
                next($allParams);
                return false;
            }
        });
        
        // Check for replace Search type before setting data to data grid
        $searchTypeColumns = false;
        if (isset($options["search_type"])) {
            $searchTypeColumns = array_keys($options["search_type"]);
        }
        
        // Searching
        // Get the where clause from options
        $where = isset($options["where"]) ? $options["where"] : "";
        if (! empty($searchParams)) {
            if ($where != "") {
                $where .= " AND ";
            }
            $where .= " ( ";
            
            foreach ($searchParams as $searchColumn => $searchValue) {
                $searchColumn = substr($searchColumn, strlen("search_"));
                
                // Creating custom search for replacement properties
                if (is_array($searchTypeColumns) && in_array($searchColumn, $searchTypeColumns)) {
                    if ($options['search_type'][$searchColumn] == "=") {
                        $where .= $searchColumn . " = '" . $searchValue . "' AND ";
                    } else 
                        if ($options['search_type'][$searchColumn] == "LIKE") {
                            $where .= $searchColumn . " LIKE '%" . $searchValue . "%' AND ";
                        }
                } else {
                    $where .= $searchColumn . " LIKE '%" . $searchValue . "%' AND ";
                }
            }
            $where = substr_replace($where, "", - 4);
            $where .= ") ";
        }
        $where = $where == "" ? " 1=1 " : $where;
        
        // Get the data from database
        $count = $request->getPost("iDisplayLength", 10);
        
        // Set Offset and Limit/Count
        $offset = $request->getPost("iDisplayStart", 0);
        
        return array(
            "where" => $where,
            "count" => $count,
            "offset" => $offset,
            "order" => $order
        );
    }

    public function filterGridResult ()
    {
        $originalColumns = $this->_dtOriginalColumns ? $this->_dtOriginalColumns : array();
        
        $options = $this->_dtOptions ? $this->_dtOptions : array();
        
        $resultArray = $this->_dtResultArray ? $this->_dtResultArray : array();
        
        $gridData = array();
        if ($resultArray) {
            foreach ($resultArray as $result) {
                $record = array();
                foreach ($originalColumns as $column) {
                    if (isset($options["column"]) && isset($options["column"]["query_result"]) && in_array($column, $options["column"]["query_result"])) {
                        $record[] = $result;
                    } else 
                        if (isset($options["column"]) && isset($options["column"]["ignore"]) && in_array($column, $options["column"]["ignore"])) {
                            $record[] = "";
                        } else {
                            $record[] = $result[$column];
                        }
                }
                $gridData[] = $record;
            }
        }
        return $gridData;
    }
}