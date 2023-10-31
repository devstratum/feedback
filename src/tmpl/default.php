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

/**
 * @param object $module
 * @param object $fields_list
 */

$disabled = '';

?>

<div class="mod-feedback mod-feedback-<?php echo $params->get('form_theme'); ?>" id="mod_feedback_<?php echo $module->id; ?>" data-form-id="<?php echo $module->id; ?>">
    <div class="mod-feedback__form">
        <?php if ($params->get('form_titles')): ?>
            <div class="mod-feedback__title">
                <span><?php echo $params->get('form_title'); ?></span>
            </div>
        <?php endif; ?>

        <?php if (trim((string) $params->get('form_header'))): ?>
            <div class="mod-feedback__header">
                <?php echo $params->get('form_header'); ?>
            </div>
        <?php endif; ?>

        <div class="mod-feedback__fields">
            <?php $ii = 0; foreach ($fields_list as $key => $item): ?>
                <?php
                // check required
                $required = '';
                if ($item->field_required && $item->field_type != 'hidden') {
                    $required = ' required';
                } elseif ($item->field_type == 'hidden')  {
                    $required = ' hidden';
                }
                ?>
                <div class="mod-feedback__field feedback-field_<?php echo $key; ?><?php echo $required; ?>">
                    <?php
                    // check placeholder
                    $placeholder = '';
                    if ($params->get('form_placeholders')) {
                        $placeholder = $item->field_placeholder ? ' placeholder="' . $item->field_placeholder . '"' : ' placeholder="' . $item->field_label . '"';
                    }
                    ?>

                    <?php if ($params->get('form_labels') && $item->field_type != 'hidden'): ?>
                        <div class="mod-feedback__label"><?php echo $item->field_label; ?></div>
                    <?php endif; ?>

                    <div class="mod-feedback__input">
                        <?php
                        switch ($item->field_type) {
                            case 'text':
                                echo '<input type="text" name="' . $key . '" class="mod-input mod-input-text"' . $required . $placeholder . '/>';
                                break;
                            case 'tel':
                                echo '<input type="tel" name="' . $key . '" class="mod-input mod-input-tel"' . $required . $placeholder . '/>';
                                break;
                            case 'email':
                                echo '<input type="text" name="' . $key . '" class="mod-input mod-input-email"' . $required . $placeholder . '/>';
                                break;
                            case 'number':
                                echo '<input type="number" name="' . $key . '" class="mod-input mod-input-number"' . $required . $placeholder . '/>';
                                break;
                            case 'date':
                                echo '<input type="date" name="' . $key . '" class="mod-input mod-input-date"' . $required . $placeholder . '/>';
                                break;
                            case 'textarea':
                                echo '<textarea name="' . $key . '" class="mod-input mod-input-textarea"' . $required . $placeholder . '></textarea>';
                                break;
                            case 'hidden':
                                echo '<input type="hidden" name="' . $key . '" class="mod-input mod-input-hidden"/>';
                                break;
                        }
                        ?>
                    </div>
                    <div class="mod-feedback__error"></div>
                </div>
            <?php $ii++; endforeach; ?>
        </div>

        <?php if (trim((string) $params->get('form_footer'))): ?>
            <div class="mod-feedback__footer">
                <?php echo $params->get('form_footer'); ?>
            </div>
        <?php endif; ?>

        <div class="mod-feedback__alert"></div>

        <?php if ($params->get('form_privacy')): ?>
            <?php $disabled = ' disabled'; ?>
            <div class="mod-feedback__privacy">
                <div class="privacy-checkbox" data-form-id="<?php echo $module->id; ?>"></div>
                <?php if ($params->get('form_privacy_url')): ?>
                    <a class="privacy-link" href="<?php echo $params->get('form_privacy_url'); ?>" target="_blank"><?php echo $params->get('form_privacy_text'); ?></a>
                <?php else: ?>
                    <div class="privacy-link privacy-text"><?php echo $params->get('form_privacy_text'); ?></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="mod-feedback__submit">
            <button class="button-submit<?php echo $disabled; ?>"<?php echo $disabled; ?> data-form-id="<?php echo $module->id; ?>">
                <span><?php echo $params->get('form_submit'); ?></span>
                <span class="mod-feedback__progress"></span>
            </button>
        </div>
    </div>
</div>