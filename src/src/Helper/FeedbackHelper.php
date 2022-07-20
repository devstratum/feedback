<?php
/**
 * @package         Feedback
 * @version         1.0.4
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
 */
class FeedbackHelper
{
    /**
     * Method get Ajax
     *
     * @throws
     */
    public static function getAjax()
    {
        $message = [];
        $response = [];

        $app = Factory::getApplication();
        $app->getLanguage()->load('mod_feedback', JPATH_SITE);

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
            $fields_errors = FeedbackHelper::validationFields($fields_array);

            if (!$fields_errors) {
                // Send mail
                $mailer_error = FeedbackHelper::sendMail($mod_params, $fields_array);

                if (!$mailer_error) {
                    $message = ['type' => 'success', 'text' => $mod_params->form_success];
                } else {
                    $message = ['type' => 'danger', 'text' => $mailer_error];
                }
            } else {
                $message = ['type' => 'warning', 'text' => Text::_('MOD_FEEDBACK_ERROR_FIELDS')];
                $response = ['mod_id' => $mod_id, 'errors' => $fields_errors];
            }
        } else {
            $message = ['type' => 'danger', 'text' => Text::_('MOD_FEEDBACK_ERROR_MODULE')];
        }

        FeedbackHelper::setResponse($response, $message);
    }

    /**
     * Method send Mail
     *
     * @param   object $mod_params
     * @param   array $fields_array
     * @throws
     *
     * @return  mixed
     */
    public static function sendMail($mod_params, $fields_array)
    {
        $error = '';
        $config = Factory::getConfig();
        $mailer = Factory::getMailer();

        if ($config->get('mailonline')) {

            $output = '';

            // Prepare mail output
            if (!$mod_params->form_title) {
                $mod_params->form_title = Text::_('MOD_FEEDBACK_TITLE_DEFAULT');
            }

            if ($mod_params->mail_header) {
                $output .= $mod_params->mail_header;
            }

            foreach ($fields_array as $item) {
                if (!empty($item['value'])) {
                    $output .= '<p><b>' . $item['label'] . ': </b><br/>' . $item['value'] . '</p>'. "\n";
                }
            }

            if ($mod_params->mail_footer) {
                $output .= $mod_params->mail_footer;
            }

            // Create email
            $mailer->CharSet = 'utf-8';
            $mailer->isHTML(true);
            $mailer->setFrom($config->get('mailfrom'), $config->get('fromname'));
            $mailer->Subject = $mod_params->form_title . ' - ' . $config->get('sitename');

            if ($mod_params->form_email_to) {
                $mailer->addAddress($mod_params->form_email_to);
            } else {
                $mailer->addAddress($config->get('mailfrom'));
            }

            if ($mod_params->form_email_copy) {
                $mailer->AddCC($mod_params->form_email_copy);
            }

            if ($mod_params->form_email_admin) {
                $mailer->AddBCC($config->get('mailfrom'));
            }

            $mailer->msgHTML($output);

            // Send email
            if (!$mailer->send()) {
                $error = Text::_('MOD_FEEDBACK_ERROR_MAILER_ERROR' . ' :: ' . $mailer->ErrorInfo);
            }
        } else {
            $error = Text::_('MOD_FEEDBACK_ERROR_MAILER_DISABLE');
        }

        return $error;
    }

    /**
     * Method validation Fields
     *
     * @param   array $fields_array
     * @throws
     *
     * @return  array
     */
    public static function validationFields($fields_array)
    {
        $mailer = Factory::getMailer();

        $errors = [];
        foreach ($fields_array as $key => $item) {
            if (empty($item['value']) && $item['required']) {
                $errors[] = [
                    'key' => $key,
                    'label' => $item['label'],
                    'error' => Text::_('MOD_FEEDBACK_ERROR_REQUIRED')
                ];
            }

            if (!empty($item['value'])) {
                switch($item['type']) {
                    case 'email':
                        if (!$mailer->validateAddress($item['value'])) {
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
     * Method set Response
     *
     * @param   array $response
     * @param   array $message
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