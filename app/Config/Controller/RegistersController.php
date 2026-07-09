<?php
App::uses('AppController', 'Controller');
/**
 * Registers Controller
 *
 * @property Register $Register
 * @property PaginatorComponent $Paginator
 * @property FlashComponent $Flash
 * @property SessionComponent $Session
 */
class RegistersController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Mpdf');		

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Register->recursive = 0;
		$conditions = array();

		if(isset($this->passedArgs['Busca.dde']) && $this->passedArgs['Busca.dde']) {
			$dde = $this->Register->dateFormatBeforeSave($this->passedArgs['Busca.dde']);
			$conditions['Register.date >='] =  $dde;

            $this->request->data['Busca']['dde'] = $this->passedArgs['Busca.dde'];
        }

		if(isset($this->passedArgs['Busca.hta']) && $this->passedArgs['Busca.hta']) {
			$hta = $this->Register->dateFormatBeforeSave($this->passedArgs['Busca.hta']);
			$conditions['Register.date <='] =  $hta;

            $this->request->data['Busca']['hta'] = $this->passedArgs['Busca.hta'];
        }

		if(isset($this->passedArgs['Busca.pro']) && $this->passedArgs['Busca.pro']) {
			$conditions[] = [
				'Register.province LIKE' => '%'.$this->passedArgs['Busca.pro'].'%'
			];

			$empty = false;

            $this->request->data['Busca']['pro'] = $this->passedArgs['Busca.pro'];
        }

		if(isset($this->passedArgs['Busca.loc']) && $this->passedArgs['Busca.loc']) {
			$conditions[] = [
				'Register.locality LIKE' => '%'.$this->passedArgs['Busca.loc'].'%'
			];

			$empty = false;

            $this->request->data['Busca']['loc'] = $this->passedArgs['Busca.loc'];
        }

		if(isset($this->passedArgs['Busca.esp']) && $this->passedArgs['Busca.esp']) {
			$conditions[] = [
				'Register.species' => $this->passedArgs['Busca.esp']
			];

			$empty = false;

            $this->request->data['Busca']['esp'] = $this->passedArgs['Busca.esp'];
        }

			$breadcrumbs = [
				['text'=>__('Home'), 'url'=>['controller'=>'pages','action'=>'display','home']],
				['text'=>__('Registers list')]
			];	    

			$this->set(compact('breadcrumbs'));		        


		// if($this->Auth->user('role')=='U')
		// 	$conditions = array('Register.user_id'=>$this->Auth->user('id'));
		$this->set('registers', $this->Paginator->paginate('Register',$conditions));
	}


/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view_($id = null) {
		if (!$this->Register->exists($id)) {
			throw new NotFoundException(__('Invalid register'));
		}
		$options = array('conditions' => array('Register.' . $this->Register->primaryKey => $id));
		$this->set('register', $this->Register->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Register->create();
			$this->request->data['Register']['user_id'] = $this->Auth->user('id');
			if ($this->Register->save($this->request->data)) {
				$this->Flash->success(__('The register has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The register could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = array('Register'=>array('date'=>date('d/m/Y')));	
		}


		$breadcrumbs = [
			['text'=>__('Home'), 'url'=>['controller'=>'pages','action'=>'display','home']],
			['text'=>__('Registers list'), 'url'=>['action'=>'index']],
			['text'=>__('New Register')]
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
		if (!$this->Register->exists($id)) {
			throw new NotFoundException(__('Invalid register'));
		}
		if ($this->request->is(array('post', 'put'))) {
			$this->request->data['Register']['user_id'] = $this->Auth->user('id');
			if ($this->Register->save($this->request->data)) {
				$this->Flash->success(__('The register has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The register could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Register.' . $this->Register->primaryKey => $id));
			$this->request->data = $this->Register->find('first', $options);
		}
		
		$breadcrumbs = [
			['text'=>__('Home'), 'url'=>['controller'=>'pages','action'=>'display','home']],
			['text'=>__('Registers list'), 'url'=>['action'=>'index']],
			['text'=>__('Edit Register nº %s', $id)]
		];		

		$this->set(compact('breadcrumbs'));		
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Register->exists($id)) {
			throw new NotFoundException(__('Invalid register'));
		}

		$options = array('conditions' => array('Register.' . $this->Register->primaryKey => $id));
		$this->request->data = $this->Register->find('first', $options);

		$title = __('Register nº %s', $id);

		unset($this->request->data['Register']['id']);
		
		$breadcrumbs = [
			['text'=>__('Home'), 'url'=>['controller'=>'pages','action'=>'display','home']],
			['text'=>__('Registers list'), 'url'=>['action'=>'index']],
			['text'=>$title]
		];		

		$this->set(compact('breadcrumbs', 'title'));		
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Register->id = $id;
		if (!$this->Register->exists()) {
			throw new NotFoundException(__('Invalid register'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Register->delete()) {
			$this->Flash->success(__('The register has been deleted.'));
		} else {
			$this->Flash->error(__('The register could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

/**
 * from_excel method
 *
 * @return void
 */
	public function from_excel() {

		$cols = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA');

		$fields = array(
			'plant_nro' => __('Plant Nro'),
			'oncca_nro' => __('Oncca Nro'),
			'troop_nro' => __('Troop Nro'),
			'dopro_nro' => __('Dopro Nro'),
			'dte_nro' => __('Dte Nro'),
			'romaneo_oncca_nro' => __('Romaneo Oncca Nro'),
			'owner' => __('Owner'),
			'cuit_nro' => __('Cuit Nro'),
			'renspa_nro' => __('Renspa Nro'),
			'plant' => __('Plant'),
			'department' => __('Department'),
			'locality' => __('Locality'),
			'province' => __('Province'),
			'consignee' => __('Consignee'),
			'consignee_oncca_nro' => __('Consignee Oncca Nro'),
			'species' => __('Species'),
			'enter_kg' => __('Enter Kg'),
			'category' => __('Category'),
			'confiscated_organs' => __('Confiscated Organs'),
			'confiscated_why' => __('Confiscated Why'),
			'organs_names' => __('Organs Names'),
			'organs_patology' => __('Organs Patology'),
			'end_kg' => __('End Kg'),
			'meat_target' => __('Meat Target'),
			'veterinary' => __('Veterinary'),
			'notes' => __('Notes'),
		);

		if ($this->request->is('post')) {

			$error = '';

			if($this->request->data['Register']['excel']['error']==0){
					
				$nombre = WWW_ROOT."files".DS.$this->request->data['Register']['excel']['name'];
				$nombreTemporal = $this->request->data['Register']['excel']['tmp_name'];

				if(move_uploaded_file($nombreTemporal,$nombre) ) {
					App::import('Vendor', 'Classes/PHPExcel/IOFactory');

					$objPHPExcel = PHPExcel_IOFactory::load($nombre);

					//pr($objPHPExcel);die;

					$rows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
					$regs = 0;

					//pr($rows);die;

					$row = $this->request->data['Register']['first_row'] * 1;


					for($row = $this->request->data['Register']['first_row']; $row <= $rows;$row++) {

						$ctl = '';
						$data = array('Register'=>array());

						foreach ($cols as $col) {
							if($this->request->data['Register'][$col]!='') {
								$data['Register'][$this->request->data['Register'][$col]] = $objPHPExcel->setActiveSheetIndex(0)->getCell($col.$row)->getValue();
								$ctl .= trim($data['Register'][$this->request->data['Register'][$col]]);
							}
						}

						$data['Register']['user_id'] = $this->Auth->user('id');
						$data['Register']['date'] = date('Y-m-d');

						if($ctl!==''){
							$this->Register->create();
							$this->Register->set($data);
	
							if($this->Register->validates()) {
								$this->Register->save();
								$regs++;
							} else {
								if ($error!=='') $error .= ', ';
								$error .= $row;
							}
						}
					};

					if ($error!=='') {
						$this->Flash->success("Se han importado $regs sin errores.");		
					} else {
						$this->Flash->warning("Se han importado $regs sin errores.");
					}
				} else {
					$this->Flash->error("Ha habido un problema con la grabación del archivo. No se ha podido importar.");
				}
					
			} else {


				
				switch ($this->request->data['Register']['excel']['error']*1) {
					case UPLOAD_ERR_NO_FILE:
						$this->Flash->error('No se ha seleccionado archivo.');
						break;
					case UPLOAD_ERR_INI_SIZE:
					case UPLOAD_ERR_FORM_SIZE:
						$this->Flash->error('No se ha podido subir el archivo. El archivo es muy grande.');
						break;
					default:
						$this->Flash->error('No se ha podido subir el archivo. Error desconocido.');
    			}
			}
			
			return $this->redirect(array('action'=>'index'));

		} //else {
			
			$c=0;
			$this->request->data['Register'] = array();
			foreach ($fields as $key => $value) {
				$this->request->data['Register'][$cols[$c++]] = $key;
			}

			$breadcrumbs = [
				['text'=>__('Home'), 'url'=>['controller'=>'pages','action'=>'display','home']],
				['text'=>__('Registers list'), 'url'=>['action'=>'index']],
				['text'=>'Import Excel']
			];		

			$this->set(compact('breadcrumbs', 'fields', 'cols'));

		//};
		// $this->Flash->warning(h("Aún no implementado."));

		// return $this->redirect(array('action' => 'index')); 


	}

/**
 * to_excel method
 *
 * @return void
 */
	public function to_excel() {

		if ($this->request->is('post')) {
			$this->layout='ajax';
			$this->Register->recursive = 0;
			$conditions = array();
			$filter = '';

			if($this->Auth->user('role')=='U') {
				$conditions['Register.user_id'] = $this->Auth->user('id');
				$filter = 'Usuario: '.$this->Auth->user('lastname').', '.$this->Auth->user('name');
			}

			if ($this->request->data['Register']['from']){
				$conditions['Register.date >='] = $this->Register->dateFormatBeforeSave($this->request->data['Register']['from']);
				$filter .= '/ Desde: '.$this->request->data['Register']['from'];
			}

			if ($this->request->data['Register']['to']) {
				$conditions['Register.date <='] = $this->Register->dateFormatBeforeSave($this->request->data['Register']['to']);
				$filter .= '/ Hasta: '.$this->request->data['Register']['to'];
			}

			if ($this->request->data['Register']['province']) {
				$conditions['Register.province LIKE'] = '%'.$this->request->data['Register']['province'].'%';
				$filter .= '/ Prov.: '.$this->request->data['Register']['province'];
			}

			if ($this->request->data['Register']['locality']) {
				$conditions['Register.locality LIKE'] = '%'.$this->request->data['Register']['locality'].'%';
				$filter .= '/ Loc.: '.$this->request->data['Register']['locality'];
			}

			if ($this->request->data['Register']['species']) {
				$conditions['Register.species'] = $this->request->data['Register']['species'];
				$filter .= '/ Esp.: '.$this->request->data['Register']['species'];
			}


			$options = array(
				'recursive' => 0,
				'conditions' => $conditions
			);



			$registers = $this->Register->find('all', $options);

			$this->set(compact('registers','filter'));
		} else {

			$breadcrumbs = [
				['text'=>__('Home'), 'url'=>['controller'=>'pages','action'=>'display','home']],
				['text'=>__('Registers list'), 'url'=>['action'=>'index']],
				['text'=>__('Export to Excel')]
			];	    

			$this->set(compact('breadcrumbs'));		

			$this->render('export');
		}

	}

/**
 * to_pdf method
 *
 * @return void
 */
	public function to_pdf() {
		if ($this->request->is(array('post'))) {

			$conditions = array();

			if($this->request->data['Register']['from']){
				$dde = $this->Register->dateFormatBeforeSave($this->request->data['Register']['from']);
				$conditions['Register.date >='] =  $dde;
			}

			if($this->request->data['Register']['to']){
				$hta = $this->Register->dateFormatBeforeSave($this->request->data['Register']['to']);
				$conditions['Register.date <='] =  $hta;
			}

			if($this->request->data['Register']['plant_nro'])
				$conditions['Register.plant_nro'] = $this->request->data['Register']['plant_nro'];

			if($this->request->data['Register']['oncca_nro'])
				$conditions['Register.oncca_nro'] = $this->request->data['Register']['oncca_nro'];

			if($this->request->data['Register']['troop_nro'])
				$conditions['Register.troop_nro'] = $this->request->data['Register']['troop_nro'];

			if($this->request->data['Register']['dopro_nro'])
				$conditions['Register.dopro_nro'] = $this->request->data['Register']['dopro_nro'];

			if($this->request->data['Register']['dte_nro'])
				$conditions['Register.dte_nro'] = $this->request->data['Register']['dte_nro'];

			if($this->request->data['Register']['romaneo_oncca_nro'])
				$conditions['Register.romaneo_oncca_nro'] = $this->request->data['Register']['romaneo_oncca_nro'];

			if($this->request->data['Register']['cuit_nro'])
				$conditions['Register.cuit_nro'] = $this->request->data['Register']['cuit_nro'];

			if($this->request->data['Register']['renspa_nro'])
				$conditions['Register.renspa_nro'] = $this->request->data['Register']['renspa_nro'];

			if($this->request->data['Register']['consignee_oncca_nro'])
				$conditions['Register.consignee_oncca_nro'] = $this->request->data['Register']['consignee_oncca_nro'];

			if($this->request->data['Register']['species'])
				$conditions['Register.species'] = $this->request->data['Register']['species'];

			if($this->request->data['Register']['enter_kg'])
				$conditions['Register.enter_kg'] = $this->request->data['Register']['enter_kg'];

			if($this->request->data['Register']['end_kg'])
				$conditions['Register.end_kg'] = $this->request->data['Register']['end_kg'];

			if($this->request->data['Register']['confiscated_organs'])
				$conditions['Register.confiscated_organs'] = $this->request->data['Register']['confiscated_organs'];

			if($this->request->data['Register']['owner'])
				$conditions['Register.owner LIKE'] = '%'.$this->request->data['Register']['owner'].'%';

			if($this->request->data['Register']['plant'])
				$conditions['Register.plant LIKE'] = '%'.$this->request->data['Register']['plant'].'%';

			if($this->request->data['Register']['department'])
				$conditions['Register.department LIKE'] = '%'.$this->request->data['Register']['department'].'%';

			if($this->request->data['Register']['locality'])
				$conditions['Register.locality LIKE'] = '%'.$this->request->data['Register']['locality'].'%';

			if($this->request->data['Register']['province'])
				$conditions['Register.province LIKE'] = '%'.$this->request->data['Register']['province'].'%';

			if($this->request->data['Register']['consignee'])
				$conditions['Register.consignee LIKE'] = '%'.$this->request->data['Register']['consignee'].'%';

			if($this->request->data['Register']['category'])
				$conditions['Register.category LIKE'] = '%'.$this->request->data['Register']['category'].'%';

			if($this->request->data['Register']['confiscated_why'])
				$conditions['Register.confiscated_why LIKE'] = '%'.$this->request->data['Register']['confiscated_why'].'%';

			if($this->request->data['Register']['organs_names'])
				$conditions['Register.organs_names LIKE'] = '%'.$this->request->data['Register']['organs_names'].'%';

			if($this->request->data['Register']['organs_patology'])
				$conditions['Register.organs_patology LIKE'] = '%'.$this->request->data['Register']['organs_patology'].'%';

			if($this->request->data['Register']['veterinary'])
				$conditions['Register.veterinary LIKE'] = '%'.$this->request->data['Register']['veterinary'].'%';

			if($this->request->data['Register']['meat_target'])
				$conditions['Register.meat_target LIKE'] = '%'.$this->request->data['Register']['meat_target'].'%';

			if($this->request->data['Register']['notes'])
				$conditions['Register.notes LIKE'] = '%'.$this->request->data['Register']['notes'].'%';


			$registers = $this->Register->find('all', array('conditions'=>$conditions));

			$this->set(compact('registers'));
			$this->set('mostrar', $this->request->data['Mostrar']);

			$name = 'lista_'.date('Ymd').'.pdf';

			$this->response->download($name);


			$this->layout = 'pdf';


			$this->render('list_pdf');

			$this->Mpdf->init(array('format'=>'A4-L'));
		    // ... set params
		    $this->Mpdf->setAutoTopMargin = true;
		    $this->Mpdf->shrink_tables_to_fit=1;

		    $this->Mpdf->setFilename($name);
		    $this->Mpdf->setOutput('I');				

		}
		
		$breadcrumbs = [
			['text'=>__('Home'), 'url'=>['controller'=>'pages','action'=>'display','home']],
			['text'=>__('Registers list'), 'url'=>['action'=>'index']],
			['text'=>__('To PDF')]
		];		

		$this->set(compact('breadcrumbs'));		
	}


}
