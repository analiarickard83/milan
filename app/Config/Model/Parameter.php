<?php
App::uses('AppModel', 'Model');


class Parameter extends AppModel {

	public $displayField = 'value';


	public function get($id) {
		if (!$this->exists($id)) {
			return '';
		}

		$this->id = $id;
		return $this->field('value');
	} 

	public function afterFind($results, $primary = false) {

		foreach ($results as $key => $val) {
			if (isset($val['Parameter']['value'])) {
				$results[$key]['Parameter']['value'] = urldecode($results[$key]['Parameter']['value']) ;
			}
			

		}
		return $results;
	}	

	public function beforeSave($options = array()) {
		
		if (!empty($this->data['Parameter']['value']) ) {
			$this->data['Parameter']['value'] = urlencode($this->data['Parameter']['value']);
		}	  

		return true;
	}


}
