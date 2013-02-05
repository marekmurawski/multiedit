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
    public static  $editableFilters = array('ace','textile','markdown','codemirror');
    private static $defaultPageFields = array(
                                            'id',
                                            'title',
                                            'slug',
                                            'breadcrumb',
                                            'keywords',
                                            'description',
                                            'content',
                                            'parent_id',
                                            'layout_id',
                                            'behavior_id',
                                            'status_id',
                                            'parent',
                                            'created_on',
                                            'published_on',
                                            'valid_until',
                                            'updated_on',
                                            'created_by_id',
                                            'updated_by_id',
                                            'position',
                                            'is_protected',
                                            'needs_login',
                                            'url',
                                            'level',
                                            'tags',
                                            'author',
                                            'author_id',
                                            'updater',
                                            'updater_id',
                                            'created_by_name',
                                            'updated_by_name',
                                            'part',
                                            );

    private static $basicFields = array(
                                            'title',
                                            'slug',
                                            'breadcrumb',
                                            'keywords',
                                            'description',
                                            'layout_id',
                                            'behavior_id',
                                            'status_id',
                                            'created_on',
                                            'published_on',
                                            'valid_until',
                                            'updated_on',
//                                            'is_protected',
//                                            'needs_login',
                                            'tags',
                                            );
    public static $fieldTemplates = array(
        'mysql' => array(
                array('description'=>'varchar 10'    , 'query'=>':field_name: VARCHAR( 10 )   NOT NULL'),
                array('description'=>'varchar 20'    , 'query'=>':field_name: VARCHAR( 20 )   NOT NULL'),
                array('description'=>'varchar 32'    , 'query'=>':field_name: VARCHAR( 32 )   NOT NULL'),
                array('description'=>'varchar 64'    , 'query'=>':field_name: VARCHAR( 64 )   NOT NULL'),
                array('description'=>'varchar 255'   , 'query'=>':field_name: VARCHAR( 255 )  NOT NULL'),
                array('description'=>'varchar 1000'  , 'query'=>':field_name: VARCHAR( 1000 ) NOT NULL'),
                array('description'=>'text'          , 'query'=>':field_name: TEXT            NOT NULL'),
                array('description'=>'int 1'         , 'query'=>':field_name: INT( 1 )        NOT NULL'),
                array('description'=>'int 8'         , 'query'=>':field_name: INT( 8 )        NOT NULL'),
                array('description'=>'int 16'        , 'query'=>':field_name: INT( 16 )       NOT NULL'),
                array('description'=>'datetime'      , 'query'=>':field_name: DATETIME        NOT NULL'),
            ),
        'sqlite' => array(
                array('description'=>'varchar 10'    , 'query'=>':field_name: VARCHAR( 10 )   NOT NULL'),
                array('description'=>'varchar 20'    , 'query'=>':field_name: VARCHAR( 20 )   NOT NULL'),
                array('description'=>'varchar 32'    , 'query'=>':field_name: VARCHAR( 32 )   NOT NULL'),
                array('description'=>'varchar 64'    , 'query'=>':field_name: VARCHAR( 64 )   NOT NULL'),
                array('description'=>'varchar 255'   , 'query'=>':field_name: VARCHAR( 255 )  NOT NULL'),
                array('description'=>'varchar 1000'  , 'query'=>':field_name: VARCHAR( 1000 ) NOT NULL'),
                array('description'=>'text'          , 'query'=>':field_name: TEXT            NOT NULL'),
                array('description'=>'int 1'         , 'query'=>':field_name: INT( 1 )        NOT NULL'),
                array('description'=>'int 8'         , 'query'=>':field_name: INT( 8 )        NOT NULL'),
                array('description'=>'int 16'        , 'query'=>':field_name: INT( 16 )       NOT NULL'),
                array('description'=>'datetime'      , 'query'=>':field_name: DATETIME        NOT NULL'),
            ),
    );

    private static $supportedDrivers = array('mysql','sqlite');

    public function __construct() {

        if (!AuthUser::hasPermission('multiedit_view')) die ('Access denied');

        $this->DB_driver = strtolower(Record::getConnection()->getAttribute(PDO::ATTR_DRIVER_NAME));

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
        Record::logQuery($sql);
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
        Record::logQuery($sql);
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

    public function getonepage($page_id,$showpageparts=1,$showcollapsed=0,$is_frontend=0,$force=0) {
        $items[] = Page::findById((int) $page_id); // add one item to array;

        if ($page_id > 1) {$parentPage = Page::findById($items[0]->parent_id);}
        if (isset($parentPage)) {$parentUri = $parentPage->getUri();} else {$parentUri='';}

        $filters = Filter::findAll();
        $layouts = Record::findAllFrom('Layout');

        // extracting extended fields
        $extended_fields = array_keys(array_diff_key((array) $items[0], array_flip(self::$defaultPageFields)));

        $itemsList = new View(self::PLUGIN_REL_VIEW_FOLDER.'itemslist', array(
                'items' => $items,
                'innerOnly' => true,
                'parentUri' => $parentUri,
                'showpageparts' => $showpageparts,
                'showcollapsed' => $showcollapsed,
                'is_frontend'   => $is_frontend==='1',
                'filters'       => $filters,
                'layouts'       => $layouts,
                'extended_fields' => $extended_fields,
                'force' => ($force!=='0'),
                ));
        echo $itemsList->render();
    }

    public function getsubpages($page_id, $sorting="id", $order="ASC",$showpageparts=1,$showcollapsed=0) {
        if ($page_id=='-1') {
          $page_id = 1;
          $whereString='id <> 1';
          $showAll = true;
        } else {
          $whereString='parent_id='.Record::escape($page_id);
          $showAll = false;
        }
        $parentPage = Page::findById($page_id);
        if ($sorting != '-default-') {
          $items = Page::findAllFrom('Page', $whereString . ' ORDER BY '.$sorting.' '.$order);
        } else {
          $items = Page::findAllFrom('Page', $whereString . ' ORDER BY '.self::$defaultSorting);
        }

        $filters = Filter::findAll();
        $layouts = Record::findAllFrom('Layout');

        // extracting extended fields
        $extended_fields = array_keys(array_diff_key((array) $parentPage, array_flip(self::$defaultPageFields)));

        $parentUri = $parentPage->getUri();
        $rootItem = new View(self::PLUGIN_REL_VIEW_FOLDER.'itemslist', array(
                'items' => array($parentPage),
                'isRoot' => true,
                'parentUri' => isset($parentPage->parent_id) ? mb_substr($parentUri,0,-mb_strlen(strrchr($parentUri,"/"))) : '', //trim last slash
                'showpageparts' => $showpageparts,
                'showcollapsed' => $showcollapsed,
                'filters'       => $filters,
                'layouts'       => $layouts,
                'is_frontend'   => false,
                'extended_fields' => $extended_fields,
                ));
        if ($showAll===true) {
          $parentUri = false;
        }
        $itemsList = new View(self::PLUGIN_REL_VIEW_FOLDER.'itemslist', array(
                'items' => $items,
                'rootItem' => $parentPage,
                'parentUri' => $parentUri,
                'showpageparts' => $showpageparts,
                'showcollapsed' => $showcollapsed,
                'filters'       => $filters,
                'layouts'       => $layouts,
                'is_frontend'   => false,
                'extended_fields' => $extended_fields,
                ));
        echo $rootItem->render();
        echo $itemsList->render();
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

    public function index() {
        $page = Page::findById(1);
        self::makePagesListRecursive($page->id);
                      $list = new View(self::PLUGIN_REL_VIEW_FOLDER.'header', array(
                                'pagesList' => self::$pagesList,
                                'db_driver' => $this->DB_driver,
                              'rootPage' => $page
                            ));
        $items = Page::findAllFrom('Page', 'parent_id=? ORDER BY '.self::$defaultSorting, array($page->id));

        $filters = Filter::findAll();
        $layouts = Record::findAllFrom('Layout');

        // extracting extended fields
        $extended_fields = array_keys(array_diff_key((array) $page, array_flip(self::$defaultPageFields)));

        $rootItem = new View(self::PLUGIN_REL_VIEW_FOLDER.'itemslist', array(
                'items' => array($page),
                'isRoot' => true,
                'parentUri' => '', //uri of root page = ''
                'showpageparts' => '1', //show page parts by default
                'showcollapsed' => '0', // show expanded by default
                'filters'       => $filters,
                'layouts'       => $layouts,
                'is_frontend'   => false,
                'extended_fields' => $extended_fields,
                ));

        $itemsList = new View(self::PLUGIN_REL_VIEW_FOLDER.'itemslist', array(
                'items' => $items,
                'parentUri' => '', //uri of root page = ''
                'showpageparts' => '1', //show page parts by default
                'showcollapsed' => '0', // show expanded by default
                'filters'       => $filters,
                'layouts'       => $layouts,
                'is_frontend'   => false,
                'extended_fields' => $extended_fields,
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

    public function rename_page_part() {

        if (!AuthUser::hasPermission('multiedit_parts'))
            $this->failure ( __('Insufficent permissions for parts editing'));

        if (empty($_POST['page_id']) || empty($_POST['old_name']) || empty($_POST['new_name'])) {
            $this->failure(__('No data specified'));
            }

        if ( trim($_POST['old_name']) === trim($_POST['new_name']) ) {
            $this->failure(__('Same name specified'));
            }

        if ( strlen(trim($_POST['new_name']))===0 ) {
            $this->failure(__('No name specified'));
            }

        if (  preg_match("/[^a-zA-Z0-9\-\+_\.]/", $_POST['new_name'])===1 ) {
            $this->failure(__('Invalid characters in page part name. Only english letters and + - . _ are allowed'));
            }


        if (Record::existsIn('PagePart', 'page_id=? AND name=?', array($_POST['page_id'],trim($_POST['new_name'])))) {
            $this->failure(__('Page part <b>:new</b> already exists in page id=<b>:page</b>', array(':new'=>trim($_POST['new_name']),':page' => $_POST['page_id'])));
        }
        if ($part = Record::findOneFrom('PagePart', 'page_id=? AND name=?', array($_POST['page_id'],$_POST['old_name']))) {
            $part->name = trim($_POST['new_name']);
            if ($part->save()) {
                $this->success(__('Renamed page part <b>:old</b> to <b>:new</b>', array(':old'=>trim($_POST['old_name']),':new'=>trim($_POST['new_name']))));
            } else {
                $this->failure(__('Error saving new part name'));
            }
        } else {
            $this->failure(__('Page part not found'));
        }
    }

    /**
     * Add field (column) in Page model
     *
     * uses $_POST['template_id']
     * uses $_POST['name']
     *
     */
    public function field_add() {
        // check permissions
        if (!AuthUser::hasPermission('multiedit_advanced'))
            $this->failure ( __('Insufficent permissions for fields manipulation'));

        // check DB driver
        if (!in_array($this->DB_driver,self::$supportedDrivers))
            $this->failure ( __('Unsupported DB driver'));

        // sanitize input
        if ( !isset($_POST['template_id']) )
            $this->failure(__('No field template specified!'));

        $template_id = (int) $_POST['template_id'];

        $fieldnewname = trim($_POST['name']);
        if (preg_match('#^[a-zA-Z_][a-zA-Z0-9_]*$#', $fieldnewname) !== 1)
            $this->failure(__('Invalid target field name!'));

        // strtolower new name
        $fieldnewname = strtolower($fieldnewname);

        // check new name existence
        $page = Record::findOneFrom('Page', '1=1');
        if ( property_exists($page, $fieldnewname) )
            $this->failure ( __('Field already exists in Page model - ') . $fieldnewname );

            /**
             * Do the actual adding
             */

            $PDO = Record::getConnection();

            $trans = array(':field_name:' => $fieldnewname);
            $result = $PDO->exec("ALTER TABLE ".TABLE_PREFIX."page ADD " .
                            strtr(self::$fieldTemplates[$this->DB_driver][$template_id]['query'], $trans)
                    );

            $out = '';
            $out .= '======= ADDING =======';
            $out .= echo_r($PDO->errorInfo(),true);
            $out .= echo_r($result,true);

        $this->success($out);
    }

    /**
     * Rename field (column) in Page model
     *
     * uses $_POST['field_name']
     *      $_POST['field_new_name']
     *
     */
    public function field_rename() {

        // check permissions
        if (!AuthUser::hasPermission('multiedit_advanced'))
            $this->failure ( __('Insufficent permissions for fields manipulation'));

        // check DB driver
        if (!in_array($this->DB_driver,self::$supportedDrivers))
            $this->failure ( __('Unsupported DB driver'));

        // sanitize input
        $fieldname = trim($_POST['field_name']);
        if (empty($fieldname))
            $this->failure(__('No field source field name specified!'));

        $fieldnewname = trim($_POST['field_new_name']);
        if (preg_match('#^[a-zA-Z_][a-zA-Z0-9_]*$#', $fieldnewname) !== 1)
            $this->failure(__('Invalid target field name!'));

        // strtolower new name
        $fieldnewname = strtolower($fieldnewname);

        // check new name existence
        $page = Record::findOneFrom('Page', '1=1');
        if ( property_exists($page, $fieldnewname) )
            $this->failure ( __('Field already exists in Page model - ') . $fieldnewname );

            $PDO = Record::getConnection();

            $stmt1 = $PDO->prepare('describe '.TABLE_PREFIX.'page '.Record::escape( $fieldname ) );
            if (!$stmt1->execute()) {
                $this->failure(__('DB error reading Page table structure!'));
            }

            $structure = $stmt1->fetchObject();

            $out = '';
            $out .= '======= STRUCTURE =======';
            $out .= echo_r($PDO->errorInfo(),true);
            $out .= echo_r($stmt1,true);
            $out .= echo_r($structure,true);


            $nullString = ( (!empty($structure->Null) && (strtolower( $structure->Null) === 'yes' || $structure->Null === '1') ) ) ? ' NULL ' : ' NOT NULL ';
            $defaultString = (!empty($structure->Default)) ? ' DEFAULT '. Record::escape($structure->Default) : '';

            $result = $PDO->exec("ALTER TABLE ".TABLE_PREFIX."page CHANGE " .
                            $structure->Field . ' ' .
                            $fieldnewname . ' ' .
                            $structure->Type .
                            $nullString .
                            $defaultString
                    );

            $out .= '======= RENAMING =======';
            $out .= echo_r($PDO->errorInfo(),true);
            $out .= echo_r($result,true);

        $this->success($out);
    }

    /**
     * Delete field (column) from Page model
     *
     * uses $_POST['field_name']
     *
     */
    public function field_delete() {

        // check permissions
        if (!AuthUser::hasPermission('multiedit_advanced'))
            $this->failure ( __('Insufficent permissions for fields manipulation!'));

        // check DB driver
        if (!in_array($this->DB_driver,self::$supportedDrivers))
            $this->failure ( __('Unsupported DB driver'));

        // sanitize input
        $fieldname = trim($_POST['field_name']);
        if (empty($fieldname))
            $this->failure ( __('No field name specified!'));

        // check for default/protected fields
        if ( in_array($fieldname,self::$defaultPageFields) )
            $this->failure ( __('Cannot delete default fields!'));

        // check field's existence - security reasons
        $page = Record::findOneFrom('Page', '1=1');
        if ( property_exists($page, $fieldname)!==true )
            $this->failure ( __('Field not found in Page model - ') . $fieldname );

            $PDO = Record::getConnection();
            $PDO->exec("ALTER TABLE ".TABLE_PREFIX."page DROP " . $fieldname  );

        $this->success(echo_r($PDO->errorInfo(),true));

    }

    /**
     * Set value of Page model field
     *
     * @return boolean
     */
    public function setvalue() {
		$fieldsAffectingUpdatedOn = array('title','breadcrumb','slug','keywords','description');
		// Page part changes always update "updated_on" field
        	$item = explode('-', $_POST['item']);
		$field = trim($item[0]);
		$identifier = $item[1];
		$value = $_POST['value'];
		$now_datetime = date('Y-m-d H:i:s');
		$messagesExt = array(); //extended messages
		$returnExt = array(); //extended return fields for jquery request
		$needsReloading = '0';

                // BASIC FIELDS PERMISSION CHECK
                if (in_array($field,self::$basicFields)) {
                    if (!AuthUser::hasPermission('multiedit_basic'))
                    $this->failure ( __('Insufficent permissions for editing this field') . ' - ' . $field);
                }
                // PART EDIT PERMISSION CHECK
                elseif ($field==='part') {
                    if (!AuthUser::hasPermission('multiedit_parts'))
                    $this->failure ( __('Insufficent permissions for editing page parts!') );
                }
                // ADVANCED FIELDS PERMISSION CHECK
                else {
                    $page = Page::findById($identifier);
                    $extended_fields = array_keys(array_diff_key((array) $page, array_flip(self::$defaultPageFields)));

                    if (in_array($field,$extended_fields)) {
                        if (!AuthUser::hasPermission('multiedit_advanced'))
                        $this->failure ( __('Insufficent permissions for editing <b>advanced</b> fields!') );
                    } else {
                        $this->failure ( __('Unknown field to edit!' . ' - ' . $field) );
                    }
                }


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
                $pdoResult = Record::update('Page', $toUpdate, 'id=?', array($identifier));

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


    private function respond($message='', $status='OK',$arr=array()) {
        // set messages
        $default = array(
                'message'=>$message,
                'status' => $status,
            );
        // add any additional fields
        $response = array_merge($default, $arr);

        echo json_encode($response);
        die();
    }

    private function success($message,$arr=array()) {
        $this->respond($message, 'OK', $arr);
    }

    private function failure($message,$arr=array()) {
        $this->respond($message, 'error', $arr);
    }

}