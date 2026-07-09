<?php
App::uses('AppController', 'Controller');
/**
 * Users Controller
 *
 * @property User $User
 */
class UsersController extends AppController {

	public $uses = ['User', 'Parameter'];

	public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('login', 'logout', 'recover');
        //$this->Auth->allow();
    }

	public function isAuthorized($user) {
		return $user['role']=='A';
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {

		$admin = $this->Auth->user('role')=='A';

		$this->User->recursive = 0;
		$users = $this->Paginator->paginate();
        $breadcrumbs = [
        	['text'=>__('Home'), 'url'=>['controller'=>'pages','action'=>'display','home']],
        	['text'=>__('Users list')]
        ];

        $this->set(compact('users', 'breadcrumbs'));
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}

		$admin = $this->Auth->user('role')=='A';

		$options = [
			'conditions' => ['User.' . $this->User->primaryKey => $id],
		];
		$user = $this->User->find('first', $options);

		$breadcrumbs = [
			['text'=>__('Home'), 'url'=>['controller'=>'pages','action'=>'display','home']],
			['text'=>__('Users list'), 'url'=>['action'=>'index']],
			['text'=>$user['User']['fullname']]
		];

		$this->set(compact('user', 'breadcrumbs'));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {

		$admin = $this->Auth->user('role')=='A';

		if ($this->request->is('post')) {
			$this->User->create();

			if ($this->User->save($this->request->data)) {

				$imgOk = true;

				if($this->request->data['User']['image_file']['error']==0){
					
					$nombreImagen = WWW_ROOT."img".DS."users".DS.$this->request->data['User']['image_file']['name'];
					$nombreTemporal = $this->request->data['User']['image_file']['tmp_name'];
					
					$this->User->updateAll(
							array('User.image'=> "'".$this->request->data['User']['image_file']['name']."'"),
							array('User.id'=>$this->User->id)
						);

					if(!move_uploaded_file($nombreTemporal,$nombreImagen) )
						$imgOk = false;
				} else 
					$imgOk = false;


				if($imgOk) 
					$this->Flash->success(__('The user has been saved.'));
				else
					$this->Flash->success(__('The user has been saved, but the image could not be uploaded.'));

				return $this->redirect(array('action' => 'index'));
			} else {

				$this->Flash->error(__('The user could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->User->create();
		}

		$breadcrumbs = [
			['text'=>__('Home'), 'url'=>['controller'=>'pages','action'=>'display','home']],
			['text'=>__('Users list'), 'url'=>['action'=>'index']],
			['text'=>__('New User')]
		];		

		$this->set(compact('breadcrumbs'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}

		$admin = $this->Auth->user('role')=='A';

		if ($this->request->is(array('post', 'put'))) {

			if(array_key_exists('password', $this->request->data['User']) && empty($this->request->data['User']['password']))
				unset($this->request->data['User']['password']);

			if ($this->User->save($this->request->data)) {

				$imgOk = true;

				if($this->request->data['User']['image_file']['error']==0){
					
					$nombreImagen = WWW_ROOT."img".DS."users".DS.$this->request->data['User']['image_file']['name'];
					$nombreTemporal = $this->request->data['User']['image_file']['tmp_name'];
					
					$this->User->updateAll(
							array('User.image'=> "'".$this->request->data['User']['image_file']['name']."'"),
							array('User.id'=>$this->User->id)
						);

					if(!move_uploaded_file($nombreTemporal,$nombreImagen) )
						$imgOk = false;
				} else 
					$imgOk = false;

				if($imgOk)
					$this->Flash->success(__('The user has been saved.'));
				else
					$this->Flash->success(__('The user has been saved, but the image could not be uploaded.'));

				if($this->User->id == $this->Session->read('Auth.User.id')){

					$this->Session->write('Auth', $this->User->read(null, $this->Auth->User('id')));
				}


				return $this->redirect(array('action' => 'index'));

			} else {
				$this->Flash->error(__('The user could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
			$this->request->data = $this->User->find('first', $options);
			unset($this->request->data['User']['password']);
		}
		$breadcrumbs = [
			['text'=>__('Home'), 'url'=>['controller'=>'pages','action'=>'display','home']],
			['text'=>__('Users list'), 'url'=>['action'=>'index']],
			['text'=>$this->request->data['User']['fullname']]
		];

		$this->set(compact('breadcrumbs'));
	}

/**
 * recover method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function recover($token = null) {
		
		if (!is_null($token)) {

			if($user = $this->User->findByToken($token, array('id', 'token', 'password', 'name', 'lastname', 'fullname', 'email'))) {
			
				$newPasword = $this->User->passwordCreator();
				$user['User']['password'] = $newPasword;
				$user['User']['token'] = '';

				if ($this->User->save($user)) {

					$to = array($user['User']['email']=>$user['User']['fullname']);
					$from = array(Configure::read('Company.email') => Configure::read('Company.name'));
				
					App::uses('CakeEmail', 'Network/Email');	
					
					$Email = new CakeEmail();
					//$Email->transport('Debug');
					$Email->from($from);
					$Email->to($to);
					$Email->bcc('khbohm@gmail.com');

					$Email->subject('Recupero de Contraseña');
			    	$Email->emailFormat('html');

			    	$msg = '<p>'.__("This email is automatically generated, please, do not respond.").'</p><p></p>';
					$msg .= '<h2>Faena Movil</h2>';
					$msg .= '<h3>Recupero de Contraseña.</h3><br><br>';
			    	$msg .= '<p>Por motivos de seguridad, nosotros no conocemos su contraseña, por lo que le hemos generado automaticamente una nueva contraseña aleatoria para que pueda ingresar. Podrá cambiarla desde su panel.</p>';
			    	$msg .= '<p>Estos son los nuevos datos para ingresar.</p>';
			    	$msg .= '<p></p><p></p>';
			    	$msg .= '<p>Su usuario es:' . $user['User']['email']. '</p>';
			    	$msg .= '<p>Su contraseña es:<strong>' . $newPasword. '</strong></p>';
					$Email->send($msg);		

					$this->Flash->success(__('Thanks you for recover your password. We have sent you an email with the access data.'));
					
					return $this->redirect(array('controller'=>'users', 'action' => 'login'));

				} else {

					pr($this->User->validationErrors);die;

					$this->Flash->error(__('The user could not be saved. Please, try again.'));	
				}
			}
		} elseif ($this->request->is('post')) {
		


			if($user = $this->User->findByEmail($this->request->data['User']['email'])) {
//pr($user);die;			
				$newToken = $this->User->passwordCreator(50,true,true,true,false);

				while($this->User->findByToken($newToken)) {
					$newToken = $this->User->passwordCreator(50,true,true,true,false);
				}

				$this->request->data['User']['token'] = $newToken;
				$this->request->data['User']['id'] = $user['User']['id'];


			
				if ($this->User->save($this->request->data, false)) {

					$url = Router::url(array('controller'=>'users', 'action'=>'recover', $newToken), true);
				
					$to = array($user['User']['email']=>$user['User']['fullname']);
					$from = array(Configure::read('Company.email') => Configure::read('Company.name'));
				
					App::uses('CakeEmail', 'Network/Email');	
					
					$Email = new CakeEmail();
					//$Email->transport('Debug');
					$Email->from($from);
					$Email->to($to);
					$Email->bcc('khbohm@gmail.com');

					$Email->subject('Recupero de Contraseña');
			    	$Email->emailFormat('html');

			    	$msg = '<p>'.__("This email is automatically generated, please, do not respond.").'</p><p></p>';
					$msg .= '<h2>Faena Movil</h2>';
					$msg .= '<h3>Recupero de Contraseña.</h3><br><br>';
			    	$msg .= '<p>Ha solicitado recuperar la contraseña de su usuario.</p>';
			    	$msg .= '<p>Para recuperar la contraseña, por favor, siga el enlace siguiente.</p>';
			    	$msg .= '<p><a href="'.$url.'">RECUPERAR CONTRASEÑA</a></p><p></p>';
			    	$msg .= '<p>O copie la siguiente dirección y péguela en su navegador:</p>';
			    	$msg .= '<p><strong>' . $url. '</strong></p>';
					$response = $Email->send($msg);		

					//pr( $response);die;
				
					$this->Flash->success(__('Thanks you for recover your password. We have sent you an email with the access data.'));
					
					return $this->redirect(array('controller'=>'users', 'action' => 'login'));
				} else {
					$this->Flash->error(__('The user could not be saved. Please, try again.'));				
				}
			}
		} 
	}



/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->User->delete()) {
			$this->Flash->success(__('The user has been deleted.'));
		} else {
			$this->Flash->error(__('The user could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

/**
 * login method
 *
 * @return void
 */
	public function login() {
	    if ($this->request->is('post')) {
	        // Important: Use login() without arguments! See warning below.

	        if ($this->Auth->login()) {
	            return $this->redirect($this->Auth->redirectUrl());
	            // Prior to 2.3 use
	            // `return $this->redirect($this->Auth->redirect());`
	        }
	        $this->Session->setFlash(
	            __('Email or password is incorrect'),
	            'default',
	            array(),
	            'auth'
	        );
	    }
	   // $this->set('no_navbar',true);
	}	

/**
 * logout method
 *
 * @return void
 */
	public function logout() {
	    return $this->redirect($this->Auth->logout());
	}	
}
