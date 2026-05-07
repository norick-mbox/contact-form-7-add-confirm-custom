<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 *
 *
 * Created by PhpStorm.
 * Author: Eyeta Co.,Ltd.(https://github.com/norick-mbox/contact-form-7-add-confirm-custom)
 *
 */

add_action('init', 'wpcf7c_control_init', 10);

function wpcf7c_control_init()
{
    if (!class_exists('WPCF7_ContactForm')) {
        return false;
    }

    wpcf7c_ajax_json_echo();

    // キャプチャ用フックの差替え
    remove_filter('wpcf7_validate_captchar', 'wpcf7_captcha_validation_filter', 10);
    add_filter('wpcf7_validate_captchar', 'wpcf7c_captcha_validation_filter', 10, 2);
// phpcs:disable WordPress.Security.NonceVerification.Missing -- Contact Form 7 validates this request.
    $wpcf7c_step = isset($_POST['_wpcf7c'])
    ? sanitize_text_field(wp_unslash($_POST['_wpcf7c']))
    : '';
// phpcs:enable WordPress.Security.NonceVerification.Missing
    if ('step1' === $wpcf7c_step) {
        remove_filter('wpcf7_ajax_onload', 'wpcf7_quiz_ajax_refill');
        remove_filter('wpcf7_ajax_json_echo', 'wpcf7_quiz_ajax_refill');
    }

    add_filter('nocache_headers', 'wpcf7c_nocache_headers', 10, 1);
    add_action('wpcf7_enqueue_scripts', 'wpcf7c_enqueue_scripts');
    add_action('wpcf7_enqueue_styles', 'wpcf7c_enqueue_styles');
}

function wpcf7c_ajax_json_echo()
{
// phpcs:disable WordPress.Security.NonceVerification.Missing -- Contact Form 7 validates this request.
    $wpcf7c_step = isset($_POST['_wpcf7c'])
    ? sanitize_text_field(wp_unslash($_POST['_wpcf7c']))
    : '';
// phpcs:enable WordPress.Security.NonceVerification.Missing
    if ($wpcf7c_step) {

        switch ($wpcf7c_step) {

            case 'step1':

                if (WPCF7_VERSION == '3.9' || WPCF7_VERSION == '3.9.1') {

                    add_filter('wpcf7_acceptance', 'wpcf7c_acceptance_filter', 11, 1);

                } elseif (version_compare(WPCF7_VERSION, '3.9.2', '>=')) {

                    add_filter('wpcf7_skip_mail', '__return_true', 10, 2);

                } else {

                    add_action('wpcf7_before_send_mail', 'wpcf7c_before_send_mail_step1', 10, 2);
                }

                add_filter('wpcf7_ajax_json_echo', 'wpcf7c_ajax_json_echo_step1', 10, 3);

                // Flamingo対策
                remove_action('wpcf7_submit', 'wpcf7_flamingo_submit');

                // Contact Form DB対策
                global $wp_filter, $merged_filters;

                if (version_compare(get_bloginfo('version'), '4.7', '<')) {

                    if (isset($wp_filter['wpcf7_before_send_mail'])) {

                        foreach ($wp_filter['wpcf7_before_send_mail'] as $priority => $actions) {

                            foreach ($actions as $key => $action) {

                                if (is_array($action['function']) && is_object($action['function'][0])) {

                                    $class_name = get_class($action['function'][0]);
                                    $method_name = $action['function'][1];

                                    if (
                                        ('CF7DBPlugin' === $class_name || 'CFDBIntegrationContactForm7' === $class_name) &&
                                        ('saveFormData' === $method_name || 'saveCF7FormData' === $method_name)
                                    ) {

                                        unset($wp_filter['wpcf7_before_send_mail'][$priority][$key]);

                                        if (empty($wp_filter['wpcf7_before_send_mail'][$priority])) {
                                            unset($wp_filter['wpcf7_before_send_mail'][$priority]);
                                        }

                                        unset($merged_filters['wpcf7_before_send_mail']);
                                    }
                                }
                            }
                        }
                    }

                } else {

                    if (isset($wp_filter['wpcf7_before_send_mail'])) {

                        $obj_hook = $wp_filter['wpcf7_before_send_mail'];

                        foreach ($obj_hook->callbacks as $priority => $actions) {

                            foreach ($actions as $key => $action) {

                                if (is_array($action['function']) && is_object($action['function'][0])) {

                                    $class_name = get_class($action['function'][0]);
                                    $method_name = $action['function'][1];

                                    if (
                                        ('CF7DBPlugin' === $class_name || 'CFDBIntegrationContactForm7' === $class_name) &&
                                        ('saveFormData' === $method_name || 'saveCF7FormData' === $method_name)
                                    ) {

                                        unset($wp_filter['wpcf7_before_send_mail']->callbacks[$priority][$key]);

                                        if (empty($wp_filter['wpcf7_before_send_mail']->callbacks[$priority])) {
                                            unset($wp_filter['wpcf7_before_send_mail']->callbacks[$priority]);
                                        }

                                        unset($merged_filters['wpcf7_before_send_mail']);
                                    }
                                }
                            }
                        }
                    }
                }

                break;

            case 'step2':

                add_filter('wpcf7_ajax_json_echo', 'wpcf7c_ajax_json_echo_step2', 10, 3);

                break;
        }
    }
}

function wpcf7c_acceptance_filter($accepted)
{

    global $wpcf7c_confflag;

    if (false === $accepted) {
        return $accepted;
    }

    $wpcf7c_confflag = true;

    return false;
}

function wpcf7c_before_send_mail_step1(&$cls)
{
    $cls->skip_mail = true;
}

function wpcf7c_ajax_json_echo_step1($items, $result)
{

    global $wpcf7c_confflag;

    $flag = false;

    if (WPCF7_VERSION == '3.9' || WPCF7_VERSION == '3.9.1') {

        $flag = $wpcf7c_confflag;

    } elseif (version_compare(WPCF7_VERSION, '5.0', '>=')) {

        if ('mail_sent' === $items['status']) {
            $items['message'] = '';
            $items['mailSent'] = false;
            $items['status'] = 'wpcf7c_confirmed';

            unset($items['captcha']);

            return $items;
        }

    } elseif (version_compare(WPCF7_VERSION, '4.8', '>=')) {

        $flag = ('mail_sent' === $items['status']);

    } elseif (version_compare(WPCF7_VERSION, '3.9.2', '>=')) {

        $flag = ('mail_sent' === $result['status']);

    } else {

        $flag = $result['mail_sent'];
    }
// phpcs:disable WordPress.Security.NonceVerification.Missing -- Contact Form 7 validates this request.
    $unit_tag = isset($_POST['_wpcf7_unit_tag'])
    ? esc_js(sanitize_text_field(wp_unslash($_POST['_wpcf7_unit_tag'])))
    : '';
// phpcs:enable WordPress.Security.NonceVerification.Missing
    if ($flag) {

        if (!isset($items['onSubmit']) || null === $items['onSubmit']) {

            $items['onSubmit'] = array(
                "wpcf7c_step1('{$unit_tag}');",
            );

        } else {

            $items['onSubmit'][] = "wpcf7c_step1('{$unit_tag}');";
        }

        $items['message'] = '';
        $items['mailSent'] = false;
        $items['status'] = '';

        unset($items['captcha']);

    } else {

        $result_scroll = false;

        if (apply_filters('wpcf7c_input_error_scroll', $result_scroll)) {

            if (!isset($items['onSubmit']) || null === $items['onSubmit']) {

                $items['onSubmit'] = array(
                    "wpcf7c_scroll('{$unit_tag}');",
                );

            } else {

                $items['onSubmit'][] = "wpcf7c_scroll('{$unit_tag}');";
            }
        }
    }

    return $items;
}

/*
 * captcha対策
 */
function wpcf7c_captcha_validation_filter($result, $tag)
{

    if (version_compare(WPCF7_VERSION, '4.6', '>=')) {
        $tag = new WPCF7_FormTag($tag);
    } else {
        $tag = new WPCF7_Shortcode($tag);
    }

    $name = $tag->name;

    $captchac = '_wpcf7_captcha_challenge_' . $name;
// phpcs:disable WordPress.Security.NonceVerification.Missing -- Contact Form 7 validates this request.
    $prefix = isset($_POST[$captchac])
    ? sanitize_text_field(wp_unslash($_POST[$captchac]))
    : '';
// phpcs:enable WordPress.Security.NonceVerification.Missing
// phpcs:disable WordPress.Security.NonceVerification.Missing -- Contact Form 7 validates this request.
    $response = isset($_POST[$name])
    ? sanitize_text_field(wp_unslash($_POST[$name]))
    : '';
// phpcs:enable WordPress.Security.NonceVerification.Missing

    $response = wpcf7_canonicalize($response);

    if (0 === strlen($prefix) || !wpcf7_check_captcha($prefix, $response)) {

        $response = wpcf7_canonicalize($response);

        if (0 === strlen($prefix) || !wpcf7_check_captcha($prefix, $response)) {
            $result->invalidate($tag, wpcf7_get_message('captcha_not_match'));
        }
    }
// phpcs:disable WordPress.Security.NonceVerification.Missing -- Contact Form 7 validates this request.
    $wpcf7c_step = isset($_POST['_wpcf7c'])
    ? sanitize_text_field(wp_unslash($_POST['_wpcf7c']))
    : '';
// phpcs:enable WordPress.Security.NonceVerification.Missing
    if (0 !== strlen($prefix) && 'step1' === $wpcf7c_step) {

        // step1時はcaptchaを維持

    } elseif (0 !== strlen($prefix)) {

        wpcf7_remove_captcha($prefix);
    }

    return $result;
}

function wpcf7c_ajax_json_echo_step2($items, $result)
{

    $flag = false;

    if (WPCF7_VERSION == '3.9' || WPCF7_VERSION == '3.9.1') {

        $flag = $items['mailSent'];

    } elseif (version_compare(WPCF7_VERSION, '4.8', '>=')) {

        $flag = ('mail_sent' === $items['status']);

    } elseif (version_compare(WPCF7_VERSION, '3.9.2', '>=')) {

        $flag = $items['mailSent'];

    } else {

        $flag = $result['mail_sent'];
    }
// phpcs:disable WordPress.Security.NonceVerification.Missing -- Contact Form 7 validates this request.
    $unit_tag = isset($_POST['_wpcf7_unit_tag'])
    ? esc_js(sanitize_text_field(wp_unslash($_POST['_wpcf7_unit_tag'])))
    : '';
// phpcs:enable WordPress.Security.NonceVerification.Missing
    if ($flag) {

        if (!isset($items['onSubmit']) || null === $items['onSubmit']) {

            $items['onSubmit'] = array(
                "wpcf7c_step2('{$unit_tag}');",
            );

        } else {

            $items['onSubmit'][] = "wpcf7c_step2('{$unit_tag}');";
        }

    } else {

        if (!isset($items['onSubmit']) || null === $items['onSubmit']) {

            $items['onSubmit'] = array(
                "wpcf7c_step2_error('{$unit_tag}');",
            );

        } else {

            $items['onSubmit'][] = "wpcf7c_step2_error('{$unit_tag}');";
        }
    }

    return $items;
}

add_filter('wpcf7_form_elements', 'norick_confirm_message_output', 20, 1);

function norick_confirm_message_output($html)
{

    if (!class_exists('WPCF7_ContactForm')) {
        return $html;
    }

    $contact_form = WPCF7_ContactForm::get_current();

    if (!$contact_form) {
        return $html;
    }

    $form_id = absint($contact_form->id());

    if (!$form_id) {
        return $html;
    }

    $message = get_post_meta($form_id, '_norick_confirm_message', true);

    if ('' === trim($message)) {
        return $html;
    }

    $output = '<div class="wpcf7c-confirm-message-wrap wpcf7c-elm-step2 wpcf7c-force-hide">';
    $output .= '<h3 class="wpcf7c-confirm-title">';
    $output .= nl2br(esc_html($message));
    $output .= '</h3>';
    $output .= '</div>';

    return $output . $html;
}