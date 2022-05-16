<?php
/**
 * @package         Feedback
 * @version         0.72
 * @author          Sergey Osipov <info@devstratum.ru>
 * @website         https://devstratum.ru
 * @copyright       Copyright (c) 2022 Sergey Osipov. All Rights Reserved
 * @license         GNU General Public License v2.0
 * @report          https://github.com/devstratum/feedback/issues
 */

namespace Devstratum\Module\Feedback\Site\Helper;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Input\Json;
use Joomla\CMS\Language\Text;

/**
 * Helper for mod_feedback
 *
 */
class FeedbackHelper
{
    /**
     * Method get ajax
     *
     * @throws
     */
    public static function getAjax()
    {
        $message = [];
        $response = [];

        $app = Factory::getApplication();
        $app->getLanguage()->load('mod_feedback', JPATH_SITE);
        $config = Factory::getConfig();
        $mailer = Factory::getMailer();

        // Add json input
        if ($data = new Json())
        {
            foreach ($data->getArray() as $name => $value)
            {
                $app->input->def($name, $value);
            }
        }

        // Mod params
        $mod_id = $app->input->getInt('mod_id');
        $mod = ModuleHelper::getModuleById((string) $mod_id);
        $mod_params = json_decode($mod->params);

        // Get fields
        $fields_form = $app->input->getRaw('fields');
        $fields_list = $mod_params->fields_list;
        $fields_array = [];

        if ($mod_params && $fields_form) {
            // Prepare fields
            foreach ($fields_list as $key => $item) {
                $fields_array[$key] = [
                    'label' => $item->field_label,
                    'placeholder' => $item->field_placeholder,
                    'type' => $item->field_type,
                    'required' => $item->field_required,
                    'value' => trim(htmlspecialchars(stripslashes($fields_form[$key])))
                ];
            }

            // Validation
            $errors = FeedbackHelper::validationFields($fields_array);

            if (!$errors) {
                $message = ['mod_id' => $mod_id, 'type' => 'success', 'text' => 'form ready'];
                $response = ['mod_id' => $mod_id, 'errors' => $errors];
            } else {
                $message = ['mod_id' => $mod_id, 'type' => 'warning', 'text' => Text::_('MOD_FEEDBACK_ERROR_FIELDS')];
                $response = ['mod_id' => $mod_id, 'errors' => $errors];
            }

        } else {
            $message = ['mod_id' => $mod_id, 'type' => 'danger', 'text' => Text::_('MOD_FEEDBACK_ERROR_MODULE')];
        }

        FeedbackHelper::setResponse($response, $message);
    }

    /**
     * Method validation fields
     *
     * @param array $fields_array
     * @throws
     *
     * @return  array
     */
    public static function validationFields($fields_array)
    {
        $errors = [];
        foreach ($fields_array as $key => $item) {
            if (!$item['value'] && $item['required']) {
                $errors[] = [
                    'key' => $key,
                    'label' => $item['label'],
                    'error' => Text::_('MOD_FEEDBACK_ERROR_REQUIRED')
                ];
            }

            if ($item['value']) {
                switch($item['type']) {
                    case 'email':
                        if (!preg_match('/[0-9a-z_]+@[0-9a-z_^\.]+\.[a-z]{2,3}/i', $item['value'])) {
                            $errors[] = [
                                'key' => $key,
                                'label' => $item['label'],
                                'error' => Text::_('MOD_FEEDBACK_ERROR_EMAIL')
                            ];
                        }
                        break;
                    case 'number':
                        if (!is_numeric($item['value'])) {
                            $errors[] = [
                                'key' => $key,
                                'label' => $item['label'],
                                'error' => Text::_('MOD_FEEDBACK_ERROR_NUMBER')
                            ];
                        }
                        break;
                    case 'date':
                        if (!strtotime($item['value'])) {
                            $errors[] = [
                                'key' => $key,
                                'label' => $item['label'],
                                'error' => Text::_('MOD_FEEDBACK_ERROR_DATE')
                            ];
                        }
                        break;
                }
            }
        }

        return $errors;
    }

    /**
     * Method set response
     *
     * @param array $response
     * @param array $message
     *
     * @throws
     */
    public static function setResponse($response, $message)
    {
        $app = Factory::getApplication();
        header('Content-Type: application/json');
        echo new JsonResponse($response, $message);
        $app->close();
    }
}