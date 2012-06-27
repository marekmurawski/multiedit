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

Plugin::setInfos(array(
    'id'          => 'multiedit',
    'title'       => 'MultiEdit',
    'description' => __('Provides convenient interface to quickly edit multiple pages metadata.'),
    'version'     => '0.0.1',
   	'license'     => 'GPL',
	'author'      => 'Marek Murawski',
    'website'     => 'http://marekmurawski.pl/',
    //'update_url'  => 'http://www.wolfcms.org/plugin-versions.xml',
    'require_wolf_version' => '0.7.3'
));

Plugin::addController('multiedit', 'MultiEdit', 'administrator');

