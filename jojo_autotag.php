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

class Jojo_Plugin_Jojo_Autotag extends Jojo_Plugin
{
    function _getContent()
    {
        global $smarty, $_USERGROUPS;

        /* Remove dashs from url rewriting */
        $keywords = urldecode(str_replace('-', ' ', Jojo::getFormData('q', '')));

        /* Get Search Type */
        $searchtype = Jojo::getFormData('type', isset($_SESSION['jojo_search_type']) ? $_SESSION['jojo_search_type'] : '');
        $smarty->assign('searchtype', $searchtype);

        /* Get Search Language */
        $language = Jojo::getFormData('l', isset($_SESSION['jojo_search_language']) ? $_SESSION['jojo_search_language'] : '');
        $smarty->assign('language', $language);


        /* Setup Page content */
        $breadcrumbs = $this->_getBreadCrumbs();
        $content['title']    = 'Autotag';
        $content['seotitle'] = 'Site Autotag';

         if (Jojo::getFormData('term')) {
         $keywords = Jojo::getFormData('term');
            foreach (Jojo::getFormData('updates', array()) as $id => $do) {
                $request = explode(',',$id);
                if ($do == 'delete'){
                    /* Delete existing tags for this item */
                    Jojo_Plugin_Jojo_Tags::deleteTags($request[1], $request[0], $keywords);
                    
                } elseif ($do=='add') {
                    /* Save all the new tags */
                    Jojo_Plugin_Jojo_Tags::saveTag($keywords, $request[1], $request[0]);
                }
            }
        }
        

       if (strlen($keywords)) {
            /* Seperate keywords */
            $keywords       = explode(' ', $keywords);
            $keywords_str   = implode(' ', $keywords);
            $keywords_clean = implode('-', $keywords);
            $displaykeywords = htmlspecialchars($keywords_str, ENT_COMPAT, 'UTF-8', false);

            $booleanphrase = false;

            if ($searchtype == 'phrase') {
                $booleankeyword_str = '"' . $keywords_str . '"';
                $booleanphrase = true;
            } elseif ($searchtype == 'all') {
                $booleankeyword_str = '+' . implode(' +', $keywords);
            } else {
                $booleankeyword_str = '';
            }

            /* Add Search Results bread crumb */
            $breadcrumb = array();
            $breadcrumb['name']     = ucfirst($displaykeywords);
            $breadcrumb['rollover'] = sprintf('Search Results for "%s"', $displaykeywords);
            $breadcrumb['url']      = parent::getCorrectUrl() . htmlspecialchars($keywords_clean, ENT_COMPAT, 'UTF-8', false);
            $breadcrumbs[]          = $breadcrumb;

            /* Set page title */
            $content['title']    = $displaykeywords . ' - Search Results';
            $content['seotitle'] = sprintf('%s | Search Results', ucfirst($displaykeywords));

       
            /* Get results from plugins */
            $results = array();
            $results = Jojo::applyFilter('jojo_search', $results, $keywords, $language, $booleankeyword_str);
            $resulttypes = array();

            /* Sort the results by relevance */
           usort($results, array('Jojo_Plugin_Jojo_Search', '_cmp_results'));

            /* Convert the Body Text to a non-html snippet */
            foreach ($results as $k => $res) {
                $body = strip_tags($res['body']);
                 /* Add result type if not added already */
                if (!in_array($res['type'], $resulttypes)) $resulttypes[] = $res['type'];
                 $results[$k]['title']  = htmlspecialchars($res['title'], ENT_COMPAT, 'UTF-8', false);
                /* Make keywords bold */
                $results[$k]['body'] = Jojo_Plugin_Jojo_search::search_format_content($body, $keywords_str, $booleanphrase );

                /* De-encode foreign text urls for display */
                $results[$k]['displayurl'] = empty($res['displayurl']) ? urldecode($res['url']) : $res['displayurl'];

                /* Use relevance figure (x10) as a pixel width for displaying the relevance graphically */
                $results[$k]['displayrelevance'] = !empty($res['relevance']) ? ($res['relevance'] * 10 ) : '0';
            }

           /* Reverse the result order for highest relevance first */
           $results = array_reverse($results);

            /* Assign smarty variables */
            $smarty->assign('numresults', count($results));
            $smarty->assign('keywords',   $displaykeywords);
            $smarty->assign('displaykeywords',  $displaykeywords);
            $smarty->assign('results',    $results);
        }

        if (_MULTILANGUAGE) {
            /* Get list of languages for drop down */
            $languages = array();
            foreach (Jojo::selectQuery("SELECT * FROM {language} WHERE active!='no' ORDER BY english_name") as $l) {
                $languages[$l['languageid']] = $l['name'];
            }
            $smarty->assign('languages', $languages);
        }

        /* Get page content */
        $smarty->assign('searchurl', parent::getCorrectUrl());
        $smarty->assign('resulttypes', $resulttypes);
        $content['breadcrumbs'] = $breadcrumbs;
        $content['content']     = $smarty->fetch('jojo_autotag.tpl');

        return $content;
    }


    function getCorrectUrl()
    {
        global $page;
        $pagelanguage = (_MULTILANGUAGE) ? $page->page['pg_language'] . '/' : '';

        /* Include any get variables in request_uri, this allows for rewrites */
        $t = strstr($_SERVER['REQUEST_URI'], '?');
        parse_str(substr($t, 1), $get);
        $_GET = array_merge($_GET, $get);

        $searchtype = Jojo::getFormData('type', isset($_SESSION['jojo_search_type']) ? $_SESSION['jojo_search_type'] : '');
        $_SESSION['jojo_search_type'] = $searchtype;

        $l = Jojo::getFormData('l', isset($_SESSION['jojo_search_language']) ? $_SESSION['jojo_search_language'] : '');
        $_SESSION['jojo_search_language'] = $l;

        $q = Jojo::getFormData('q');
        if ($q) {

            if (!preg_match('/^([a-zA-Z0-9 -]*)$/', $q)) {
                return _SITEURL . '/' . $pagelanguage . 'autotag/?q=' . urlencode($q);
            }


            /* Remove dashs from url rewriting */
            $keywords = str_replace('-', ' ', $q);

            /* Separate keywords */
            $keywords = explode(' ', $keywords);

            $correcturl =  $pagelanguage . 'autotag/' . implode('-', $keywords) . '/';
            if ($correcturl) {
                return _SITEURL . '/' . $correcturl;
                exit;
            }

            return parent::getCorrectUrl();
        }

        return parent::getCorrectUrl();
    }
    
}
          
