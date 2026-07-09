<?php
/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
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
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
	public $actsAs = array('Containable');


	public function afterFind($results, $primary = false) {

		if(isset($this->dateFields)) {
		    foreach ($results as $key => $val) {
	    		foreach($this->dateFields as $field){


			        if (isset($val[$this->alias][$field])) {

			            $results[$key][$this->alias][$field.'_wf'] = $results[$key][$this->alias][$field];
			            $results[$key][$this->alias][$field] = $this->dateFormatAfterFind(
			                $val[$this->alias][$field]
			            );
			        }
			    }
		    }
		}

		if(isset($this->datetimeFields)) {
		    foreach ($results as $key => $val) {
	    		foreach($this->datetimeFields as $field){

			        if (isset($val[$this->alias][$field])) {

			            $results[$key][$this->alias][$field.'_wf'] = $results[$key][$this->alias][$field];
			            $results[$key][$this->alias][$field] = $this->dateTimeFormatAfterFind(
			                $val[$this->alias][$field]
			            );
			        }
			    }
		    }
		}


	    return $results;
	}

	public function beforeSave($options = array()){
		if(isset($this->dateFields)){
			foreach ($this->dateFields as $field) {
			    if (strtoupper($field)!='CREATED' && strtoupper($field)!='MODIFIED' && !empty($this->data[$this->alias][$field]) ) {
			        $this->data[$this->alias][$field] = $this->dateFormatBeforeSave(
			            $this->data[$this->alias][$field]
			        );
			    }
			}
		}

		if(isset($this->datetimeFields)){
			foreach ($this->datetimeFields as $field) {
			    if (strtoupper($field)!='CREATED' && strtoupper($field)!='MODIFIED' && !empty($this->data[$this->alias][$field]) ) {
			        $this->data[$this->alias][$field] = $this->dateTimeFormatBeforeSave(
			            $this->data[$this->alias][$field]
			        );
			    }
			}
		}		

	}

	public function dateFormatAfterFind($dateString) {
		return date('d/m/Y', strtotime($dateString));
	}
	
	public function dateTimeFormatAfterFind($dateString) {
		return date('d/m/Y h:i a', strtotime($dateString));
	}
	
	public function dateTimeFormatBeforeSave($dateString) {

		$p1 = explode(' ', $dateString);
		$f = explode('/', $p1[0]);
		$h = explode(':', $p1[1]);

		if($p1[2]=='pm') $h[0]= ($h[0]*1)+12;

		return $f[2].'-'.$f[1].'-'.$f[0].' '.$h[0].':'.$h[1];

		//return date('Y-m-d H:i', strtotime($dateString));
	}

	public function dateFormatBeforeSave($dateString) {

		return date('Y-m-d', strtotime(str_replace('/','-',$dateString)));
	}	

	function dateDifference($date_1 , $date_2 , $differenceFormat = '%a' ) {
	    $datetime1 = date_create($date_1);
	    $datetime2 = date_create($date_2);
	    
	    $interval = date_diff($datetime1, $datetime2);
	    
	    return $interval->format($differenceFormat);
	    
	}	

	public function passwordCreator($length=8, $alphaLower=true, $alphaUpper=true, $numbers=true, $symbols=true, $exclude=''){
		$alpha = "abcdefghijklmnopqrstuvwxyz";
		$alpha_upper = strtoupper($alpha);
		$numeric = "0123456789";
		$special = ".-+=_,!@$#*%<>[]{}";
		$chars = "";
		 

		if ($alphaLower)
			$chars .= $alpha;
		 
		if ($alphaUpper)
			$chars .= $alpha_upper;
		 
		if ($numbers)
			$chars .= $numeric;
		 
		if ($symbols)
			$chars .= $special;
			 
		 
		$len = strlen($chars);
		$pw = '';
		 
		for ($i=0;$i<$length;$i++) {

			$nr = mt_rand(0, $len-1);
			$ch = substr($chars, $nr, 1);				

			while(strlen($exclude)>0 && strpos($exclude, $ch)!==false){
				$nr = mt_rand(0, $len-1);
				$ch = substr($chars, $nr, 1);				
			}

			$pw .= $ch;
		}

		return str_shuffle($pw);	
	}	
}
