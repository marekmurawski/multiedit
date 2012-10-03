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
/*
 * Wolf CMS - Content Management Simplified. <http://www.wolfcms.org>
 * Copyright (C) 2008-2010 Martijn van der Kleijn <martijn.niji@gmail.com>
 * 
 * MultiEdit Plugin for Wolf CMS
 * Provides convenient interface to quickly edit multiple pages metadata.
 *  
 * @package Plugins
 * @subpackage multiedit
 *
 * @author Marek Murawski <http://marekmurawski.pl>
 * @copyright Marek Murawski, 2012
 * @license http://www.gnu.org/licenses/gpl.html GPLv3 license
 */

class MultieditController extends PluginController {
    const PLUGIN_REL_VIEW_FOLDER = "../../plugins/multiedit/views/";	
    private static $pagesList = array();
    private static $defaultSorting = 'position ASC, published_on DESC';
	
    public function __construct() {
        $this->setLayout('backend');
	$lang = ( $user = AuthUser::getRecord() ) ? strtolower($user->language) : 'en';
        if( !file_exists( PLUGINS_ROOT.DS.'multiedit/views/documentation/sidebar/'.$lang.'.php') ) {            
            $lang='en';
        }
	$sidebarContents = new View(self::PLUGIN_REL_VIEW_FOLDER.'documentation/sidebar/'.$lang);
        $this->assignToLayout('sidebar', new View(self::PLUGIN_REL_VIEW_FOLDER.'sidebar',array(
			'sidebarContents' => $sidebarContents
	)));
    }

    private static function getAllChildren($id) {
        // Prepare SQL
        $sql = 'SELECT page.* '
                . 'FROM '.TABLE_PREFIX.'page AS page '
                . 'WHERE parent_id = '.(int)$id
                . " ORDER BY page.position, page.id";

        $pages = array();
        // Run!
        if ($stmt = Record::getConnection()->prepare($sql)) {
            $stmt->execute();
            while ($object = $stmt->fetchObject()) $pages[] = $object;            
        }

        return $pages;
    }
    
    private static function countAllChildren($id) {
        // Prepare SQL
        $sql = 'SELECT COUNT(*) AS nb_rows '
                . 'FROM '.TABLE_PREFIX.'page AS page '
	        . 'WHERE parent_id = '.(int)$id;
        $stmt = Record::getConnection()->prepare($sql);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public static function makePagesListRecursive($page_id=1) {
     $children = self::getAllChildren($page_id);
     static $nestLevel; //for storing level, faster than ->level()
     if (count($children) > 0) {
	 $nestLevel++;
         foreach ($children as $childpage) {
		$childCount = self::countAllChildren($childpage->id);
		if ($childCount>0) {self::$pagesList[] = array( //add only if there are children
				'label' => str_replace(" ", "-", str_pad(' ', $nestLevel, " ", STR_PAD_LEFT)) . ' ' . $childpage->breadcrumb,
				'id' => $childpage->id,
				'count' => $childCount,
				);
		self::makePagesListRecursive($childpage->id);
		}
	 }
	 $nestLevel--;
     }
    }    

     public function getonepage($page_id,$showpageparts=1,$showcollapsed=0,$is_frontend=0) {
	$items[] = Page::findById((int) $page_id); // add one item to array;

	if ($page_id > 1) {$parentPage = Page::findById($items[0]->parent_id);}
	if (isset($parentPage)) {$parentUri = $parentPage->getUri();} else {$parentUri='';}
	  
	$itemsList = new View(self::PLUGIN_REL_VIEW_FOLDER.'itemslist', array(
			'items' => $items,
			'innerOnly' => true,
			'parentUri' => $parentUri,		
			'showpageparts' => $showpageparts,
			'showcollapsed' => $showcollapsed,
            'is_frontend'   => $is_frontend==='1',
			));
	echo $itemsList->render();	
    }   
    
     public function getsubpages($page_id, $sorting="id", $order="ASC",$showpageparts=1,$showcollapsed=0) {
	$parentPage = Page::findById($page_id);
	if ($sorting != '-default-') {
	$items = Page::findAllFrom('Page', 'parent_id=? ORDER BY '.$sorting.' '.$order,array((int)$page_id));
	} else {
	$items = Page::findAllFrom('Page', 'parent_id=? ORDER BY '.self::$defaultSorting,array((int)$page_id));
	}
	$parentUri = $parentPage->getUri();
	$rootItem = new View(self::PLUGIN_REL_VIEW_FOLDER.'itemslist', array(
			'items' => array($parentPage),
			'isRoot' => true,
			'parentUri' => isset($parentPage->parent_id) ? mb_substr($parentUri,0,-mb_strlen(strrchr($parentUri,"/"))) : '', //trim last slash
			'showpageparts' => $showpageparts,
			'showcollapsed' => $showcollapsed, 
			));	
	$itemsList = new View(self::PLUGIN_REL_VIEW_FOLDER.'itemslist', array(
			'items' => $items,
			'rootItem' => $parentPage,
			'parentUri' => $parentUri,
			'showpageparts' => $showpageparts,
			'showcollapsed' => $showcollapsed,
			));
	echo $rootItem->render();
	echo $itemsList->render();
    }   


    public function index() {
	$page = Page::findById(1);
	self::makePagesListRecursive($page->id);
	               $list = new View(self::PLUGIN_REL_VIEW_FOLDER.'header', array(
                            'pagesList' => self::$pagesList,
			    'rootPage' => $page	 
                        )); 
	$items = Page::findAllFrom('Page', 'parent_id=? ORDER BY '.self::$defaultSorting, array($page->id));
	
	$rootItem = new View(self::PLUGIN_REL_VIEW_FOLDER.'itemslist', array(
			'items' => array($page),
			'isRoot' => true,
			'parentUri' => '', //uri of root page = ''
			'showpageparts' => '1', //show page parts by default
			'showcollapsed' => '0', // show expanded by default
			));

	$itemsList = new View(self::PLUGIN_REL_VIEW_FOLDER.'itemslist', array(
			'items' => $items,
			'parentUri' => '', //uri of root page = ''
			'showpageparts' => '1', //show page parts by default			
			'showcollapsed' => '0', // show expanded by default
			));
	
	$this->display('multiedit/views/index', array(
				'pagesList' => $list,
				'rootItem' => $rootItem,
				'itemsList' => $itemsList,
			));
    }
    
    public static function checkdatevalid($sDate) {
	if ((preg_match('/^([0-9]{4})[-_]([0-9]{2})[-_]([0-9]{2}) ([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/D', (string) $sDate, $bits) &&
	checkdate($bits[2], $bits[3], $bits[1]))==true) {return true;}
	else return false;
    }

    public function setvalue() {
		$fieldsAffectingUpdatedOn = array('title','breadcrumb','slug','keywords','description');
		// Page part changes always update "updated_on" field
        	$item = explode('-', $_POST['item']);
		$field = $item[0];
		$identifier = $item[1];
		$value = $_POST['value'];
		$now_datetime = date('Y-m-d H:i:s');
		$messagesExt = array(); //extended messages
		$returnExt = array(); //extended return fields for jquery request
		$needsReloading = '0';

		if ($field=='slug') {
				$page = Record::findOneFrom('Page','id=?', array($identifier));
				$oldslug = $page->slug;
				if ($identifier==1) { //root page protection
					$result =  array('message' => __("Slug of root page can't be changed!"),
 						 'oldvalue' => $oldslug,
						   'status' => 'error');
				echo json_encode($result); return false;
				}				
				if (strpos($value,'/')) {
					$result =  array('message' => __('Slug cannot contain slashes!'),
 						 'oldvalue' => $oldslug,
						   'status' => 'error');
				echo json_encode($result); return false;
				}
				if (strlen($value)<1) {
					$result =  array('message' => __('Slug cannot be empty!'),
 						 'oldvalue' => $oldslug,
						   'status' => 'error');
				echo json_encode($result); return false;
				}				
				$exists = Record::countFrom('Page', 'parent_id=? AND slug=?', array($page->parent_id,$value));
				if ($exists>0) {
					$result =  array('message' => __('Other sibling page already has this slug - <b>:slug</b> - restoring original one',array(':slug' => $value)),
 						 'oldvalue' => $oldslug,
						   'status' => 'error');
				echo json_encode($result); return false;
				}
		}
		elseif ($field=='part') {
				$tmpval = explode('_partname_', $_POST['item']);
				$page_id = substr($tmpval[0],strlen($field)+1);
				$part_name = $tmpval[1];
				$revision_save_info = ''; //Part_revisions plugin notice
				
				Record::update('Page', array($field => $value,        
					'updated_by_id' => AuthUser::getId(),
					'updated_on' => $now_datetime
					), 'id=?', array($page_id));				
				
				$part = Record::findOneFrom('PagePart','name=? AND page_id=?', array($part_name, $page_id));
				$part->content = $value;
				$part->content_html = $value;

				if (Plugin::isEnabled('part_revisions')) {
					if (save_old_part($part)) {
					$savedPart = Flash::get('page_revisions_saved_parts');
					$revision_save_info = '<br/><br/>' . __('Part Revisions plugin active:'). '<br/>'. __('Revision saved for') . ' <b>' . $savedPart[0] . '</b>';
					} else {$revision_save_info = '<br/><br/>' . __('Part Revisions plugin active:'). '<br/>'. __('Revision not saved!');}
				}				
				
				if ($part->save()) {

					$result =  array('message' => __('Updated <b>:part</b> page part in page <b>:page</b>', array(':part'=>$part->name,':page'=>$page_id)) .
							$revision_save_info,
							'datetime' => $now_datetime, 
							'identifier' => $page_id,
							'status' => 'OK');
					}
					else {
					$result =  array('message' => __('Error updating <b>:part</b> page part in page <b>:page</b>', array(':part'=>$part->name,':page'=>$page_id)),
							'status' => 'error');
					}
		echo json_encode($result); return false;						 
		}
		elseif ( in_array($field, array('created_on','published_on')) ) {
			 $correct = MultieditController::checkdatevalid($value);
			 if (! $correct) {
				$page = Page::findById((int) $identifier);
				$result =  array('message' => __('Wrong date - <b>:date</b> - restoring original one', array(':date'=>$value)),
						 'oldvalue' => $page->{$field},
						 'status' => 'error');
				echo json_encode($result); return false;
			 }  else {
				 if  ($now_datetime<$value)  {
					 $messagesExt[] = '<span class="warning">'.__('Warning: Date of <b>:field</b> is in future!', array(':field'=>$field)).'</span>';
				 };
			 }
		}
		elseif ( $field == 'valid_until' ) {
			 if (trim($value,'-/: ')=='') {
				 Record::getConnection()->exec("UPDATE " . TABLE_PREFIX . "page SET valid_until=NULL WHERE id=".(int)$identifier);

				 $result =  array('message' => __('Cleared <b>valid_until</b> field in page: <b>:page</b>', array(':page'=>$identifier)),
						  'datetime' => $now_datetime, 
						  'identifier' => $identifier,
						  'status' => 'OK');
        	  		 echo json_encode($result); return false;
				 };
			 $correct = MultieditController::checkdatevalid($value);
			 if (! $correct) {
				$page = Page::findById((int) $identifier);
				$result =  array('message' => __('Wrong date - <b>:date</b> - restoring original one', array(':date'=>$value)),
						 'oldvalue' => $page->{$field},
						 'status' => 'error');
				echo json_encode($result); return false;
			 }
			if ($value < $now_datetime) {
					Record::getConnection()->exec("UPDATE " . TABLE_PREFIX . "page SET status_id=".Page::STATUS_ARCHIVED." WHERE id=".(int)$identifier);
					$messagesExt[] = '<span class="warning">'.__('Warning: Date of <b>:field</b> is in past! Changed page status to archived!', array(':field'=>$field)).'</span>';
					$returnExt = array('setstatus'=>Page::STATUS_ARCHIVED,
							  'identifier' => $identifier,
							);
				}			
		}
		elseif ($field == 'tags') {
			$page = Page::findById((int) $identifier);
			$page -> setTags($value);

			$result =  array(
				'message' => __('Updated <b>tags</b> in page: <b>:page</b>', array(':page'=>$identifier)),
				'status' => 'OK'
				);
			
			echo json_encode($result); return false;
		}
		
		$toUpdate = array($field => $value);
		$updateInfo = array(   'updated_by_id' => AuthUser::getId(),
					'updated_on' => $now_datetime);
		
		// add modification time to update array if field affects updated_on
		if (in_array($field, $fieldsAffectingUpdatedOn)) {
			$toUpdate = array_merge($toUpdate,$updateInfo);}
		
		// @todo allow NULL values insertion instead of empty strings
		Record::update('Page', $toUpdate, 'id=?', array($identifier));
		
		if (count($messagesExt)>0) {$moreMessages='<br/>'.implode('<br/>', $messagesExt);} else {$moreMessages='';}

		$result =  array_merge(
			   array('message' => __('Updated field <b>:field</b> in page <b>:page</b>',array(':field'=>$field, ':page'=>$identifier)).$moreMessages,
				'status' => 'OK'),
			   $returnExt // add extended return
			  );
		$timeInfo = array('datetime' => $now_datetime, 
				'identifier' => $identifier);

		// add modification time to return array if field affects updated_on
		if (in_array($field, $fieldsAffectingUpdatedOn)) {
			$result = array_merge($result,$timeInfo);
		}
		echo json_encode($result); return false;
		
	}
	
	public function documentation() {
		// Check for localized documentation or fallback to the default english and display notice
		$lang = ( $user = AuthUser::getRecord() ) ? strtolower($user->language) : 'en';

		if (!file_exists(PLUGINS_ROOT . DS . 'multiedit' . DS . 'views/documentation/' . $lang . '.php')) {
			$this->display('multiedit/views/documentation/en');
		}
		else
			$this->display('multiedit/views/documentation/' . $lang);
	}	
}