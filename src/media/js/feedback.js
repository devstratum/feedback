/**
 * @package         Feedback
 * @version         0.63
 * @author          Sergey Osipov <info@devstratum.ru>
 * @website         https://devstratum.ru
 * @copyright       Copyright (c) 2022 Sergey Osipov. All Rights Reserved
 * @license         GNU General Public License v2.0
 * @report          https://github.com/devstratum/feedback/issues
 */

document.addEventListener('DOMContentLoaded', function() {
    let feedback_submits = document.querySelectorAll('.mod-feedback__submit .button-submit');
    feedback_submits.forEach(function(item) {
        item.addEventListener('click', function() {
            let id = this.getAttribute('data-form-id');
            feedbackAction(id);
        });
    });

    function feedbackAction(id) {
        let feedback_form = document.getElementById('mod_feedback_' + id);
        let feedback_fields = feedback_form.querySelectorAll('.mod-input');
        let feedback_data = {};

        feedback_fields.forEach(function(item) {
            let type = item.getAttribute('type');
            if (type !== 'file') {
                feedback_data[item.getAttribute('name')] = item.value;
            }
        });

        feedbackRequest(id, feedback_data);
    }

    function feedbackAlert(message) {

    }

    function feedbackUpdate(data) {
        if (data.length !== 0 && data.errors) {
            let feedback_form = document.getElementById('mod_feedback_' + data.mod_id);
            // output errors
            data.errors.forEach(function(item) {
                let feedback_input = feedback_form.querySelector('.feedback-field_' + item.key + ' .mod-feedback__input');
                let feedback_error = feedback_form.querySelector('.feedback-field_' + item.key + ' .mod-feedback__error');
                feedback_input.classList.add('error');
                feedback_error.textContent = item.error;
            });
        }
    }

    function feedbackClear(id) {
        let feedback_form = document.getElementById('mod_feedback_' + id);
        let feedback_fields = feedback_form.querySelectorAll('.mod-feedback__field');
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
                    //feedbackAlert(data.message);
                    feedbackClear(id);
                    feedbackUpdate(data.data);
                }
            },
            onError: function() {
                console.log('ajax Error');
            },
            onComplete: function() {
                console.log('ajax Complete');
            }
        });
    }
});