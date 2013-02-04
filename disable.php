<?php

/* Security measure */
if (!defined('IN_CMS')) {
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


    Flash::set( 'success', __( 'Successfully deactivated plugin' ) . ' ' . 'MultiEdit' );


exit();