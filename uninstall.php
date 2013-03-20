<?php

/* Security measure */
if ( !defined('IN_CMS') ) {
    exit();
}

/**
 * Restrict PHP Plugin for Wolf CMS.
 * Provides PHP code restriction in page parts based on roles and/or permissions
 *
 *
 * @package Plugins
 * @subpackage restrict_php
 *
 * @author Marek Murawski <http://marekmurawski.pl>
 * @copyright Marek Murawski, 2012
 * @license http://www.gnu.org/licenses/gpl.html GPLv3 license
 */
AutoLoader::addFolder(PLUGINS_ROOT . '/multiedit/lib');
AutoLoader::load('mmInstaller');

$success = true;


$success = $success && mmInstaller::deletePermission('multiedit_view');
$success = $success && mmInstaller::deletePermission('multiedit_basic');
$success = $success && mmInstaller::deletePermission('multiedit_advanced');
$success = $success && mmInstaller::deletePermission('multiedit_parts');
$success = $success && mmInstaller::deletePermission('multiedit_frontend');

$success = $success && mmInstaller::deleteRole('multieditor');

if ( $success ) {
    Flash::set('success', __('Successfully deactivated plugin') . ' ' . 'MultiEdit');
    if ( !empty(mmInstaller::$infoMessages) ) {
        Flash::set('info', implode('<br/>', mmInstaller::$infoMessages));
    }
} else {
    Flash::set('error', __('Problems occured while deactivating plugin ') . ' MultiEdit:<br/>' .
                implode('<br/>', mmInstaller::$errorMessages));
    if ( !empty(mmInstaller::$infoMessages) ) {
        Flash::set('info', implode('<br/>', mmInstaller::$infoMessages));
    }
}

exit();