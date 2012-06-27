<?php
/*
 * Wolf CMS - Content Management Simplified. <http://www.wolfcms.org>
 * Copyright (C) 2008-2010 Martijn van der Kleijn <martijn.niji@gmail.com>
 *
 * This file is part of Wolf CMS. Wolf CMS is licensed under the GNU GPLv3 license.
 * Please see license.txt for the full license text.
 */

/* Security measure */
if (!defined('IN_CMS')) { exit(); }

/**
 * The multiedit plugin serves as a basic plugin template.
 *
 * This multiedit plugin makes use/provides the following features:
 * - A controller without a tab
 * - Three views (sidebar, documentation and settings)
 * - A documentation page
 * - A sidebar
 * - A settings page (that does nothing except display some text)
 * - Code that gets run when the plugin is enabled (enable.php)
 *
 * Note: to use the settings and documentation pages, you will first need to enable
 * the plugin!
 *
 * @package Plugins
 * @subpackage multiedit
 *
 * @author Martijn van der Kleijn <martijn.niji@gmail.com>
 * @copyright Martijn van der Kleijn, 2008
 * @license http://www.gnu.org/licenses/gpl.html GPLv3 license
 */

class MultieditController extends PluginController {
    const PLUGIN_REL_VIEW_FOLDER = "../../plugins/multiedit/views/";	
    private static $pagesList = array();
	
    public function __construct() {
        $this->setLayout('backend');
        $this->assignToLayout('sidebar', new View(self::PLUGIN_REL_VIEW_FOLDER.'sidebar'));
    }
    
    public static function makeSubpagesList($page) {
        $arr = array('order' => 'position ASC, published_on DESC');
        if ($page && count($page->children(null, array(), true)) > 0) {
            foreach ($page->children($arr, array(), true) as $menu) :
			if ($menu->childrenCount(null, array(), true) > 0) {
				self::$pagesList[] = array(
				'label' => str_replace(" ", "&nbsp;&nbsp;", str_pad(' ', $menu->level(), " ", STR_PAD_LEFT)) . $menu->title,
				'id' => $menu->id,
				'count' => $menu->childrenCount(null, array(), true),
				);
			}
			MultieditController::makeSubpagesList($menu);

            endforeach;
        }
    }



     public function getonepage($page_id,$showpageparts=1) {
	$items[] = Page::findById((int) $page_id); // add one item to array;
//	echo '<pre>';
//	print_r ($items[0]);
//	echo '</pre>';
	if ($page_id > 1) {$parentPage = Page::findById($items[0]->parent_id);}
	if ($parentPage) {$parentUri = $parentPage->getUri();} else {$parentUri='';}
	  
	$itemsList = new View(self::PLUGIN_REL_VIEW_FOLDER.'itemslist', array(
			'items' => $items,
			'innerOnly' => true,
			'parentUri' => $parentUri,		
			'showpageparts' => $showpageparts,
			'filters' => Filter::findAll(),
			'behaviors' => Behavior::findAll(),
			'layouts' => Record::findAllFrom('Layout')			
			));
	echo $itemsList->render();	
    }   
    
     public function getsubpages($page_id, $sorting="id", $order="ASC",$showpageparts=1) {
	$parentPage = Page::findById($page_id);
	$items = Page::findAllFrom('Page', 'parent_id=? ORDER BY '.$sorting.' '.$order,array((int)$page_id));

	
	$itemsList = new View(self::PLUGIN_REL_VIEW_FOLDER.'itemslist', array(
			'items' => $items,
			'parentUri' => $parentPage->getUri(),
			'showpageparts' => $showpageparts,
			'filters' => Filter::findAll(),
			'behaviors' => Behavior::findAll(),
			'layouts' => Record::findAllFrom('Layout')			
			));
	echo $itemsList->render();
    }   


    public function index() {
	$page = Page::findById(1);
	self::makeSubpagesList($page);
	               $list = new View(self::PLUGIN_REL_VIEW_FOLDER.'pageselect', array(
                            'pagesList' => self::$pagesList,
                        )); 

	$items = Page::findAllFrom('Page', 'parent_id=?',array($page->id));
	
	$itemsList = new View(self::PLUGIN_REL_VIEW_FOLDER.'itemslist', array(
			'items' => $items,
			'parentUri' => '', //uri of root page = ''
			'showpageparts' => '1', //show page parts by default			
			'filters' => Filter::findAll(),
			'behaviors' => Behavior::findAll(),
			'layouts' => Record::findAllFrom('Layout')			
			));
	
	$this->display('multiedit/views/index', array(
				'pagesList' => $list,
				'itemsList' => $itemsList,
			));
    }
    
    public static function checkdatevalid($sDate) {

	if ((preg_match('/^([0-9]{4})[-_]([0-9]{2})[-_]([0-9]{2}) ([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/D', (string) $sDate, $bits) &&
	checkdate($bits[2], $bits[3], $bits[1]))==true) {return true;}
	else return false;

    }

    public function setvalue() {
		$item = explode('-', $_POST['item']);
		$field = $item[0];
		$identifier = $item[1];
		$value = $_POST['value'];
		
		if ($field=='slug') {
				$page = Record::findOneFrom('Page','id=?', array($identifier));
				$oldslug = $page->slug;
				if (strpos($value,'/')) {
					$result =  array('message' => 'Slug cannot contain slashes!',
 						 'oldvalue' => $oldslug,
						   'status' => 'error');
				echo json_encode($result); return false;
				}
				if (strlen($value)<1) {
					$result =  array('message' => 'Slug cannot be empty!',
 						 'oldvalue' => $oldslug,
						   'status' => 'error');
				echo json_encode($result); return false;
				}				
				$exists = Record::countFrom('Page', 'parent_id=? AND slug=?', array($page->parent_id,$value));
				if ($exists>0) {
					$result =  array('message' => 'Slug exists: <b>' . $value . '</b>',
 						 'oldvalue' => $oldslug,
						   'status' => 'error');
				echo json_encode($result); return false;
				}
		}
		elseif ($field=='part') {
				$tmpval = explode('_partname_', $identifier);
				$page_id = $tmpval[0];
				$part_name = $tmpval[1];
				
				Record::update('Page', array($field => $value,        
					'updated_by_id' => AuthUser::getId(),
					'updated_on' => date('Y-m-d H:i:s')
					), 'id=?', array($page_id));				
				
				$part = Record::findOneFrom('PagePart','name=? AND page_id=?', array($part_name, $page_id));
				$part->content = $value;
				$part->content_html = $value;
				if ($part->save()) {
					$result =  array('message' => 'Updated PagePart <b>' . $part->name. '</b>' . ' in page: ' . $page_id,
							'status' => 'OK');
					}
					else {
					$result =  array('message' => 'Error saving PagePart <b>' . $part->name. '</b>' . ' in page: ' . $page_id,
							'status' => 'OK');
					}
		echo json_encode($result); return false;						 
		}
		elseif ( in_array($field, array('created_on','published_on')) ) {
			 $correct = MultieditController::checkdatevalid($value);
			 if (! $correct) {
				$page = Page::findById((int) $item[1]);
				$result =  array('message' => 'Date: <b>' . $value. '</b>',
						 'oldvalue' => $page->{$field},
						 'status' => 'error');
				echo json_encode($result); return false;
			 }
		}
		elseif ( $field == 'valid_until' ) {
			 if (trim($value,'-/: ')=='') {
				 $sql = "UPDATE `page` SET `valid_until`=DEFAULT WHERE `page`.`id` = ".(int)$identifier." LIMIT 1";
				 Record::query($sql);
				 $result =  array('message' => 'Valid_until cleared in page <b>' . $identifier . '</b>',
						 'status' => 'OK');
        	  		 echo json_encode($result); return false;
				 };
			 $correct = MultieditController::checkdatevalid($value);
			 if ($correct) {
				 Record::update('Page', array($item[0] => $value), 'id=?', array($identifier));
				 
				 $result =  array('message' => 'Changed <b>"' . $field . '"</b><br/>'.
							       'in page: <b>' . $identifier . '</b><br/>'.
							       'new value: <b>' . $value . '</b>',
						 'status' => 'OK');
			 } else {
				$page = Page::findById((int) $item[1]);
				$result =  array('message' => 'Invalid datetime: <b>' . $value. '</b>',
						 'oldvalue' => $page->{$field},
						 'status' => 'error');
			 }
				
			echo json_encode($result); return false;
		}
		elseif ($field == 'tags') {
			$page = Page::findById((int) $identifier);
			$page -> setTags($value);

			$result =  array(
				'message' => 'Updated <b>' . $item[0] . '</b> in page <b>' . $identifier . '</b>',
				'status' => 'OK'
				);
			
			echo json_encode($result); return false;
		}
		
		//$page = Record::findByIdFrom('Page', (int) $identifier);
		//$page->{$field} = $value;
		Record::update('Page', array($field => $value,        
					'updated_by_id' => AuthUser::getId(),
					'updated_on' => date('Y-m-d H:i:s')
					), 'id=?', array($identifier));
		//
		//$page = Page::findById((int) $identifier);
		//$page->setFromData( array($field => $value) );
		//$page->{$field} = $value;
		//$page->save();
		
		$result = array('message' => 'Updated <b>' . $field . '</b> in page <b>' . $identifier . '</b>',
				'status' => 'OK');
		echo json_encode($result);
		return false;
		
	}
}