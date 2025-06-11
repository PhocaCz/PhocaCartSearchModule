<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

defined('_JEXEC') or die;

class ModPhocaCartSearchHelper
{
	public static function getAjax() {

		jimport('joomla.application.module.helper');

        $app = Factory::getApplication();
        $document = Factory::getDocument();

        if (!ComponentHelper::isEnabled('com_phocacart')) {
            echo '<div class="alert alert-error alert-danger">'.Text::_('Phoca Cart Error') . ' - ' . Text::_('Phoca Cart is not installed on your system').'</div>';
			return;
        }
        if (file_exists(JPATH_ADMINISTRATOR . '/components/com_phocacart/libraries/bootstrap.php')) {
            // Joomla 5 and newer
            require_once(JPATH_ADMINISTRATOR . '/components/com_phocacart/libraries/bootstrap.php');
        } else {
            // Joomla 4
            JLoader::registerPrefix('Phocacart', JPATH_ADMINISTRATOR . '/components/com_phocacart/libraries/phocacart');
        }


		$lang = Factory::getLanguage();
		$lang->load('com_phocacart');

		$module = ModuleHelper::getModule('phocacart_search');
		$params = new Registry();
		$params->loadString($module->params);

		$search								= new PhocacartSearch();
		$search->ajax               		= 1;
		$search->search_options 			= $params->get( 'search_options', 0 );
		$search->hide_buttons 				= $params->get( 'hide_buttons', 0 );
		$search->display_inner_icon 		= $params->get( 'display_inner_icon', 0 );
		$search->load_component_media 		= $params->get( 'load_component_media', 1 );
		$search->placeholder_text 			= $params->get( 'placeholder_text', '' );
		$search->display_active_parameters 	= $params->get( 'display_active_parameters', 0 );

		echo $search->renderSearch();

	}
}
