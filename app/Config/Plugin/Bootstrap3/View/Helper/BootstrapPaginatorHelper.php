<?php

/**
* Bootstrap Paginator Helper
*
*
* PHP 5
*
*  Licensed under the Apache License, Version 2.0 (the "License");
*  you may not use this file except in compliance with the License.
*  You may obtain a copy of the License at
*
*      http://www.apache.org/licenses/LICENSE-2.0
*
*
* @copyright Copyright (c) Mikaël Capelle (http://mikael-capelle.fr)
* @link http://mikael-capelle.fr
* @package app.View.Helper
* @since Apache v2
* @license http://www.apache.org/licenses/LICENSE-2.0
*/

App::import('Helper', 'Paginator') ;

class BootstrapPaginatorHelper extends PaginatorHelper {


public function sort($key, $title = null, $options = array()) {
        $options += array('url' => array(), 'model' => null);
        $url = $options['url'];
        unset($options['url']);

        if (empty($title)) {
            $title = $key;

            if (strpos($title, '.') !== false) {
                $title = str_replace('.', ' ', $title);
            }

            $title = __(Inflector::humanize(preg_replace('/_id$/', '', $title)));
        }
        $defaultDir = isset($options['direction']) ? $options['direction'] : 'asc';
        unset($options['direction']);

        $locked = isset($options['lock']) ? $options['lock'] : false;
        unset($options['lock']);

        $sortKey = $this->sortKey($options['model']);
        $defaultModel = $this->defaultModel();
        $isSorted = (
            $sortKey === $key ||
            $sortKey === $defaultModel . '.' . $key ||
            $key === $defaultModel . '.' . $sortKey
        );

        $dir = $defaultDir;
        $icon = '';
        if ($isSorted) {
            $dir = $this->sortDir($options['model']) === 'asc' ? 'desc' : 'asc';
            $class = $dir === 'asc' ? 'desc' : 'asc';
            if (!empty($options['class'])) {
                $options['class'] .= ' ' . $class;
            } else {
                $options['class'] = $class;
            }

            if ($locked) {
                $dir = $defaultDir;
                $options['class'] .= ' locked';
            }
        } else {
            if (!empty($options['class'])) {
                $options['class'] .= ' ' . 'sort';
            } else {
                $options['class'] = 'sort';
            }
        }
        if (is_array($title) && array_key_exists($dir, $title)) {
            $title = $title[$dir];
        }

        $url = array_merge(array('sort' => $key, 'direction' => $dir), $url, array('order' => null));
        return $this->link($title, $url, $options);
    }

    protected function _extractOption ($key, $options, $default = null) {
        if (isset($options[$key])) {
            return $options[$key] ;
        }
        return $default ;
    }
    
    /**
     * 
     * Get link to the first pagination page.
     * 
     * @param $title The link text
     * @param $options Options for link
     * @param $disabledtitle Title when link is disabled
     * @param $disabledOptions Options for link when it's disabled
     * 
    **/
    public function first ($title = '<<', $options = array(), $disabledTitle = null, $disabledOptions = array()) {
        $options = array_merge(array('tag' => 'li'), $options) ;
        $disabledOptions = array_merge(array('tag' => 'li', 'class' => 'disabled', 'disabledTag' => 'a'),
            $disabledOptions) ;
        return parent::first($title, $options, $disabledTitle, $disabledOptions) ;        
    }
    
    /**
     * 
     * Get link to the previous pagination page.
     * 
     * @param $title The link text
     * @param $options Options for link
     * @param $disabledtitle Title when link is disabled
     * @param $disabledOptions Options for link when it's disabled
     * 
    **/
    public function prev ($title = '<', $options = array(), $disabledTitle = null, $disabledOptions = array()) {
        $options = array_merge(array('tag' => 'li'), $options) ;
        $disabledOptions = array_merge(array('tag' => 'li', 'class' => 'disabled', 'disabledTag' => 'a'),
            $disabledOptions) ;
        return parent::prev($title, $options, $disabledTitle, $disabledOptions) ;        
    }
    
    /**
     * 
     * Get link to the next pagination page.
     * 
     * @param $title The link text
     * @param $options Options for link
     * @param $disabledtitle Title when link is disabled
     * @param $disabledOptions Options for link when it's disabled
     * 
    **/
    public function next ($title = '>', $options = array(), $disabledTitle = null, $disabledOptions = array()) {
        $options = array_merge(array('tag' => 'li'), $options) ;
        $disabledOptions = array_merge(array('tag' => 'li', 'class' => 'disabled', 'disabledTag' => 'a'),
            $disabledOptions) ;
        return parent::next($title, $options, $disabledTitle, $disabledOptions) ;        
    }
    
    /**
     * 
     * Get link to the last pagination page.
     * 
     * @param $title The link text
     * @param $options Options for link
     * @param $disabledtitle Title when link is disabled
     * @param $disabledOptions Options for link when it's disabled
     * 
    **/
    public function last ($title = '>>', $options = array(), $disabledTitle = null, $disabledOptions = array()) {
        $options = array_merge(array('tag' => 'li'), $options) ;
        $disabledOptions = array_merge(array('tag' => 'li', 'class' => 'disabled', 'disabledTag' => 'a'),
            $disabledOptions) ;
        return parent::last($title, $options, $disabledTitle, $disabledOptions) ;        
    }
    
        
    /**
     * 
     * Get pagination link list.
     * 
     * @param $options Options for link element
     *
     * Extra options:
     *  - size small/normal/large (default normal)
     *       
    **/
    public function numbers ($options = array()) {
    
        $default = array(
            'tag' => 'li',
            'currentTag' => 'a', 
            'separator' => '', 
            'currentClass' => 'active', 
            'disabledTag' => 'a',
            'size' => 'normal') ;
        $options = array_merge($default, $options) ;
        
        $size = $options['size'] ; unset($options['size']) ;
        
        $class = 'pagination' ;

        if (isset($options['ulClass'])) {
            $class .= ' '.$options['ulClass'] ;
            unset($options['ulClass']) ;
        }
        
        switch ($size) {
        case 'small':
            $class .= ' pagination-sm' ;
            break ;
        case 'large':
            $class .= ' pagination-lg' ;
            break ;
        }
          
        $options['before'] = '<ul class="'.$class.'">' ;
        $options['after'] = '</ul>' ;

        if (isset($options['prev'])) {
            $options['before'] .= $this->prev($options['prev']) ;
        }

        if (isset($options['next'])) {
            $options['after'] = $this->next($options['next']).$options['after'] ;
        }
                
        return parent::numbers ($options) ;
    }

}

?>
