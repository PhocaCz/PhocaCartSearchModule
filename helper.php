<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('_JEXEC') or die;

class ModPhocaCartSearchHelper
{
	public static function getAjax() {

		jimport('joomla.application.module.helper');
		if (!JComponentHelper::isEnabled('com_phocacart')) {

			echo '<div class="alert alert-error alert-danger">'.JText::_('Phoca Cart Error') . ' - ' . JText::_('Phoca Cart is not installed on your system').'</div>';
			return;
		}

        JLoader::registerPrefix('Phocacart', JPATH_ADMINISTRATOR . '/components/com_phocacart/libraries/phocacart');
		$lang = JFactory::getLanguage();
		$lang->load('com_phocacart');

		$module = JModuleHelper::getModule('phocacart_search');
		$params = new JRegistry();
		$params->loadString($module->params);

		$search								= new PhocacartSearch();
		$search->ajax               		= 1;
		$search->search_options 			= $params->get( 'search_options', 0 );
		$search->hide_buttons 				= $params->get( 'hide_buttons', 0 );
		$search->display_inner_icon 		= $params->get( 'display_inner_icon', 0 );
		$search->load_component_media 		= $params->get( 'load_component_media', 0 );
		$search->placeholder_text 			= $params->get( 'placeholder_text', '' );
		$search->display_active_parameters 	= $params->get( 'display_active_parameters', 0 );
		
		echo $search->renderSearch();

	}
}
