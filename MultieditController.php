<?php

/*
 * Wolf CMS - Content Management Simplified. <http://www.wolfcms.org>
 * Copyright (C) 2008-2010 Martijn van der Kleijn <martijn.niji@gmail.com>
 *
 * This file is part of Wolf CMS. Wolf CMS is licensed under the GNU GPLv3 license.
 * Please see license.txt for the full license text.
 */

/* Security measure */
if ( !defined('IN_CMS') ) {
    exit();
}
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
    const GLUE                   = '<br/>';

    public static $messages          = array( );
    private static $pagesList         = array( );
    private static $defaultSorting    = 'position ASC, published_on DESC';
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
    private static $basicFields       = array(
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
    public static $fieldTemplates    = array(
                'mysql'  => array(
                            array( 'description' => 'varchar 10', 'query'       => ':field_name: VARCHAR( 10 )   NOT NULL' ),
                            array( 'description' => 'varchar 20', 'query'       => ':field_name: VARCHAR( 20 )   NOT NULL' ),
                            array( 'description' => 'varchar 32', 'query'       => ':field_name: VARCHAR( 32 )   NOT NULL' ),
                            array( 'description' => 'varchar 64', 'query'       => ':field_name: VARCHAR( 64 )   NOT NULL' ),
                            array( 'description' => 'varchar 255', 'query'       => ':field_name: VARCHAR( 255 )  NOT NULL' ),
                            array( 'description' => 'varchar 1000', 'query'       => ':field_name: VARCHAR( 1000 ) NOT NULL' ),
                            array( 'description' => 'text', 'query'       => ':field_name: TEXT            NOT NULL' ),
                            array( 'description' => 'int 1', 'query'       => ':field_name: INT( 1 )        NOT NULL' ),
                            array( 'description' => 'int 8', 'query'       => ':field_name: INT( 8 )        NOT NULL' ),
                            array( 'description' => 'int 16', 'query'       => ':field_name: INT( 16 )       NOT NULL' ),
                            array( 'description' => 'datetime', 'query'       => ':field_name: DATETIME        NOT NULL' ),
                ),
                'sqlite' => array(
                            array( 'description' => 'TEXT', 'query'       => 'COLUMN :field_name: TEXT          NULL' ),
                            array( 'description' => 'INTEGER', 'query'       => 'COLUMN :field_name: INTEGER       NULL' ),
                            array( 'description' => 'DATETIME', 'query'       => 'COLUMN :field_name: DATETIME      NULL' ),
                ),
    );
    private static $supportedDrivers  = array( 'mysql', 'sqlite' );
    public static $cookie            = array(
                'showrow1'       => true,
                'showrow2'       => true,
                'showrow3'       => true,
                'showrow4'       => true,
                'showpageparts'  => true,
                'useace'         => false,
                'pagepartheight' => 200,
    );

    public static function read_cookies() {
        if ( isset($_COOKIE['MEdit']) ) {
            $cookieExploded = explode('|', $_COOKIE['MEdit']);
            self::$cookie = array(
                        'showrow1'       => (int) $cookieExploded[0],
                        'showrow2'       => $cookieExploded[1],
                        'showrow3'       => ((isset($cookieExploded[2])) ? $cookieExploded[2] : ''),
                        'showrow4'       => !empty($cookieExploded[3]),
                        'showpageparts'  => !empty($cookieExploded[4]),
                        'useace'         => !empty($cookieExploded[5]),
                        'pagepartheight' => !empty($cookieExploded[6]) ? (int) ( $cookieExploded[6] ) : 54,
            );
        }

    }


    public function __construct() {

        if ( !AuthUser::hasPermission('multiedit_view') )
            die('Access denied');

        $this->DB_driver = strtolower(Record::getConnection()->getAttribute(PDO::ATTR_DRIVER_NAME));
        self::read_cookies();
        $this->setLayout('backend');

        $lang = ( $user = AuthUser::getRecord() ) ? strtolower($user->language) : 'en';
        if ( !file_exists(PLUGINS_ROOT . DS . 'multiedit/views/documentation/sidebar/' . $lang . '.php') ) {
            $lang = 'en';
        }
        $sidebarContents = new View(self::PLUGIN_REL_VIEW_FOLDER . 'documentation/sidebar/' . $lang);
        $this->assignToLayout('sidebar', new View(self::PLUGIN_REL_VIEW_FOLDER . 'sidebar', array(
                    'sidebarContents' => $sidebarContents
        )));

    }


    private static function getAllChildren($id) {
        // Prepare SQL
        $sql   = 'SELECT page.* '
                    . 'FROM ' . TABLE_PREFIX . 'page AS page '
                    . 'WHERE parent_id = ' . (int) $id
                    . " ORDER BY page.position, page.id";
        $pages = array( );
        Record::logQuery($sql);
        if ( $stmt  = Record::getConnection()->prepare($sql) ) {
            $stmt->execute();
            while ( $object  = $stmt->fetchObject() )
                $pages[] = $object;
        }

        return $pages;

    }


    private static function countAllChildren($id) {
        // Prepare SQL
        $sql  = 'SELECT COUNT(*) AS nb_rows '
                    . 'FROM ' . TABLE_PREFIX . 'page AS page '
                    . 'WHERE parent_id = ' . (int) $id;
        Record::logQuery($sql);
        $stmt = Record::getConnection()->prepare($sql);
        $stmt->execute();
        return (int) $stmt->fetchColumn();

    }


    public static function makePagesListRecursive($page_id = 1) {
        $children = self::getAllChildren($page_id);
        static $nestLevel; //for storing level, faster than ->level()
        if ( count($children) > 0 ) {
            $nestLevel++;
            foreach ( $children as $childpage ) {
                $childCount = self::countAllChildren($childpage->id);
                if ( $childCount > 0 ) {
                    self::$pagesList[] = array( //add only if there are children
                                'label' => str_replace(" ", "-", str_pad(' ', $nestLevel, " ", STR_PAD_LEFT)) . ' ' . $childpage->breadcrumb,
                                'id'    => $childpage->id,
                                'count' => $childCount,
                    );
                    self::makePagesListRecursive($childpage->id);
                }
            }
            $nestLevel--;
        }

    }


    /**
     * Retrieves single page view
     *
     * {
     * page_id
     * frontend
     * force_full_view
     * }
     */
    public function getoneitem() {
        if ( empty($_POST['page_id']) )
            $this->failure(__('Page ID not specified'));
        $page_id    = (int) $_POST['page_id'];
        $items[]    = Page::findById((int) $page_id); // add one item to array;
        if ( $page_id > 1 )
            $parentPage = Page::findById($items[0]->parent_id);

        $is_frontend     = !empty($_POST['frontend']) && $_POST['frontend'];
        $force_full_view = !empty($_POST['force_full_view']) && $_POST['force_full_view'];

        // extracting extended fields
        //$extended_fields = array_keys(array_diff_key((array) $items[0], array_flip(self::$defaultPageFields)));

        // extracting extended fields
        $pagePublicProperties = $this->_get_object_public_vars($items[0]);
        $extended_fields = array_keys(array_diff_key(array_flip($pagePublicProperties), array_flip(self::$defaultPageFields)));

        $itemsList = new View(self::PLUGIN_REL_VIEW_FOLDER . 'itemslist', array(
                    'items'           => $items,
                    'innerOnly'       => true,
                    'parentUri'       => ( isset($parentPage) ) ? $parentPage->getUri() : '',
                    'is_frontend'     => $is_frontend,
                    'filters'         => Filter::findAll(),
                    'layouts'         => Record::findAllFrom('Layout'),
                    'extended_fields' => $extended_fields,
                    'force_full_view' => $force_full_view,
        ));
        echo $itemsList->render();

    }


//    public function getonepage( $page_id, $showpageparts = 1, $showcollapsed = 0, $is_frontend = 0, $force = 0 ) {
//        $items[] = Page::findById( (int) $page_id ); // add one item to array;
//
//        if ( $page_id > 1 )
//            $parentPage = Page::findById( $items[0]->parent_id );
//
//        // extracting extended fields
//        $extended_fields = array_keys( array_diff_key( (array) $items[0], array_flip( self::$defaultPageFields ) ) );
//
//        $itemsList = new View( self::PLUGIN_REL_VIEW_FOLDER . 'itemslist', array(
//                    'items'           => $items,
//                    'innerOnly'       => true,
//                    'parentUri'       => ( isset( $parentPage ) ) ? $parentPage->getUri() : '',
//                    'is_frontend'     => $is_frontend === '1',
//                    'filters'         => Filter::findAll(),
//                    'layouts'         => Record::findAllFrom( 'Layout' ),
//                    'extended_fields' => $extended_fields,
//                    'force_full_view' => ($force !== '0'),
//                    ) );
//        echo $itemsList->render();
//
//    }


    public function getsubpages($page_id, $sorting = "id", $order = "ASC", $showpageparts = 1, $showcollapsed = 0) {
        if ( $page_id == '-1' ) {
            $page_id     = 1;
            $whereString = 'id <> 1';
            $showAll     = true;
        } else {
            $whereString = 'parent_id=' . Record::escape($page_id);
            $showAll     = false;
        }
        $parentPage = Page::findById($page_id);
        if ( $sorting != '-default-' ) {
            $items = Page::findAllFrom('Page', $whereString . ' ORDER BY ' . $sorting . ' ' . $order);
        } else {
            $items = Page::findAllFrom('Page', $whereString . ' ORDER BY ' . self::$defaultSorting);
        }

        $filters = Filter::findAll();
        $layouts = Record::findAllFrom('Layout');

        // extracting extended fields
//        $extended_fields = array_keys(array_diff_key((array) $parentPage, array_flip(self::$defaultPageFields)));
        $pagePublicProperties = $this->_get_object_public_vars($parentPage);
        $extended_fields = array_keys(array_diff_key(array_flip($pagePublicProperties), array_flip(self::$defaultPageFields)));

        $parentUri = $parentPage->getUri();
        $rootItem  = new View(self::PLUGIN_REL_VIEW_FOLDER . 'itemslist', array(
                    'items'           => array( $parentPage ),
                    'isRoot'          => true,
                    'parentUri'       => isset($parentPage->parent_id) ? mb_substr($parentUri, 0, -mb_strlen(strrchr($parentUri, "/"))) : '', //trim last slash
//                    'showpageparts'   => $showpageparts,
//                    'showcollapsed'   => $showcollapsed,
                    'filters'         => $filters,
                    'layouts'         => $layouts,
                    'is_frontend'     => false,
                    'extended_fields' => $extended_fields,
        ));
        if ( $showAll === true ) {
            $parentUri = false;
        }
        $itemsList = new View(self::PLUGIN_REL_VIEW_FOLDER . 'itemslist', array(
                    'items'           => $items,
                    'rootItem'        => $parentPage,
                    'parentUri'       => $parentUri,
//                    'showpageparts'   => $showpageparts,
//                    'showcollapsed'   => $showcollapsed,
                    'filters'         => $filters,
                    'layouts'         => $layouts,
                    'is_frontend'     => false,
                    'extended_fields' => $extended_fields,
        ));
        echo $rootItem->render();
        echo $itemsList->render();

    }


    public function documentation() {
        // Check for localized documentation or fallback to the default english and display notice
        $lang = ( $user = AuthUser::getRecord() ) ? strtolower($user->language) : 'en';

        if ( !file_exists(PLUGINS_ROOT . DS . 'multiedit' . DS . 'views/documentation/' . $lang . '.php') ) {
            $this->display('multiedit/views/documentation/en');
        }
        else
            $this->display('multiedit/views/documentation/' . $lang);

    }


    private function _get_object_public_vars($obj) {
        $ref    = new ReflectionObject($obj);
        $pros   = $ref->getProperties(ReflectionProperty::IS_PUBLIC);
        $result = array( );
        foreach ( $pros as $pro ) {
            false && $pro  = new ReflectionProperty();
            $name = $pro->getName();
            if ( !startsWith($name, '_') ) {
                $result[] = $name;
            }
        }

        return $result;

    }


    public function index() {
        //$page  = Page::findById(1);
        $page  = Record::findByIdFrom('Page', 1);
        self::makePagesListRecursive($page->id);
        $list  = new View(self::PLUGIN_REL_VIEW_FOLDER . 'header', array(
                    'pagesList' => self::$pagesList,
                    'db_driver' => $this->DB_driver,
                    'rootPage'  => $page
        ));
        $items = Page::findAllFrom('Page', 'parent_id=? ORDER BY ' . self::$defaultSorting, array( $page->id ));

        $filters = Filter::findAll();
        $layouts = Record::findAllFrom('Layout');

        // extracting extended fields
        $pagePublicProperties = $this->_get_object_public_vars($page);
        $extended_fields = array_keys(array_diff_key(array_flip($pagePublicProperties), array_flip(self::$defaultPageFields)));
        //echo_r($extended_fields);

        $rootItem = new View(self::PLUGIN_REL_VIEW_FOLDER . 'itemslist', array(
                    'items'           => array( $page ),
                    'isRoot'          => true,
                    'parentUri'       => '', //uri of root page = ''
                    'showpageparts'   => '1', //show page parts by default
                    'showcollapsed'   => '0', // show expanded by default
                    'filters'         => $filters,
                    'layouts'         => $layouts,
                    'is_frontend'     => false,
                    'extended_fields' => $extended_fields,
        ));

        $itemsList = new View(self::PLUGIN_REL_VIEW_FOLDER . 'itemslist', array(
                    'items'           => $items,
                    'parentUri'       => '', //uri of root page = ''
                    'showpageparts'   => '1', //show page parts by default
                    'showcollapsed'   => '0', // show expanded by default
                    'filters'         => $filters,
                    'layouts'         => $layouts,
                    'is_frontend'     => false,
                    'extended_fields' => $extended_fields,
        ));

        $this->display('multiedit/views/index', array(
                    'pagesList' => $list,
                    'rootItem'  => $rootItem,
                    'itemsList' => $itemsList,
        ));

    }


    public static function checkdatevalid($sDate) {
        if ( (preg_match('/^([0-9]{4})[-_]([0-9]{2})[-_]([0-9]{2}) ([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/D', (string) $sDate, $bits) &&
                    checkdate($bits[2], $bits[3], $bits[1])) == true ) {
            return true;
        }
        else
            return false;

    }


    public function rename_page_part() {
        // check permissions
        if ( !AuthUser::hasPermission('multiedit_parts') )
            $this->failure(__('Insufficent permissions for parts editing'));
        // sanitize input
        if ( empty($_POST['page_id']) || empty($_POST['old_name']) || empty($_POST['new_name']) ) {
            $this->failure(__('No data specified'));
        }
        // sanitize input
        if ( trim($_POST['old_name']) === trim($_POST['new_name']) ) {
            $this->failure(__('Same name specified'));
        }
        // sanitize input
        if ( strlen(trim($_POST['new_name'])) === 0 ) {
            $this->failure(__('No name specified'));
        }
        // sanitize input
        if ( preg_match("/[^a-zA-Z0-9\-\+_\.]/", $_POST['new_name']) === 1 ) {
            $this->failure(__('Invalid characters in page part name. Only alphanumeric characters and + - . _ are allowed.'));
        }

        // check new name existence
        if ( Record::existsIn('PagePart', 'page_id=? AND name=?', array( $_POST['page_id'], trim($_POST['new_name']) )) ) {
            $this->failure(__('Page part <b>:new</b> already exists in page <b>:page</b>', array( ':new'  => trim($_POST['new_name']), ':page' => $_POST['page_id'] )));
        }

        if ( $part = Record::findOneFrom('PagePart', 'page_id=? AND name=?', array( $_POST['page_id'], $_POST['old_name'] )) ) {
            $part->name = trim($_POST['new_name']);
            if ( $part->save() ) {
                $this->success(__('Renamed page part <b>:old</b> to <b>:new</b>', array( ':old' => trim($_POST['old_name']), ':new' => trim($_POST['new_name']) )));
            } else {
                $this->failure(__('Error saving new part name!'));
            }
        } else {
            $this->failure(__('Page part not found'));
        }

    }


    public function add_page_part() {
        // check permissions
        if ( !AuthUser::hasPermission('multiedit_parts') )
            $this->failure(__('Insufficent permissions for parts editing'));
        // sanitize input
        if ( empty($_POST['page_id']) || empty($_POST['name']) ) {
            $this->failure(__('No data specified'));
        }
        // sanitize input
        if ( preg_match("/[^a-zA-Z0-9\-\+_\.]/", $_POST['name']) === 1 ) {
            $this->failure(__('Invalid characters in page part name. Only alphanumeric characters and + - . _ are allowed.'));
        }

        // check new name existence
        if ( Record::existsIn('PagePart', 'page_id=? AND name=?', array( $_POST['page_id'], trim($_POST['name']) )) ) {
            $this->failure(__('Page part <b>:new</b> already exists in page <b>:page</b>', array( ':new'  => trim($_POST['name']), ':page' => $_POST['page_id'] )));
        }

        $part          = new PagePart();
        $part->name    = trim($_POST['name']);
        $part->page_id = (int) trim($_POST['page_id']);
        if ( $part->save() ) {
            $this->success(__('Added page part <b>:new</b>', array( ':new' => trim($_POST['name']) )));
        } else {
            $this->failure(__('Error adding new part!'));
        }

    }


    public function delete_page_part() {
        // check permissions
        if ( !AuthUser::hasPermission('multiedit_parts') )
            $this->failure(__('Insufficent permissions for parts editing'));
        // sanitize input
        if ( empty($_POST['page_id']) || empty($_POST['name']) ) {
            $this->failure(__('No data specified'));
        }
        if ( $part = PagePart::findOneFrom('PagePart', 'page_id=? AND name=?', array( $_POST['page_id'], $_POST['name'] )) ) {

            /**
             * RESTRICT PHP integration
             */
            if (
                        Plugin::isEnabled('restrict_php') &&
                        !AuthUser::hasPermission('edit_parts_php') &&
                        has_php_code($part->content)
            )
                $this->failure(__('<b>Restrict PHP plugin:</b><br/>You don`t have permission to edit parts containing PHP code!'));

            if ( $part->delete() ) {
                $this->success(__('Deleted page part <b>:name</b>', array( ':name' => $_POST['name'] )));
            } else {
                $this->failure(__('Error deleting page part!'));
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
        if ( !AuthUser::hasPermission('multiedit_advanced') )
            $this->failure(__('Insufficent permissions for fields manipulation!'));

        // check DB driver
        if ( !in_array($this->DB_driver, self::$supportedDrivers) )
            $this->failure(__('Unsupported DB driver'));

        // sanitize input
        if ( !isset($_POST['template_id']) )
            $this->failure(__('No field template specified!'));

        $template_id = (int) $_POST['template_id'];

        $fieldnewname = trim($_POST['name']);
        if ( preg_match('#^[a-zA-Z_][a-zA-Z0-9_]*$#', $fieldnewname) !== 1 )
            $this->failure(__('Invalid target field name!'));

        // strtolower new name
        $fieldnewname = strtolower($fieldnewname);

        // check new name existence
        $page = Record::findOneFrom('Page', '1=1');
        if ( property_exists($page, $fieldnewname) )
            $this->failure(__('Field already exists in Page model - ') . $fieldnewname);

        /**
         * Do the actual adding
         */
        $PDO = Record::getConnection();

        $translation_array = array( ':field_name:' => $fieldnewname );
        $PDO->exec("ALTER TABLE " . TABLE_PREFIX . "page ADD " .
                    strtr(self::$fieldTemplates[$this->DB_driver][$template_id]['query'], $translation_array)
        );

        $result = $PDO->errorInfo();
        if ( $result[0] == 0 ) {
            $this->success(__('Successfully added field <b>:field</b> to Page table', array( ':field' => $fieldnewname )));
        } else {
            $this->failure($result[2]);
        }

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
        if ( !AuthUser::hasPermission('multiedit_advanced') )
            $this->failure(__('Insufficent permissions for fields manipulation!'));

        // check DB driver
        if ( !in_array($this->DB_driver, self::$supportedDrivers) )
            $this->failure(__('Unsupported DB driver'));

        // sanitize input
        $fieldname = trim($_POST['field_name']);
        if ( empty($fieldname) )
            $this->failure(__('No source field name specified!'));

        $fieldnewname = trim($_POST['field_new_name']);
        if ( preg_match('#^[a-zA-Z_][a-zA-Z0-9_]*$#', $fieldnewname) !== 1 )
            $this->failure(__('Invalid target field name!'));

        // strtolower new name
        $fieldnewname = strtolower($fieldnewname);

        // check new name existence
        $page = Record::findOneFrom('Page', '1=1');
        if ( property_exists($page, $fieldnewname) )
            $this->failure(__('Field <b>:field</b> already exists in Page model!', array( ':field' => $fieldnewname )));

        $PDO = Record::getConnection();

        // mySQL case
        if ( $this->DB_driver === 'mysql' ) {
            $stmt1 = $PDO->prepare('describe ' . TABLE_PREFIX . 'page ' . Record::escape($fieldname));
            if ( !$stmt1->execute() ) {
                $this->failure(__('DB error reading Page table structure!'));
            }

            $structure = $stmt1->fetchObject();

            //$out = '';
            //$out .= '======= STRUCTURE =======';
            //$out .= print_r( $PDO->errorInfo(), true );
            //$out .= print_r( $stmt1, true );
            //$out .= print_r( $structure, true );
            // recreating row properties
            // TODO: keep indexes and other properties
            $nullString    = ( (!empty($structure->Null) && (strtolower($structure->Null) === 'yes' || $structure->Null === '1') ) ) ? ' NULL ' : ' NOT NULL ';
            $defaultString = (!empty($structure->Default)) ? ' DEFAULT ' . Record::escape($structure->Default) : '';

            $PDO->exec("ALTER TABLE " . TABLE_PREFIX . "page CHANGE " .
                        $structure->Field . ' ' .
                        $fieldnewname . ' ' .
                        $structure->Type .
                        $nullString .
                        $defaultString
            );

            $result = $PDO->errorInfo();
            if ( $result[0] == 0 ) {
                $this->success(__('Successfully renamed field <b>:from</b> to <b>:to</b>!', array( ':from' => $structure->Field, ':to'   => $fieldnewname )));
            } else {
                $this->failure($result[2]);
            }

            //$out .= '======= RENAMING =======';
            //$out .= print_r( $PDO->errorInfo(), true );
            //$out .= print_r( $result, true );
        } else {
            $this->failure('SQLite field rename not supported yet!');
        }

    }


    /**
     * Delete field (column) from Page model
     *
     * uses $_POST['field_name']
     *
     */
    public function field_delete() {

        // check permissions
        if ( !AuthUser::hasPermission('multiedit_advanced') )
            $this->failure(__('Insufficent permissions for fields manipulation!'));

        // check DB driver
        if ( !in_array($this->DB_driver, self::$supportedDrivers) )
            $this->failure(__('Unsupported DB driver'));

        // sanitize input
        $fieldname = trim($_POST['field_name']);
        if ( empty($fieldname) )
            $this->failure(__('No field name specified!'));

        // check for default/protected fields
        if ( in_array($fieldname, self::$defaultPageFields) )
            $this->failure(__('Cannot delete default fields!'));

        // check field's existence - security reasons
        $page = Record::findOneFrom('Page', '1=1');
        if ( property_exists($page, $fieldname) !== true )
            $this->failure(__('Field not found in Page model - ') . $fieldname);

        $PDO = Record::getConnection();

        // mySQL case
        if ( $this->DB_driver === 'mysql' ) {
            $PDO->exec("ALTER TABLE " . TABLE_PREFIX . "page DROP " . $fieldname);
            $result = $PDO->errorInfo();
            if ( $result[0] == 0 ) {
                $this->success('Successfully updated PAGE table');
            } else {
                $this->failure($result[2]);
            }
        }

        // SQLite case
        elseif ( $this->DB_driver === 'sqlite' ) {
            $this->sqlite_table_drop_columns(TABLE_PREFIX . 'page', array( $fieldname ));
            $result = $PDO->errorInfo();
            if ( $result[0] == 0 ) {
                $this->success(__('Successfully deleted field :field', array( ':field' => $fieldname )));
            } else {
                $this->failure($result[2]);
            }
        }

    }


    /**
     * A sort of Brute-force implementation of drop columns for SQLite function
     *
     * @param $table STRING
     * @param $columns ARRAY
     */
    private function sqlite_table_drop_columns($table, $columns) {

        $out = '';

        $sql  = "SELECT sql FROM sqlite_master WHERE type='table' and name='{$table}'";
        $stmt = Record::getConnection()->query($sql);
        if ( $stmt ) {
            $struct = $stmt->fetch(PDO::FETCH_COLUMN, 1);
        }
        else
            $this->failure('Fafiled sqlite_master table query!');


        if ( preg_match('/\(([\s\S\n]+)\)/si', $struct, $match) ) {
            $col_sqls = explode(',', $match[1]);
            $analyzed = array( );
            $stmt     = Record::getConnection()->query("PRAGMA table_info({$table})");
            if ( $stmt ) {
                $cols = $stmt->fetchAll(PDO::FETCH_COLUMN, 1);
                foreach ( $col_sqls as $num => $col_sql ) {
                    $test      = trim(str_replace(array( '"', "'" ), '', $col_sql));
                    $exp       = explode(' ', $test);
                    $col_name  = array_shift($exp);
                    $col_query = implode(' ', $exp);
//                    $out .= '<span style="color: black">' . $num . '</span> - ';
//                    $out .= '<span style="color: red">' . $col_name . '</span> ';
//                    $out .= '<span style="color: blue">' . $col_query . '</span><br/>';
//                    $out .= '<span style="color: green">' . $col_sql . '</span><br/>';
                    if ( in_array($col_name, $cols) ) {
//                    $col_sql = $col_name . ' ' . trim(str_replace ( $col_name, '', $col_sql ));
                        $analyzed[$col_name] = trim($col_sql);
                    }
                }
                if ( empty($analyzed) )
                    $this->failure('Table Page analyze failed!');
            }
            else
                $this->failure('PRAGMA table_info failure');
        }
        else
            $this->failure('Invalid sqlite_master table structure!');

        if ( $analyzed ) {
            $fls = array( );
            foreach ( $analyzed as $key => $definition ) {
                if ( !in_array($key, $columns) )
                    $fls[$key] = $definition;
            }
        }
        else
            $this->failure('Analyzed table empty!');

        $field_list     = implode(', ', array_keys($fls));
        $field_list_sql = implode(', ', array_values($fls));

        $SQL = <<<QUERY
  BEGIN TRANSACTION;
        DROP TABLE IF EXISTS {$table}_bak;
        CREATE TABLE {$table}_bak ( {$field_list_sql} );
        INSERT INTO {$table}_bak SELECT {$field_list} FROM {$table};
        DROP TABLE {$table};
        CREATE TABLE {$table}({$field_list_sql});
        INSERT INTO {$table} SELECT {$field_list} FROM {$table}_bak;
        DROP TABLE {$table}_bak;
  COMMIT;
QUERY;

        $PDO    = Record::getConnection();
        $PDO->exec($SQL);
        //  $out .= '<pre>' . print_r( $SQL, true ) . '</pre>';
        //  $out .= print_r( Record::getConnection()->errorInfo(), true );
        //  $this->success( $out );
        $result = $PDO->errorInfo();
        return ($result === 0);

    }


    public function to_slug() {
        // sanitize input
        if ( empty($_POST['string']) || empty($_POST['string']) ) {
            $this->failure(__('No data specified'));
        }
        $string = $_POST['string'];
        $result = Node::toSlug($string);
        $this->respond($result);
    }

    /**
     * Set value of Page model field
     *
     * @return boolean
     */
    public function setvalue() {
        $fieldsAffectingUpdatedOn = array( 'title', 'breadcrumb', 'slug', 'keywords', 'description' );
        // Page part changes always update "updated_on" field
        $item                     = explode('-', $_POST['item']);
        $field                    = trim($item[0]);
        $ident                    = $item[1];
        $value                    = $_POST['value'];
        $now_datetime             = date('Y-m-d H:i:s');
        $messagesExt              = array( ); //extended messages
        $returnExt                = array( ); //extended return fields for jquery request
        $needsReloading           = '0';

        // BASIC FIELDS PERMISSION CHECK
        if ( in_array($field, self::$basicFields) ) {
            if ( !AuthUser::hasPermission('multiedit_basic') )
                $this->failure(__('Insufficent permissions for editing this field') . ' - ' . $field);
        }
        // PART EDIT PERMISSION CHECK
        elseif ( $field === 'part' ) {
            if ( !AuthUser::hasPermission('multiedit_parts') )
                $this->failure(__('Insufficent permissions for editing page parts!'));
        }
        // ADVANCED FIELDS PERMISSION CHECK
        else {
            $page            = Page::findById($ident);
            $extended_fields = array_keys(array_diff_key((array) $page, array_flip(self::$defaultPageFields)));

            if ( in_array($field, $extended_fields) || $field === 'partfilter' ) {
                if ( !AuthUser::hasPermission('multiedit_advanced') )
                    $this->failure(__('Insufficent permissions for editing <b>advanced</b> fields!'));
            } else {
                $this->failure(__('Unknown field to edit!' . ' - ' . $field));
            }
        }


        if ( $field == 'slug' ) {

            $page    = Record::findOneFrom('Page', 'id=?', array( $ident ));
            $oldslug = $page->slug;
            if ( $ident == 1 ) { //root page protection
                $result = array( 'message'  => __("Slug of root page can't be changed!"),
                            'oldvalue' => $oldslug,
                            'status'   => 'error' );
                echo json_encode($result);
                return false;
            }
            if ( !(bool) preg_match('/^[-a-z0-9_.]++$/D', (string) $value) ) {
                $this->failure(__('Slug cannot be empty and should consist of [a-z] and [_-.] characters!'), array(
                            'oldvalue' => $oldslug,
                            'status'   => 'error' ));
            }
            $exists = Record::countFrom('Page', 'parent_id=? AND slug=?', array( $page->parent_id, $value ));
            if ( $exists > 0 ) {
                $this->failure(__('Other sibling page already has this slug - <b>:slug</b> - restoring original one', array( ':slug' => $value )), array(
                            'oldvalue' => $oldslug,
                            'status'   => 'error' ));
            }
        } elseif ( $field == 'partfilter' ) {
            $tmpval          = explode('_partname_', $_POST['item']);
            $page_id         = substr($tmpval[0], strlen($field) + 1);
            $part_name       = $tmpval[1];
            $part            = PagePart::findOneFrom('PagePart', 'name=? AND page_id=?', array( $part_name, $page_id ));
            $part->filter_id = $value;
            if ( $part->save() ) {
                $this->success(__('Changed filter of <b>:part</b> page part in page <b>:page</b>', array( ':part' => $part->name, ':page' => $page_id )), array(
                            'reloaditem' => '1',
                            'identifier' => $page_id,
                ));
            } else {
                $this->failure(__('Error updating <b>:part</b> page part in page <b>:page</b>', array( ':part' => $part->name, ':page' => $page_id )));
            }
            return false;
        } elseif ( $field == 'part' ) {
            $tmpval        = explode('_partname_', $_POST['item']);
            $page_id       = substr($tmpval[0], strlen($field) + 1);
            $part_name     = $tmpval[1];
            $part          = PagePart::findOneFrom('PagePart', 'name=? AND page_id=?', array( $part_name, $page_id ));
            $part->content = $value;

            /**
             * Notify part_before_save
             * - RESTRICT_PHP integration
             * - PART_REVISIONS integration
             */
            Observer::notify('part_edit_before_save', $part);

            // if part had protected PHP code it will be listed in Flash::get('php_restricted_parts');
            $restrParts = Flash::get('php_restricted_parts');
            if ( count($restrParts) > 0 )
                $this->failure(__('<b>Restrict PHP plugin:</b><br/>You don`t have permission to edit parts containing PHP code!'));

            // if Part Revision was saved it will be listed in Flash::get('page_revisions_saved_parts');
            $savedPart = Flash::get('page_revisions_saved_parts');
            if ( count($savedPart) > 0 )
                $this->appendResult(__('<b>Part Revisions plugin:</b><br/>Revision saved for') . ' <b>' . $savedPart[0] . '</b>');

            // checking if filter actually exists
            if ( !in_array($part->filter_id, Filter::findAll()) && ($part->filter_id != '') ) {
                $msg = __('This page part has invalid filter <b>:filter</b>  set! ', array( ':filter' => $part->filter_id )) . '<br/>';
                $msg = $msg . __('You either dont`t have permissions to use this filter or it`s disabled.');
                $this->failure($msg);
            }

            if ( $part->save() ) {
                $insdata = array(
                            'updated_by_id' => AuthUser::getId(),
                            'updated_on'    => $now_datetime
                );
                Record::update('Page', $insdata, 'id=?', array( $page_id ));

                $this->success(__('Updated <b>:part</b> page part in page <b>:page</b>', array( ':part' => $part->name, ':page' => $page_id )), array(
                            'datetime'   => $now_datetime,
                            'identifier' => $page_id,
                ));
            }
            else
                $this->failure(__('Error updating <b>:part</b> page part in page <b>:page</b>', array( ':part' => $part->name, ':page' => $page_id )));
            return false;
        } elseif ( in_array($field, array( 'created_on', 'published_on' )) ) {
            $correct = MultieditController::checkdatevalid($value);
            if ( !$correct ) {
                $page = Page::findById((int) $ident);
                $this->failure(__('Wrong date - <b>:date</b> - restoring original one', array( ':date' => $value )), array(
                            'oldvalue' => $page->{$field},
                            'status'   => 'error' ));
            } else {
                if ( $now_datetime < $value ) {
                    $this->appendResult('<span class="warning">' . __('Warning: Date of <b>:field</b> is in future!', array( ':field' => $field )) . '</span>');
                };
            }
        } elseif ( $field == 'valid_until' ) {

            if ( trim($value, '-/: ') == '' ) {
                Record::getConnection()->exec("UPDATE " . TABLE_PREFIX . "page SET valid_until=NULL WHERE id=" . (int) $ident);

                $this->success(__('Cleared <b>valid_until</b> field in page: <b>:page</b>', array( ':page' => $ident )), array(
                            'datetime'   => $now_datetime,
                            'identifier' => $ident,
                ));
            };
            $correct = MultieditController::checkdatevalid($value);
            if ( !$correct ) {
                $page = Page::findById((int) $ident);
                $this->failure(__('Wrong date format - <b>:date</b> - restoring original one', array( ':date' => $value )), array(
                            'oldvalue' => $page->{$field},
                ));
            }
            if ( $value < $now_datetime ) {
                Record::getConnection()->exec("UPDATE " . TABLE_PREFIX . "page SET status_id=" . Page::STATUS_ARCHIVED . " WHERE id=" . (int) $ident);
                $this->appendResult('<span class="warning">' . __('Warning: Date of <b>:field</b> is in past! Changed page status to archived!', array( ':field' => $field )) . '</span>');
                $returnExt = array( 'setstatus'  => Page::STATUS_ARCHIVED,
                            'identifier' => $ident,
                );
            }
        } elseif ( $field == 'tags' ) {
            $page = Page::findById((int) $ident);
            $page->setTags($value);
            $this->success(__('Updated <b>tags</b> in page: <b>:page</b>', array( ':page' => $ident )));
        }

        $toUpdate   = array( $field => $value );
        $updateInfo = array( 'updated_by_id' => AuthUser::getId(),
                    'updated_on'    => $now_datetime );

        // add modification time to update array if field affects updated_on
        if ( in_array($field, $fieldsAffectingUpdatedOn) ) {
            $toUpdate = array_merge($toUpdate, $updateInfo);
        }

        // @todo allow NULL values insertion instead of empty strings
        $pdoResult = Record::update('Page', $toUpdate, 'id=?', array( $ident ));

        if ( count($messagesExt) > 0 ) {
            $moreMessages = '<br/>' . implode('<br/>', $messagesExt);
        } else {
            $moreMessages = '';
        }

        $result   = array_merge(
                    array( 'message' => __('Updated field <b>:field</b> in page <b>:page</b>', array( ':field' => $field, ':page'  => $ident )) . $moreMessages,
                    'status'  => 'OK' ), $returnExt // add extended return
        );
        $timeInfo = array( 'datetime'   => $now_datetime,
                    'identifier' => $ident );

        // add modification time to return array if field affects updated_on
        if ( in_array($field, $fieldsAffectingUpdatedOn) ) {
            $result = array_merge($result, $timeInfo);
        }
        //echo json_encode( $result );
        $this->success(__('Success!'), $result);
        return false;

    }


    /**
     *
     * @param type $message
     * @param type $status
     * @param type $arr
     */
    public static function respond($message = '', $status = 'OK', $arr = array( )) {
        // set messages
        $msg_text = (count(self::$messages) > 0) ? implode(self::GLUE, self::$messages) . self::GLUE . $message : $message;
        $default  = array(
                    'message'  => $msg_text,
                    'exe_time' => execution_time(),
                    'mem_used' => memory_usage(),
                    'status'   => $status,
        );

        // add any additional fields
        $response = array_merge($default, $arr);

        echo json_encode($response);
        if ( $status !== 'OK' )
            header("HTTP/1.0 404 Not found");
        die();

    }


    public static function success($message, $arr = array( )) {
        self::respond($message, 'OK', $arr);

    }


    public static function appendResult($message) {
        self::$messages[] = $message;

    }


    public static function failure($message, $arr = array( )) {
        self::respond($message, 'error', $arr);

    }


}