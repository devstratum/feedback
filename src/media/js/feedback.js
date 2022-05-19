/**
 * @package         Feedback
 * @version         1.0.3
 * @author          Sergey Osipov <info@devstratum.ru>
 * @website         https://devstratum.ru
 * @copyright       Copyright (c) 2022 Sergey Osipov. All Rights Reserved
 * @license         GNU General Public License v2.0
 * @report          https://github.com/devstratum/feedback/issues
 */

document.addEventListener('DOMContentLoaded', function() {
    // Check privacy confirm
    let privacy_checkbox = document.querySelectorAll('.mod-feedback__privacy .privacy-checkbox');
    privacy_checkbox.forEach(function(item) {
        item.addEventListener('click', function() {
            let id = this.getAttribute('data-form-id');
            let feedback_form = document.getElementById('mod_feedback_' + id);
            let feedback_submit = feedback_form.querySelector('.mod-feedback__submit .button-submit');

            if (this.classList.contains('checked')) {
                this.classList.remove('checked');
                feedback_submit.classList.add('disabled');
                feedback_submit.setAttribute('disabled', '1');
            } else {
                this.classList.add('checked');
                feedback_submit.classList.remove('disabled');
                feedback_submit.removeAttribute('disabled');
            }
        });
    });

    // Action Submit
    let feedback_submits = document.querySelectorAll('.mod-feedback__submit .button-submit');
    feedback_submits.forEach(function(item) {
        item.addEventListener('click', function() {
            let id = this.getAttribute('data-form-id');
            if (!this.getAttribute('disabled')) feedbackAction(id);
        });
    });

    // Action Feedback Request
    function feedbackAction(id) {
        let feedback_form = document.getElementById('mod_feedback_' + id);
        let feedback_fields = feedback_form.querySelectorAll('.mod-input');
        let feedback_submit = feedback_form.querySelector('.mod-feedback__submit .button-submit');
        let feedback_progress = feedback_form.querySelector('.mod-feedback__progress');
        let feedback_data = {};

        feedback_submit.classList.add('freeze');
        feedback_submit.setAttribute('disabled', '1');
        feedback_progress.classList.add('active');

        feedback_fields.forEach(function(item) {
            let type = item.getAttribute('type');
            if (type !== 'file') {
                feedback_data[item.getAttribute('name')] = item.value;
            }
        });

        setTimeout(function() {
            feedbackRequest(id, feedback_data);
        }, 300);
    }

    // Alert message
    function feedbackAlert(id, message) {
        let feedback_form = document.getElementById('mod_feedback_' + id);
        let feedback_alert = feedback_form.querySelector('.mod-feedback__alert');
        feedback_alert.innerHTML = '<div class="mod-alert mod-alert-' + message.type + '">' + message.text + '</div>';

        // Clear fields values
        if (message.type === 'success') {
            let feedback_fields = feedback_form.querySelectorAll('.mod-feedback__fields .mod-input');
            feedback_fields.forEach(function(item) {
                item.value = '';
            });
        }
    }

    // Check errors
    function feedbackUpdate(id, data) {
        if (data.length !== 0 && data.errors) {
            let feedback_form = document.getElementById('mod_feedback_' + id);
            data.errors.forEach(function(item) {
                let feedback_input = feedback_form.querySelector('.feedback-field_' + item.key + ' .mod-feedback__input');
                let feedback_error = feedback_form.querySelector('.feedback-field_' + item.key + ' .mod-feedback__error');
                feedback_input.classList.add('error');
                feedback_error.textContent = item.error;
            });
        }
    }

    // Clear errors
    function feedbackClear(id) {
        let feedback_form = document.getElementById('mod_feedback_' + id);
        let feedback_alert = feedback_form.querySelector('.mod-feedback__alert');
        let feedback_fields = feedback_form.querySelectorAll('.mod-feedback__field');

        feedback_alert.innerHTML = '';
        feedback_fields.forEach(function(item) {
            item.querySelector('.mod-feedback__input').classList.remove('error');
            item.querySelector('.mod-feedback__error').textContent = '';
        });
    }

    function feedbackRequest(id, feedback_data) {
        Joomla.request({
            url: '?option=com_ajax&module=feedback&format=json',
            method: 'POST',
            headers: {
                'Cache-Control' : 'no-cache',
                'Content-Type': 'application/json'
            },
            data: JSON.stringify({
                'mod_id': id,
                'fields': feedback_data
            }),
            onBefore: function() {

            },
            onSuccess: function(response) {
                //console.log(response);
                let data = JSON.parse(response);
                if (data.length !== 0) {
                    feedbackClear(id);
                    feedbackAlert(id, data.message);
                    feedbackUpdate(id, data.data);
                }
            },
            onError: function() {
                let message = {"type":"danger","text":"Error: unable to send a message"};
                feedbackAlert(id, message);
            },
            onComplete: function() {
                let feedback_form = document.getElementById('mod_feedback_' + id);
                let feedback_submit = feedback_form.querySelector('.mod-feedback__submit .button-submit');
                let feedback_progress = feedback_form.querySelector('.mod-feedback__progress');
                feedback_submit.classList.remove('freeze');
                feedback_submit.removeAttribute('disabled');
                feedback_progress.classList.remove('active');
            }
        });
    }
});