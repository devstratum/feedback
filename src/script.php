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

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Version;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Language\Text;

class mod_feedbackInstallerScript
{
    /**
     * Minimum PHP version required to install the extension
     *
     * @param   string
     * @since   1.0.0
     */
	protected $minimumPhp = '7.4';

    /**
     * Minimum Joomla version required to install the extension
     *
     * @param   string
     * @since   1.0.0
     */
	protected $minimumJoomla = '4.3.0';

    /**
     * Runs right before any installation action
     *
     * @param   string            $type    Type of PostFlight action.
     * @param   InstallerAdapter  $parent  Parent object calling object.
     * @throws
     * @return  boolean True on success. False on failure
     * @since   1.0.0
     */
	public function preflight($type, $parent)
	{
		// Check old Joomla!
		if (!class_exists('Joomla\CMS\Version')) {
			JFactory::getApplication()->enqueueMessage(JText::sprintf('MOD_FEEDBACK_ERROR_COMPATIBLE_JOOMLA',
				$this->minimumJoomla), 'error');

			return false;
		}

		$app = Factory::getApplication();
		$jversion = new Version();

		// Check PHP
		if (!(version_compare(PHP_VERSION, $this->minimumPhp) >= 0)) {
			$app->enqueueMessage(Text::sprintf('MOD_FEEDBACK_ERROR_COMPATIBLE_PHP',
				$this->minimumPhp), 'error');

			return false;
		}

		// Check Joomla version
		if (!$jversion->isCompatible($this->minimumJoomla)) {
			$app->enqueueMessage(Text::sprintf('MOD_FEEDBACK_ERROR_COMPATIBLE_JOOMLA',
				$this->minimumJoomla), 'error');

			return false;
		}

		return true;
	}
}