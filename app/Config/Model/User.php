<?php
App::uses('AppModel', 'Model');
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');
/**
 * User Model
 *
 * @property Department $Department
 * @property Payroll $Payroll
 */
class User extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

/**
 * Calculated fields
 *
 * @var array
 */
	public $virtualFields = array(
		'fullname' => 'CONCAT(User.name, " ", User.lastname)'
	);


/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'This field cannot be left blank',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'lastname' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'This field cannot be left blank',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'password' => array(
			'lengthBetween1' => array(
				'rule' => array('lengthBetween',6,12),
				'message' => 'Between 6 to 12 characters',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'lengthBetween2' => array(
				'rule' => array('lengthBetween',6,12),
				'message' => 'Between 6 to 12 characters',
				'allowEmpty' => true,
				'required' => false,
				//'last' => false, // Stop validation after this rule
				'on' => 'update', // Limit validation to 'create' or 'update' operations
			),
		),
		'role' => array(
			'inList' => array(
				'rule' => array('inList', ['A','U']),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'email' => array(
			'email' => array(
				'rule' => array('email'),
				'message' => 'Please supply a valid email address.',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'image_file' => array(
			'rule1'=>array(
				'rule' => array('extension',array('jpeg','jpg','png','gif')),
				'required' => false,
				'allowEmpty' => true,
				'message' => 'Please supply a valid image.'
			)
		)

	);

	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Register' => array(
			'className' => 'Register',
			'foreignKey' => 'user_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);



	public function beforeSave($options = array()) {
		parent::beforeSave($options);

		if (isset($this->data[$this->alias]['password'])) {
			$passwordHasher = new BlowfishPasswordHasher();
			$this->data[$this->alias]['password'] = $passwordHasher->hash(
				$this->data[$this->alias]['password']
			);
		}

		return true;
	}

	public function beforeValidate($options = array()) {
		parent::beforeSave($options);

		if ($this->data['User']['image_file']['error'] == UPLOAD_ERR_NO_FILE) {
		    $this->validator()->remove('image_file');
		}

		return true;
	}

}
