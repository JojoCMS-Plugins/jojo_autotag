<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2007-2008 Harvey Kane <code@ragepank.com>
 * Copyright 2007-2008 Michael Cochrane <mikec@jojocms.org>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package jojo_search
 */

/* Register URI patterns */
Jojo::registerURI("autotag/[action:tag]/", 'Jojo_Plugin_Jojo_Autotag'); // "autotag/query string/"
Jojo::registerURI("autotag/[q:(.*)]", 'Jojo_Plugin_Jojo_Autotag'); // "autotag/query string/"

$_provides['pluginClasses'] = array(
        'Jojo_Plugin_Jojo_Autotag' => 'AutoTag - Search & Tag',
        );
        