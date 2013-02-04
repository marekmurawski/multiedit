<?php

/* Security measure */
if ( !defined( 'IN_CMS' ) ) {
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

AutoLoader::addFolder(PLUGINS_ROOT.'/multiedit/lib');
AutoLoader::load('mmInstaller');

$success = true;
$success = $success && mmInstaller::createPermission( 'multiedit_view' );
$success = $success && mmInstaller::createPermission( 'multiedit_basic' );
$success = $success && mmInstaller::createPermission( 'multiedit_advanced' );
$success = $success && mmInstaller::createPermission( 'multiedit_parts' );
$success = $success && mmInstaller::createPermission( 'multiedit_frontend' );

$success = $success && mmInstaller::createRole( 'multieditor' );

$success = $success && mmInstaller::assignPermissionToRole( 'multiedit_view',     'multieditor' );
$success = $success && mmInstaller::assignPermissionToRole( 'multiedit_basic',    'multieditor' );
$success = $success && mmInstaller::assignPermissionToRole( 'multiedit_advanced', 'multieditor' );
$success = $success && mmInstaller::assignPermissionToRole( 'multiedit_parts',    'multieditor' );
$success = $success && mmInstaller::assignPermissionToRole( 'multiedit_frontend', 'multieditor' );

$success = $success && mmInstaller::assignPermissionToRole( 'multiedit_view',     'editor' );
$success = $success && mmInstaller::assignPermissionToRole( 'multiedit_basic',    'editor' );
$success = $success && mmInstaller::assignPermissionToRole( 'multiedit_frontend', 'editor' );

$success = $success && mmInstaller::assignPermissionToRole( 'multiedit_view',     'developer' );
$success = $success && mmInstaller::assignPermissionToRole( 'multiedit_basic',    'developer' );
$success = $success && mmInstaller::assignPermissionToRole( 'multiedit_parts',    'developer' );
$success = $success && mmInstaller::assignPermissionToRole( 'multiedit_frontend', 'developer' );

if ( $success ) {
    Flash::set( 'success', __( 'Successfully activated plugin' ) . ' ' . 'MultiEdit' );
    if ( !empty( mmInstaller::$infoMessages ) ) {
        Flash::set( 'info', implode( '<br/>', mmInstaller::$infoMessages ) );
    }
}
else {
    Flash::set( 'error', __( 'Problems occured while activating plugin ' ) . ' MultiEdit:<br/>' .
            implode( '<br/>', mmInstaller::$errorMessages ) );
    if ( !empty( mmInstaller::$infoMessages ) ) {
        Flash::set( 'info', implode( '<br/>', mmInstaller::$infoMessages ) );
    }
}

exit();