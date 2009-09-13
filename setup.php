<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2007-2008 Harvey Kane <code@ragepank.com>
 * Copyright 2007-2008 Michael Holt <code@gardyneholt.co.nz>
 * Copyright 2007 Melanie Schulz <mel@gardyneholt.co.nz>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @author  Melanie Schulz <mel@gardyneholt.co.nz>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package jojo_search
 */

/* Site Autotag Page */
$data = Jojo::selectQuery("SELECT * FROM {page} WHERE pg_link = 'Jojo_Plugin_Jojo_Autotag'");
if (!count($data)) {
    echo "Adding <b>Site Autotag</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Autotag', pg_link = 'Jojo_Plugin_Jojo_Autotag', pg_url = 'autotag', pg_parent = ?, pg_order=0, pg_mainnav='no', pg_footernav='no',  pg_sitemapnav='no', pg_xmlsitemapnav='no', pg_body = '', pg_permissions = \"everyone.show = 0\neveryone.view = 0\nadmin.show = 1\nadmin.view = 1\"", array($_NOT_ON_MENU_ID));
}