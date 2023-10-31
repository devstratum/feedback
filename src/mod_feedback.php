<?php
/**
 * @package         Feedback
 * @version         1.1.0
 * @author          Sergey Osipov <info@devstratum.ru>
 * @website         https://devstratum.ru
 * @copyright       Copyright (c) 2023 Sergey Osipov. All Rights Reserved
 * @license         GNU General Public License v2.0
 * @report          https://github.com/devstratum/feedback/issues
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;

/**
 * @param   \Joomla\CMS\WebAsset\WebAssetManager $wa
 * @since   1.0.0
 */
$wa = $app->getDocument()->getWebAssetManager();
$wa->getRegistry()->addRegistryFile('media/mod_feedback/joomla.asset.json');

$wa->useScript('feedback.main');
$wa->useStyle('feedback.main');

$fields_list = $params->get('fields_list');

require ModuleHelper::getLayoutPath('mod_feedback', $params->get('layout', 'default'));