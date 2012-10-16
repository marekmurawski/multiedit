<?php
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

/* Security measure */
if (!defined('IN_CMS')) { exit(); }

Plugin::setInfos(array(
		'id'          => 'multiedit',
		'title'       => 'MultiEdit',
		'description' => __('Provides convenient interface to quickly edit multiple pages metadata.'),
		'version'     => '0.1.1',
			'license'     => 'GPL',
			'author'      => 'Marek Murawski',
		'website'     => 'http://marekmurawski.pl/',
		'update_url'  => 'http://marekmurawski.pl/static/wolfplugins/plugin-versions.xml',
		'require_wolf_version' => '0.7.3' // 0.7.5SP-1 fix -> downgrading requirement to 0.7.3
));

if (defined('CMS_BACKEND')) {
	Plugin::addController('multiedit', 'MultiEdit', 'administrator');
	Plugin::addJavascript('multiedit', 'js/helpers.js');
}
else {
	function getMultiEdit($page_id) {
		$frontView = new View('../../plugins/multiedit/views/frontend/editor', array('page_id'=>$page_id));
		echo $frontView;
	}
}
