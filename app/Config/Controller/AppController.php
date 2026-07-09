<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

	public $layout = 'bootstrap';

	//Helpers para generar Bootstrap 3
	public $helpers = array(
		'Html' => array(
			'className' => 'Bootstrap3.BootstrapHtml'
		),
		'Form' => array(
			'className' => 'Bootstrap3.BootstrapForm'
		),
		'Modal' => array(
			'className' => 'Bootstrap3.BootstrapModal'
		),
		'Navbar' => array(
			'className' => 'Bootstrap3.BootstrapNavbar'
		),
		'Paginator' => array(
			'className' => 'Bootstrap3.BootstrapPaginator'
		),
		'Time'
	);

	public $components = array(
		'Paginator', 
		'Session',
		'Flash',
		'Auth' => array(
			'authenticate' => array(
				'Form' => array(
					'fields' => ['username'=>'email'],
					'passwordHasher' => 'Blowfish'
				)
			),
			'authorize' => array('Controller')
		)
	);	
	
	public function beforeFilter() {
		parent::beforeFilter();
		//pr($this->request);
		$this->Session->write('Config.language', 'esp');

		$this->Auth->loginAction = array(
										'admin'=>false,
										'controller' => 'users',
										'action' => 'login'
									);
		$this->Auth->loginRedirect = array(
										'admin'=>false,
										'controller' => 'pages',
										'action' => 'display',
										'home'
									);
		$this->Auth->logoutRedirect = array(
										'admin'=>false,
										'controller' => 'users',
										'action'=>'login'
									);

		$this->Auth->authError = __("You are not authorized to access that location.");

		// $this->Auth->flash['params']['class'] = 'alert alert-danger';
		// $this->Auth->flash['message'] = 'Debes iniciar sesión para ingresar.';  
		 //$this->Auth->allow('display');    
	}

	public function isAuthorized($user) {
		if (isset($user['id']) && $user['id']) {
			return true;
		}
		return false;
	}	

	public function beforeRender(){
		$this->set('admin', ($this->Auth->loggedIn() && $this->Auth->user('role')=='A'));
	}

	public function search() {
		$url['action'] = 'index';
		 
		foreach ($this->data as $k=>$v){
			foreach ($v as $kk=>$vv){
				$url[$k.'.'.$kk]=$vv;
			}
		}
 
		$this->redirect($url, null, true);
	}	

}
