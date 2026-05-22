<?php
if (!defined('ABSPATH')) {
    exit;
}
/**
 *
 *
 * Created by PhpStorm.
 * Author: Eyeta Co.,Ltd.(http://www.eyeta.jp)
 *
 */

global $wpcf7c_confflag;
$wpcf7c_confflag = false;

require_once WPCF7C_PLUGIN_DIR . '/includes/functions.php';
require_once WPCF7C_PLUGIN_DIR . '/includes/controller.php';

add_action('plugins_loaded', 'wpcf7c', 11);

function wpcf7c()
{
    wpcf7c_load_modules();

    nocache_headers();
}

function wpcf7c_nocache_headers($headers)
{

    $headers["Cache-Control"] = "no-store, no-cache, must-revalidate, max-age=0";

    return $headers;
}

function wpcf7c_enqueue_scripts()
{
    $in_footer = true;

    if ('header' === WPCF7_LOAD_JS) {
        $in_footer = false;
    }

    wp_enqueue_script(
        'contact-form-7-confirm-custom',
        wpcf7c_plugin_url('includes/js/scripts.js'),
        array('jquery', 'jquery-form'),
        WPCF7C_CUSTOM_VERSION,
        $in_footer
    );

    // 現在のフォームID取得
    $post_id = get_the_ID();

    wp_localize_script(
        'contact-form-7-confirm-custom',
        'norickConfirm',
        array(
            'simpleThanksEnabled' => get_post_meta(
                $post_id,
                '_norick_simple_thanks_enabled',
                true
            ),

            'simpleThanksMessage' => get_post_meta(
                $post_id,
                '_norick_simple_thanks_message',
                true
            ),
        )
    );
}


function wpcf7c_enqueue_styles()
{
    wp_enqueue_style('contact-form-7-confirm-custom',
        wpcf7c_plugin_url('includes/css/styles.css'),
        array(),
        WPCF7C_CUSTOM_VERSION,
        'all'
    );

    if (wpcf7_is_rtl()) {
        wp_enqueue_style(
            'contact-form-7-confirm-custom-rtl',
            wpcf7c_plugin_url('includes/css/styles-rtl.css'),
            array(),
            WPCF7C_CUSTOM_VERSION,
            'all'
        );
    }

}
