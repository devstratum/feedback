<?php
/**
 * @package         Feedback
 * @version         0.63
 * @author          Sergey Osipov <info@devstratum.ru>
 * @website         https://devstratum.ru
 * @copyright       Copyright (c) 2022 Sergey Osipov. All Rights Reserved
 * @license         GNU General Public License v2.0
 * @report          https://github.com/devstratum/feedback/issues
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Devstratum\Module\Feedback\Site\Helper\FeedbackHelper;

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $app->getDocument()->getWebAssetManager();
$wa->getRegistry()->addRegistryFile('media/mod_feedback/joomla.asset.json');

$wa->useScript('feedback.main');
$wa->useStyle('feedback.main');

$fields_list = $params->get('fields_list');

require ModuleHelper::getLayoutPath('mod_feedback', $params->get('layout', 'default'));