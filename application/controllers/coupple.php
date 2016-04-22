<?php

/**
 * This file contains the Coupple class which represents a CodeIgniter controller
 * This is the default controller of the project, so function $this->index() is called when user visits website
 */

include('common.php');

class Coupple extends CI_Controller {

	public function __construct() {
		parent::__construct();

		if (!empty($this->Coupple_model)) {
			// from now on we can use property 'model', don't have to know the name of the model
			$this->model = $this->Coupple_model;
		}
	}

	/**
	 * This is the default function that runs when user visits website - loads 'login.php'
	 */
	public function index() {
		$this->login();
	}

	/**
	 * Loads 'login.php'
	 */
	public function login() {
		$this->load->view('login');
	}

	/**
	 * Loads 'signup.php' with some information that is needed for registration
	 */
	public function signup() {
		$data['genders'] = $this->model->getAll('genders');
		$data['relationshipStates'] = $this->model->getAll('relationshipStates');

		$this->load->view('signup', $data);
	}

	/**
	 * Loads 'main.php' with information about couple in session
	 */
	public function main() {

		$data['couple'] = $this->session->userdata('couple');

		if (!empty($data['couple'])) {        // if a couple's data was in the session

			// converts object keys to Javascript style
			$data['couple'] = objectKeysToJavascript($data['couple']);

			// loads main page
			$this->load->view('main', $data);
		} else {
			show_error('An error has occurred :(');
			return;
		}
	}

	function events() {
		$data['couple'] = $this->session->userdata('couple');

		if (!empty($data['couple'])) {        // if a couple's data was in the session

			// converts object keys to Javascript style
			$data['couple'] = objectKeysToJavascript($data['couple']);

			// loads main page
			$this->load->view('events', $data);
		} else {
			show_error('An error has occurred :(');
			return;
		}
	}

	function memories() {
		$data['couple'] = $this->session->userdata('couple');

		if (!empty($data['couple'])) {        // if a couple's data was in the session

			// converts object keys to Javascript style
			$data['couple'] = objectKeysToJavascript($data['couple']);

			// loads main page
			$this->load->view('memories', $data);
		} else {
			show_error('An error has occurred :(');
			return;
		}
	}

	function messages() {
		$data['couple'] = $this->session->userdata('couple');

		if (!empty($data['couple'])) {        // if a couple's data was in the session

			// converts object keys to Javascript style
			$data['couple'] = objectKeysToJavascript($data['couple']);

			if ($data['couple']['accessId'] != 'couple') {	// checks if logged in as couple or as person
				// loads main page
				$this->load->view('messages', $data);
			} else {	// logged in as couple -> will not open messaging
				$this->load->view('messages', $data);
			}
		} else {
			show_error('An error has occurred :(');
			return;
		}
	}

	function aboutUs() {
		$data['couple'] = $this->session->userdata('couple');

		if (!empty($data['couple'])) {        // if a couple's data was in the session

			$data['genders'] = $this->model->getAll('genders');
			$data['relationshipStates'] = $this->model->getAll('relationshipStates');

			// converts object keys to Javascript style
			$data['couple'] = objectKeysToJavascript($data['couple']);
			$data['genders'] = objectKeysToJavascript($data['genders']);
			$data['relationshipStates'] = objectKeysToJavascript($data['relationshipStates']);

			// loads main page
			$this->load->view('aboutUs', $data);
		} else {
			show_error('An error has occurred :(');
			return;
		}
	}

	function photos() {
		$data['couple'] = $this->session->userdata('couple');

		if (!empty($data['couple'])) {        // if a couple's data was in the session

			// converts object keys to Javascript style
			$data['couple'] = objectKeysToJavascript($data['couple']);

			// loads main page
			$this->load->view('photos', $data);
		} else {
			show_error('An error has occurred :(');
			return;
		}
	}

	/**
	 * Searches database for couple (if not given), filters are name and password of the members of the relationship
	 *
	 * @param null $couple
	 */
	public function ajaxLogInCouple($couple = null) {

		if (!empty($couple)) {    // couple was given (calling this function when couple just signed up)
			// need to match search return value: array with offset 0, keys are converted to Javasript style
			$results = array(objectKeysToJavascript($couple));
		} else {    // regular login
			$persons = $this->input->post('persons');

			$personIds = [];

			foreach ($persons as $index => $person) {

				if (!empty($person['email']) && !empty($person['password'])) {
					// returns id of person (or ids of people) with given name and password
					$id = $this->model->getPersonIdByEmailAndPassword(array('email'    => $person['email'],
																			'password' => $person['password']));

					// if there are more than 1 or no matches for a person, returns null as result for couple
					if (!is_array($id) || count($id) > 1 || count($id) == 0) {
						$this->output->set_output(json_encode(array('success' => true,
																	'result'  => null,
						)));
						return;
					}
					// sets person id
					$persons[$index]['id'] = $id[0]['id'];
					$personIds[$index] = $id[0]['id'];
				}
			}

			if (empty($personIds)) {
				$this->output->set_output(json_encode(array('success' => true,
															'result'  => null,
				)));
				return;
			}

			$results = objectKeysToJavascript($this->model->getCoupleByPersonIds($personIds));
		}

		// if only 1 couple matched criteria, updates last login time and loads couple data into session data
		if (is_array($results) && count($results) == 1) {
			$results[0]['lastLoginDatetime'] = $this->input->post('time');
			$coupleId = array('id'=> $results[0]['id']);
			$loginTime = array('lastLoginDatetime' => $results[0]['lastLoginDatetime']);
			$this->model->update('couples', $coupleId, $loginTime);


			// sets 'person1' and 'person2' attribute of couple data
			for ($personId = 1; $personId <= 2; $personId++) {
				// gets person by id from session
				$person = objectKeysToJavascript($this->model->getPersonById($results[0]['person' . $personId . 'Id']));
				if (!empty($person) && is_array($person) && count($person) === 1) {
					// adds person to couple object
					$results[0]['persons'][$personId] = $person[0];
				} else {
					$this->output->set_output(json_encode(array('success' => false,
																'message' => 'Person not found in database',
					)));
					return;
				}
			}

			// gets relationship state
			$relationshipState = $this->model->getRelationshipStateById($results[0]['relationshipStateId']);

			if (!empty($relationshipState) && is_array($relationshipState) && count($relationshipState) === 1) {
				$results[0]['relationshipState'] = $relationshipState[0];
			} else {
				$this->output->set_output(json_encode(array('success' => false,
															'message' => 'Cannot retrieve relationship states from database',
				)));
				return;
			}

			$results[0]['accessId'] = !empty($personIds) ? determineAccessId($personIds) :
					'couple';        // at first log in (after registering) $personIds is empty

			$this->session->set_userdata('couple', $results[0]);
		}

		// Returns results
		$this->output->set_output(json_encode(array('success' => true,
													'result'  => $results,
		)));
	}

	/**
	 * Creates new element in a table in database -> post data must contain every data for the insertion!
	 * Calls insert function of given type (couple, event, etc.) in model
	 *
	 * @param string $table determines which table we want the data to add -> name of insert function to call
	 */
	public function ajaxCreate($table) {
		$data = $this->input->post('data');

		if (empty($data) || empty($table)) {
			// Returns error message
			$this->output->set_output(json_encode(array('success' => false,
														'message' => "Missing information",
			)));
			return;
		}

		// name of insert functions in model: 'insert' + table name
		// e.g. 'insertCouple'
		$table = 'insert' . ucfirst($table);        // ucfirst: makes first letter of string uppercase
		$data = objectKeysToMySQL($data);

		try {
			$resultId = $this->model->$table($data);
		} catch (Exception $e) {
			// Returns error message
			$this->output->set_output(json_encode(array('success' => false,
														'message' => $e->getMessage()
			)));
			return;
		}

		$this->output->set_output(json_encode(array('success'  => true,
													'result' => array(0=>array('id'=>$resultId))		// so that array length is 1
		)));
	}

	public function ajaxSearch($table) {
		$data = $this->input->post('data');

		if (empty($data) || empty($table)) {
			// Returns error message
			$this->output->set_output(json_encode(array('success' => false,
														'message' => "Missing information",
			)));
			return;
		}

		// name of search functions in model: 'search' + type
		// e.g. 'searchMessage'
		$table = 'search' . ucfirst($table);        // ucfirst: makes first letter of string uppercase
		$data = objectKeysToMySQL($data);

		try {
			$results = objectKeysToJavascript($this->model->$table($data));
		} catch (Exception $e) {
			// Returns error message
			$this->output->set_output(json_encode(array('success' => false,
														'message' => $e->getMessage()
			)));
			return;
		}

		if (is_array($results)) {
			$this->output->set_output(json_encode(array('success' => true,
														'result'  => $results,
			)));
		}

	}

	public function ajaxUpdate($table) {
		$data = $this->input->post('data');
		$criteria = $this->input->post('criteria');

		$this->model->update($table, $criteria, $data);
	}

	/**
	 * Registers new couple
	 * Creates 2 new items in table 'persons'
	 * Creates 1 new item in table 'couples'
	 * Creates 1 new item in table 'events' (date of beginning of their relationship)
	 */
	public function ajaxRegisterCouple() {
		$data = $this->input->post('data');
		if (empty($data['couple']) || empty($data['persons']) || empty($data['persons']['person1'])
				|| empty($data['persons']['person1'])
		) {
			// Returns error message
			$this->output->set_output(json_encode(array('success' => false,
														'message' => "Missing information",
			)));
			return;
		}
		$time = $this->input->post('time');

		$data['persons']['person1']['last_login_datetime'] =
		$data['persons']['person1']['last_logout_datetime'] = $time;
		$data['persons']['person2']['last_login_datetime'] =
		$data['persons']['person2']['last_logout_datetime'] = $time;

		$data['persons']['person1']['genderId'] = 1;
		$data['persons']['person1']['email'] = 'email11s1111';
		$data['persons']['person1']['birthday'] = '1995-03-27';

		$data['persons']['person2']['genderId'] = 2;
		$data['persons']['person2']['email'] = 'email221s11';
		$data['persons']['person2']['birthday'] = '1993-03-27';

		$data['couple']['lastLoginDatetime'] =
		$data['couple']['lastLogoutDatetime'] = $data['couple']['relationshipBeginningDate'] = $time;

		$data['couple']['relationshipStateId'] = 1;

		$eventData['shortDescription'] = $data['couple']['relationshipBeginningShortDescription'];
		unset($data['couple']['relationshipBeginningShortDescription']);

		$eventData['sessionId'] = $this->model->getMax('events', 'sessionId', array('select' => 'sessionId'));
		// result should be an array
		if (is_array($eventData['sessionId'])) {
			switch (count($eventData['sessionId'])) {
				case 0:        // no matches were found - there isn't any events in the events table - new one's id session id should be 1
					$eventData['sessionId'] = 1;
					break;
				case 1:        // found max - new event's id should be current max + 1
					$eventData['sessionId'] = $eventData['sessionId'][0]['session_id'] + 1;
					break;
				default:    // there should be only 1 returned record - more records means error
					$this->output->set_output(json_encode(array('success' => false,
																'message' => 'Error while searching in events table'
					)));
					return;
			}
		} else {
			$this->output->set_output(json_encode(array('success' => false,
														'message' => 'Error while searching in events table'
			)));
			return;
		}

		try {        // if person is duplicate or data does not match database, exception is thrown

			// inserts 2 items to table 'persons' - returns new ids
			$data['couple']['person1_id'] = $this->model->insertPerson($data['persons']['person1']);
			$data['couple']['person2_id'] = $this->model->insertPerson($data['persons']['person2']);

			// inserts 1 item to table 'couples' - returns new id
			$data['couple']['id'] = $this->model->insertCouple($data['couple']);

			$eventData['name'] = 'We got together :)';
			$eventData['coupleId'] = $data['couple']['id'];
			$eventData['dateTime'] = $data['couple']['relationshipBeginningDate'];
			$eventData['visibilityId'] = 1;        // only couple can see event (not public)
			$eventData['repetitionId'] = 5;        // 'every year'

			// inserts 1 item to table 'events' (date of beginning of their relationship)
			$this->model->insertEvent($eventData);
		} catch (Exception $e) {
			// Returns error message
			$this->output->set_output(json_encode(array('success' => false,
														'message' => $e->getMessage()
			)));
			return;
		}

		// if success: logs couple in
		$this->ajaxLogInCouple($data['couple']);
	}
}
