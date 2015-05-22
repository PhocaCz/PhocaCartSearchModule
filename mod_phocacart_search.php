<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
 
defined('_JEXEC') or die('Restricted access');// no direct access

if (!JComponentHelper::isEnabled('com_phocacart', true)) {
	return JError::raiseError(JText::_('Phoca Cart Error'), JText::_('Phoca Cart is not installed on your system'));
}
if (! class_exists('PhocaCartLoader')) {
    require_once( JPATH_ADMINISTRATOR.'/components/com_phocacart/libraries/loader.php');
}

phocacartimport('phocacart.utils.settings');
phocacartimport('phocacart.search.search');
phocacartimport('phocacart.filter.filter');
phocacartimport('phocacart.path.route');
phocacartimport('phocacart.render.renderjs');

$lang 						= JFactory::getLanguage();
$lang->load('com_phocacart');
$document					= JFactory::getDocument();

$search						= new PhocaCartSearch();

/* See documentation of Phoca Cart Filter module - commented code for description of functions:
 * - phSetFilter
 * - phRemoveFilter
 *
 * We can search in both ways:
 * - all products (ignoring selected filters) - url will be set to basic filter page with help of is isItemsView
 *   We use variable isItemsView in two different cases
 *   1) in filter function - we use module, this means we can stay on different site that items view page
 *      and when we are on different site the javascript will redirect to items view (to default view without any parameters)
 *      We can select only one filtering parameter on other than items view page, then we will be always redirected to
 *      items view and there we can set other parameters - leaving items view, we will lose all selected params as they are GET
 *   2) See 1) we can use the 1) for search feature - In search we use two parameters: search all products and search filtered products
 *      When user clicks on search all products, in fact we need to delete all previously set filter parameters - in fact we need
 *      default items view and such we can get with "isItemsView = 0" - this means we simulate that we are not on items view (but we can)
 *      but when we want to search without all previously set filter parameters we will simulate other view to get default items view page
 *      Default items view page  means an items view without any paramaters
 *      This is this case: .'       phSetFilter(param, value, 0, urlItemsView, 1, 0);' - zero after value means not items view;
 * - only selected products (filtered products - they are filtered by filter parameter) - url stay the same with filter parameters
 */ 

$document->addScript(JURI::root(true).'/media/com_phocacart/js/filter/jquery.ba-bbq.min.js');
$document->addScript(JURI::root(true).'/media/com_phocacart/js/filter/filter.js');
$isItemsView 				= PhocaCartRoute::isItemsView();
$urlItemsView 				= PhocaCartRoute::getJsItemsRoute();// With category
$urlItemsViewWithoutParams 	= PhocaCartRoute::getJsItemsRouteWithoutParams();// Without category

$p['search_options']		= $params->get( 'search_options', 0 );

//$jsPart1 = 'var currentUrlParams	= jQuery.param.querystring();'
//		  .'document.location 		= jQuery.param.querystring(urlItemsView, currentUrlParams, 2);';
//$jsPart1 = 'document.location 		= urlItemsView';
$jsPart2 = PhocaCartRenderJs::renderLoaderFullOverlay();

$js	  = array();	
$js[] = 'function phChangeSearch(param, value, formAction) {';
$js[] = '   var isItemsView		= '.(int)$isItemsView.';';
$js[] = '   var urlItemsView	= \''.$urlItemsView.'\';';
$js[] = '   ';
//$js[] = '   value = phEncode(value);';
$js[] = '   if (formAction == 1) {';

if ($p['search_options'] == 1) {
	$js[] = '      if(jQuery("#phSearchSearchAllProducts").attr(\'checked\')) {';
	$js[] = '         urlItemsView = \''.$urlItemsViewWithoutParams.'\';';
	$js[] = '         isItemsView = 0;'; // When options are enabled and searching is set to all - we search without filtering
	$js[] = '      }';
} else {
	$js[] = '         isItemsView = 0;';// When options are disabled we always search without filtering
}

$js[] = '     phSetFilter(param, value, isItemsView, urlItemsView, 1, 0);';
$js[] = '   } else {';
$js[] = '      phRemoveFilter(param, value, isItemsView, urlItemsView, 1,0);';
$js[] = '   }';
$js[] = '	'.$jsPart2;
$js[] = '}';

$document->addScriptDeclaration(implode("\n", $js));
require(JModuleHelper::getLayoutPath('mod_phocacart_search'));
?>