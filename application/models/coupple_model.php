<?php

/**
 * This file contains the Coupple_model class which represents a CodeIgniter model
 */

class Coupple_model extends CI_Model {

	function __construct() {
		// Call the Model constructor
		parent::__construct();
	}

	/**
	 * @param {string} $table name of table to get data from
	 * @param {string|Array} $options Contains parameters for query query criteria
	 * @param {string|string[]} $options['select'] name of fields to return, if not set, replaced with array of all fields (e.g. array('id', 'name'))
	 * @param {Array} $options['join'] MySQL join can be set with it (e.g. array('with'=>'persons', 'on'=>'persons.id=couples.person1_id'))
	 * @param {string} $options['join']['with'] name of table that should be joined
	 * @param {string} $options['join']['on'] join criteria
	 * @param {string|Array} $options['where'] MySQL where clause can be set with it (e.g. array('first_name'=>'Bruce', 'surname'=>'Willis')
	 * @param {}
	 * @return mixed|null
	 */
	function getAll($table, $options = null) {

		if (empty($table) || gettype($table) != 'string') {
			return null;
		}

		/*
		 * Converts object keys to fit in MySQL queries as column names -> replaces capital letters with an underscore and lowercase letter
 		 * e.g. $options['relationshipStateId'] will become $options['relationship_state_id']
		 */
		$table = strToMySQL($table);
		$options = objectKeysToMySQL($options);

		$this->db->select(!empty($options['select']) ? $options['select'] :  $this->db->list_fields($table));
		unset ($options['select']);

		$this->db->from($table);

		// string query
		if (gettype($options) == 'string'){
			$this->db->query($options);
		} else if (!empty($options)) {

			if (!empty($options['join'])) {

				$this->db->join($options['join']['with'], $options['join']['on']);

				unset($options['join']);
			}
			if (!empty($options['where'])) {

				if (gettype($options['where']) === 'string') {
					$this->db->where($options['where'], null, false);
				} else if (gettype($options['where']) === 'array'){

					// if any value in array is array, it means that instead of 'where' 'where_in' is needed
					// e.g. $options['where']['id'] = array('1', '2')	->	2 items should be selected: where id is 1 OR 2
					foreach ($options['where'] as $key=>$value) {
						if (gettype($value) === 'array') {
							$this->db->where_in($key, $value);
							unset ($options['where'][$key]);	// unsets criteria - to prevent running another query with it
						}
					}
					if (!empty($options['where'])) {
						$this->db->where($options['where']);
					}
				}
				unset($options['where']);
			}

			if (!empty($options['min_date_time'])) {

				$this->db->where(array('date_time >'=>$options['min_date_time']));

				unset($options['min_date_time']);
			}

			if (!empty($options['max_date_time'])) {

				$this->db->where(array('date_time <'=>$options['min_date_time']));

				unset($options['max_date_time']);
			}

			if (!empty($options['order_by'])) {
				$this->db->order_by($options['order_by'][0], $options['order_by'][1]);
				unset($options['order_by']);
			}

			if (!empty($options['limit'])) {

				$this->db->limit($options['limit']);

				unset($options['limit']);
			}

			if (!empty($options['like']) && is_array($options['like'])) {

				if (is_array($options['like'][0])) {
					foreach ($options['like'] as $clause) {
						$this->db->like($clause[0], $clause[1], !empty($clause[2]) ? $clause[2] : 'both');
					}
				} else {
					$this->db->like($options['like'][0], $options['like'][1], !empty($options['like'][2]) ? $options['like'][2] : 'both');
				}
				unset($options['like']);
			}

			// if no query key was found, object contains only column names and values
			if (!empty($options)) {
				foreach ($options as $key=>$value) {
					if (gettype($value) === 'array') {
						$this->db->where_in($key, $value);
						unset ($options[$key]);	// unsets criteria - to prevent running another query with it
					} else if (empty($value)) {
						unset ($options[$key]);
					}
				}
				if (!empty($options)) {
					$this->db->where($options);
				}
			}
		}

		$query = $this->db->get();

		//Query the data table for every record and row (needed to encode and decode it so that php can use it as object (std Object -> Object))
		return json_decode(json_encode($query->result()), true);
	}

	/**
	 * Returns row of min/max value (or given number or smallest/largest values) of given field form table (with query options)
	 * @param string $table
	 * @param string $fieldWhereMinOrMax
	 * @param null $options
	 * @param null $orderDirection "asc" for min or "desc" for max
	 * @param int $limit
	 * @return mixed|null
	 */
	function getMinOrMax($table, $fieldWhereMinOrMax, $options=null, $orderDirection=null, $limit=null) {

		if (empty($table) || gettype($table) != 'string' || empty($fieldWhereMinOrMax) || gettype($fieldWhereMinOrMax) != 'string') {
			return null;
		}

		// Converts strings to fit in MySQL queries
		$table = strToMySQL($table);
		$fieldWhereMinOrMax = strToMySQL($fieldWhereMinOrMax);

		$options['orderBy'] = array($fieldWhereMinOrMax, !empty($orderDirection) ? $orderDirection : 'desc');	// orderDirection can be "asc" or "desc"
		$options['limit'] = !empty($limit) ? $limit : (!empty($options['limit']) ? $options['limit'] : 1);

		if ($options['limit'] === 'no') {	// we can set 'no' limit
			unset($options['limit']);
		}


		// this line makes criteria field the default return value, but we need the whole line in most of the cases
		//$options['select'] = !empty($options['select']) ? $options['select'] : $fieldWhereMinOrMax;

		// Converts object keys to fit in MySQL queries
		$options = objectKeysToMySQL($options);

		return $this->getAll($table, $options);
	}

	/**
	 * Returns row of max value (or given number or largest values) of given field form table (with query options)
	 * @param string $table
	 * @param string $fieldWhereMax
	 * @param null $options
	 * @param int null $limit
	 * @return mixed|null
	 */
	function getMax ($table, $fieldWhereMax, $options=null, $limit=null) {
		return $this->getMinOrMax($table, $fieldWhereMax, $options, 'desc', $limit);
	}

	/**
	 * Returns row of min value (or given number or smallest values) of given field form table (with query options)
	 * @param string $table
	 * @param string $fieldWhereMax
	 * @param null $options
	 * @param int null $limit
	 * @return mixed|null
	 */
	function getMin ($table, $fieldWhereMin, $options=null, $limit=null) {
		return $this->getMinOrMax($table, $fieldWhereMin, $options, 'asc', $limit);
	}

	/**
	 * Inserts new row of data to given table
	 * Does not check if row already exists, that has to be checked before calling this function! (with custom column, whichever is needed)
	 * @param string $table
	 * @param $data
	 * @return string
	 */
	function insert($table, $data) {

		if (empty($data) || empty($table)) {
			throw new Exception('Cannot update table. Incomplete data from controller.');
		}

		/*
		 * Converts object keys to fit in MySQL queries as column names -> replaces capital letters with an underscore and lowercase letter
 		 * e.g. $options['relationshipStateId'] will become $options['relationship_state_id']
		 */
		$data = objectKeysToMySQL($data);

		if (!$this->dataMatchesTable($data, $table)) {
			throw new Exception('Data fields did not match ' . $table . ' table fields.');
		}

		$this->db->insert($table, $data);
		return $this->db->insert_id();

	}

	/**
	 * Updates given element of given table with given data
	 * @param string $table name of table in database
	 * @param string|Object $criteria condition that determines row in table
	 * @param string|Object $data data that has to be updated in table
	 * @throws Exception thrown if data does not match table fields
	 */
	function update($table, $criteria, $data) {

		if (empty($data) || empty($table)) {
			throw new Exception('Cannot update table. Incomplete data from controller.');
		}

		/*
		 * Converts object keys to fit in MySQL queries as column names -> replaces capital letters with an underscore and lowercase letter
 		 * e.g. $options['relationshipStateId'] will become $options['relationship_state_id']
		 * also number strings are converted to numbers (e.g. '123' to 123)
		 */
		$data = numberStringsToNumbers(objectKeysToMySQL($data));

		if (!$this->dataMatchesTable($data, $table)) {
			throw new Exception('Data fields did not match ' . $table . ' table fields.');
		}

		if (!empty($criteria)) {

			if (gettype($criteria) === 'string') {
				$this->db->where($criteria, null, false);
			} else if (gettype($criteria) === 'array'){

				$criteria = objectKeysToMySQL($criteria);

				// if any value in array is array, it means that instead of 'where' 'where_in' is needed
				// e.g. $criteria['id'] = array('1', '2')	->	2 items should be selected: where id is 1 OR 2
				foreach ($criteria as $key=>$value) {
					if (gettype($value) === 'array') {

						$this->db->where_in($key, $value);
						unset ($criteria[$key]);	// unsets criteria - to prevent running another query with it
					}
				}
				if (!empty($criteria)) {
					$this->db->where($criteria);
				}
			}
		}

		$this->db->update($table, $data);
	}

	/**
	 * Inserts new row of data to table 'couples'
	 * @param string|Object $data
	 * @return mixed
	 * @throws Exception throws it if couple (person1_id and person2_id) already exists in database
	 */
	function insertCouple($data) {
		/*
		 * Converts object keys to fit in MySQL queries as column names -> replaces capital letters with an underscore and lowercase letter
 		 * e.g. $data['relationshipStateId'] will become $data['relationship_state_id']
		 */
		$data = objectKeysToMySQL($data);

		// checks the 2 person ids to determine whether duplicate or not
		$existenceData = array(
				'person1_id'=>$data['person1_id'],
				'person2_id'=>$data['person2_id']
		);

		if ($this->existsInDatabase('couples', $existenceData)) {
			throw new Exception('Couple already exists in database.');
		}

		return $this->insert('couples', $data);
	}

	/**
	 * Inserts new row of data to table 'events'
	 * @param string|Object $data
	 * @return mixed
	 */
	function insertEvent($data) {
		return $this->insert('events', $data);
	}

	/**
	 * Inserts new row of data to table 'persons'
	 * @param string|Object $data
	 * @return mixed
	 * @throws Exception
	 */
	function insertPerson($data) {
		/*
		 * Converts object keys to fit in MySQL queries as column names -> replaces capital letters with an underscore and lowercase letter
 		 * e.g. $data['genderId'] will become $data['gender_id']
		 */
		$data = objectKeysToMySQL($data);

		// checks email to determine whether duplicate or not
		$existenceData = array(
				'email'=>$data['email']
		);

		if ($this->existsInDatabase('persons', $existenceData)) {
			throw new Exception('Person already exists in database.');
		}

		return $this->insert('persons', $data);
	}

	/**
	 * Inserts new row of data to table 'messages'
	 * @param $data
	 * @return mixed
	 */
	function insertMessages($data) {
		$data['unread'] = !empty($data['unread']) ? $data['unread'] : 1;	// message is unread by default
		return $this->insert('messages', $data);
	}

	function searchMessages($options) {
		$limit = !empty($options['limit']) ? $options['limit'] : 'no';
		return array_reverse($this->getMax('messages', 'dateTime', $options, $limit));
	}

	/**
	 * Returns false if there is at least one key in $data object that is not a field in the given table (so data must not be inserted into table)
	 * @param $data
	 * @param string $table
	 * @return bool
	 */
	function dataMatchesTable($data, $table){
		// if there is at least one key in $data that does not match any of the fields in $table, returns false
		foreach ($data as $field=>$value) {
			if (!$this->db->field_exists($field, $table)) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Returns true if item with given options already exists in table
	 * @param $table
	 * @param $options
	 * @return bool
	 */
	function existsInDatabase($table, $options)
	{
		$result = $this->getAll($table, $options);

		if (count($result) > 0){
			return true;
		}
		return false;
	}

	/**
	 * Runs search on table 'persons' with email and password as criteria and returns result's id
	 * @param string|Object $data
	 * @return mixed|null
	 */
	function getPersonIdByEmailAndPassword($data) {

		$options['select'] = 'id';

		$options['where'] = array('email' => $data['email'], 'password' => $data['password']);

		return $this->getAll('persons', $options);
	}

	/**
	 * Runs search on table 'persons' with id as criteria and returns all fields of result
	 * @param int $personId
	 * @return mixed|null
	 */
	function getPersonById($personId) {
		if (empty($personId)) {
			return null;
		}
		$options['where'] = array('id' => $personId);

		return $this->getAll('persons', $options);
	}

	/**
	 * Runs search on table 'couples' with id as criteria and returns all fields of result
	 * @param int $coupleId
	 * @return mixed|null
	 */
	function getCoupleById($coupleId) {
		if (empty($coupleId)) {
			return null;
		}

		$options['where'] = array('id'=>$coupleId);

		return $this->getAll('couples', $options);
	}

	/**
	 * Runs search on table 'couples' with person ids as criteria and returns result's id
	 * @param int[] $personIds
	 * @return array|null
	 */
	function getCoupleIdByPersonIds($personIds) {
		if (empty($personIds)) {
			return null;
		}
		// sets query options
		$options['select'] = 'id';

		$results = [];

		// order of people is irrelevant, so runs query for both people as person1 and person2 in database
		foreach ($personIds as $firstIndex => $firstPersonId) {
			foreach ($personIds as $secondIndex => $secondPersonId) {
				if ($firstIndex != $secondIndex) {
					$options['where'] = array('couples.person1_id' => $firstPersonId,
											  'couples.person2_id' => $secondPersonId
					);
					$results = array_merge($results, $this->model->getAll('couples', $options));
				}
			}
		}
		return $results;
	}

	/**
	 * Runs search on table 'couples' with person ids as criteria and returns all fields of result
	 * @param int[] $personIds
	 * @return array|null
	 */
	function getCoupleByPersonIds($personIds) {
		if (empty($personIds)) {
			return null;
		}

		$results = [];

		// if only 1 known person id exists in array
		if (count($personIds) === 1) {
			foreach($personIds as $personId) {
				// checks for person as person1
				$options['where'] = array('couples.person1_id' => $personId);
				$results = $this->model->getAll('couples', $options);

				// checks for person as person2
				$options['where'] = array('couples.person2_id' => $personId);
				$results = array_merge($results, $this->model->getAll('couples', $options));
			}
		} else {

			// order of people is irrelevant, so runs query for both people as person1 and person2 in database
			foreach ($personIds as $firstIndex => $firstPersonId) {
				foreach ($personIds as $secondIndex => $secondPersonId) {
					if ($firstIndex != $secondIndex) {
						$options['where'] = array('couples.person1_id' => $firstPersonId,
												  'couples.person2_id' => $secondPersonId
						);
						$results = array_merge($results, $this->model->getAll('couples', $options));
					}
				}
			}
		}
		return $results;
	}

	/**
	 * Runs search on table 'genders' with no criteria - returns array of all items
	 * @return mixed
	 */
	function getGenders(){
		// returns all genders (id and name)
		return $this->getGenderById(null);
	}

	/**
	 * Runs search on table 'genders' with id as criteria and returns all fields of result
	 * @param int $genderId
	 * @return mixed
	 */
	function getGenderById($genderId) {
		if (empty($genderId)) {
			return null;
		}

		$options['where'] = array('id' => $genderId);

		return $this->model->getAll('genders', $options);
	}

	/**
	 * Runs search on table 'relationship_states' with no criteria - returns array of all items
	 * @return mixed
	 */
	function getRelationshipStates(){
		// returns all relationship states (id and name)
		return $this->getRelationshipStateById(null);
	}

	/**
	 * @param null $relationshipStateId
	 * @return mixed
	 */
	function getRelationshipStateById($relationshipStateId) {
		if (empty($relationshipStateId)) {
			return null;
		}

		$options['where'] = array('id' => $relationshipStateId);

		return $this->model->getAll('relationship_states', $options);
	}

}