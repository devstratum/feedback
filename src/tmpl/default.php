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

/** @var object $module */
/** @var object $fields_list */

?>

<div class="mod-feedback mod-feedback-<?php echo $params->get('form_theme'); ?>" id="mod_feedback_<?php echo $module->id; ?>" data-form-id="<?php echo $module->id; ?>">
    <div class="mod-feedback__form">
        <?php if ($params->get('form_title')): ?>
            <div class="mod-feedback__title">
                <span><?php echo $params->get('form_title'); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($params->get('form_header')): ?>
            <div class="mod-feedback__header">
                <?php echo $params->get('form_header'); ?>
            </div>
        <?php endif; ?>

        <div class="mod-feedback__fields">
            <?php $ii = 0; foreach ($fields_list as $key => $item): ?>
                <div class="mod-feedback__field feedback-field_<?php echo $key; ?> <?php if ($item->field_required) echo 'required'; ?>">
                    <?php
                    // check placeholder
                    $placeholder = '';
                    if ($params->get('form_placeholder')) $placeholder = ' placeholder="' . $item->field_name . '"';

                    // check required
                    $required = '';
                    if ($item->field_required) $required = ' required';
                    ?>

                    <?php if ($params->get('form_label')): ?>
                        <div class="mod-feedback__label"><?php echo $item->field_name; ?></div>
                    <?php endif; ?>

                    <div class="mod-feedback__input">
                        <?php
                        switch ($item->field_type) {
                            case 'text':
                                echo '<input type="text" name="' . $key . '" class="mod-input mod-input-text"' . $required . $placeholder . '/>';
                                break;
                            case 'tel':
                                echo '<input type="text" name="' . $key . '" class="mod-input mod-input-tel"' . $required . $placeholder . '/>';
                                break;
                            case 'email':
                                echo '<input type="text" name="' . $key . '" class="mod-input mod-input-email"' . $required . $placeholder . '/>';
                                break;
                            case 'number':
                                echo '<input type="text" name="' . $key . '" class="mod-input mod-input-number"' . $required . $placeholder . '/>';
                                break;
                            case 'date':
                                echo '<input type="text" name="' . $key . '" class="mod-input mod-input-date"' . $required . $placeholder . '/>';
                                break;
                            case 'textarea':
                                echo '<textarea name="' . $key . '" class="mod-input mod-input-textarea"' . $required . $placeholder . '></textarea>';
                                break;
                        }
                        ?>
                    </div>
                    <div class="mod-feedback__error"></div>
                </div>
            <?php $ii++; endforeach; ?>
        </div>

        <?php if ($params->get('form_footer')): ?>
            <div class="mod-feedback__footer">
                <?php echo $params->get('form_footer'); ?>
            </div>
        <?php endif; ?>

        <div class="mod-feedback__system">
            <div class="mod-feedback__alert"></div>
        </div>

        <?php if ($params->get('form_privacy')): ?>
            <div class="mod-feedback__privacy">
                <div class="privacy-checkbox checked" data-form-id="<?php echo $module->id; ?>"></div>
                <?php if ($params->get('form_privacy_url')): ?>
                    <a class="privacy-link" href="<?php echo $params->get('form_privacy_url'); ?>" target="_blank"><?php echo $params->get('form_privacy_text'); ?></a>
                <?php else: ?>
                    <div class="privacy-link"><?php echo $params->get('form_privacy_text'); ?></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="mod-feedback__submit">
            <button class="button-submit" data-form-id="<?php echo $module->id; ?>">
                <span><?php echo $params->get('form_submit'); ?></span>
            </button>
        </div>
    </div>
</div>
