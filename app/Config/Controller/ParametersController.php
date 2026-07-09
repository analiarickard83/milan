<?php
App::uses('AppController', 'Controller');
/**
 * Parameters Controller
 *
 * @property Parameter $Parameter
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class ParametersController extends AppController {



/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Parameter->recursive = -1;
		$this->set('parameters', $this->Paginator->paginate());
        $this->set('breadcrumbs', [
        	['text'=>__('Home'), 'url'=>['controller'=>'pages','action'=>'display','home']],
        	['text'=>__('Parameters list')]
        ]);			
	}	

/**
 * set method
 *
 * @throws NotFoundException
 * @param string $id
 * @param string $value
 * @return void
 */
	public function setValue($id = null, $value = null) {
		if (!$this->Parameter->exists($id)) {
			throw new NotFoundException(__('Invalid parameter'));
		}
		$this->Parameter->id = $id;
		$this->Parameter->saveField('value', $value);

		$rnd = mt_rand();

		return $this->redirect($this->referer().'?'.$rnd);
	}

	public function edit($id = null) {
		if (!$this->Parameter->exists($id)) {

			$this->Flash->error(__('The parameter is invalid.'));

			return $this->redirect(array('controller'=>'pages', 'action' => 'display', 'home'));
			
		}
		
		if ($this->request->is(array('post', 'put'))) {

			if ($this->Parameter->save($this->request->data)) {

				if($this->request->data['Parameter']['type']=='file') {

					if($this->request->data['Parameter']['image']['error']==0) {
						
						$nombreImagen = WWW_ROOT."img".DS.$this->request->data['Parameter']['image']['name'];
						$nombreTemporal = $this->request->data['Parameter']['image']['tmp_name'];
						
						$this->Parameter->updateAll(
								array('Parameter.value'=> "'".$this->request->data['Parameter']['image']['name']."'"),
								array('Parameter.id'=>$this->Parameter->id)
							);

						if(move_uploaded_file($nombreTemporal,$nombreImagen) ) {

							$this->Flash->success(__('The Parameter has been saved.'));	
							return $this->redirect(array('action' => 'index'));			
						} else {	
							$this->Flash->warning(__('The parameter has been saved, but the image could not be uploaded.'));
						}
					} else {
						$this->Flash->success(__('The Parameter has been saved.'));
						return $this->redirect(array('action' => 'index'));
					}
				} else {
					$this->Flash->success(__('The Parameter has been saved.'));
					return $this->redirect(array('action' => 'index'));
				}			
			} else {
				$this->Flash->error(__('The Parameter could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Parameter.' . $this->Parameter->primaryKey => $id));
			$this->request->data = $this->Parameter->find('first', $options);
		}

		//pr($this->request->data);
		$title = ucfirst(Inflector::humanize(h($this->request->data['Parameter']['id'])));
		$this->set('title', $title);
		$this->set('breadcrumbs', [
			['text'=>__('Home'), 'url'=>['controller'=>'pages','action'=>'display','home']],
			['text'=>__('Parameters'), 'url'=>['action'=>'index']],
			['text'=>$title]
		]);			

	}

}
