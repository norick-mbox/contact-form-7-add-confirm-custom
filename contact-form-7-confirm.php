<?php
    if (!defined('ABSPATH')) {
    exit;
    }

    /*
    Plugin Name: Norick Confirm for Contact Form 7
    Plugin URI: https: //plugins.norick-mbox.com/191/

    Requires Plugins: contact-form-7
    Description: Forked version of Contact Form 7 add confirm for modern Contact Form 7.
    Author: noricksaeki
    Text Domain: norick-confirm-for-contact-form-7
    Domain Path: /languages/
    Version: 6.0.1
    License: GPLv2 or later
    License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

    /*  Copyright 2014- Yuichiro ABE (email: y.abe at eyeta.jp)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

    function norick_disable_old_confirm_plugin()
    {

    if (!current_user_can('activate_plugins')) {
        return;
    }

    include_once ABSPATH . 'wp-admin/includes/plugin.php';

    $old_plugin = 'contact-form-7-add-confirm/contact-form-7-add-confirm.php';

    if (is_plugin_active($old_plugin)) {
        deactivate_plugins($old_plugin);
    }
    }

    register_activation_hook(
    __FILE__,
    'norick_disable_old_confirm_plugin'
    );

    define('WPCF7C_CUSTOM_VERSION', '6.0.1');

    if (!defined('WPCF7C_PLUGIN_BASENAME')) {
    define('WPCF7C_PLUGIN_BASENAME', plugin_basename(__FILE__));
    }

    if (!defined('WPCF7C_PLUGIN_NAME')) {
    define('WPCF7C_PLUGIN_NAME', trim(dirname(WPCF7C_PLUGIN_BASENAME), '/'));
    }

    if (!defined('WPCF7C_PLUGIN_DIR')) {
    define('WPCF7C_PLUGIN_DIR', untrailingslashit(dirname(__FILE__)));
    }

    if (!defined('WPCF7C_PLUGIN_URL')) {
    define('WPCF7C_PLUGIN_URL', untrailingslashit(plugins_url('', __FILE__)));
    }

    if (!defined('WPCF7C_PLUGIN_MODULES_DIR')) {
    define('WPCF7C_PLUGIN_MODULES_DIR', WPCF7C_PLUGIN_DIR . '/modules');
    }

    add_filter('wpcf7_editor_panels', function ($panels) {
    $panels['norick_confirm'] = [
        'title' => esc_html__(
            'Confirm Settings',
            'norick-confirm-for-contact-form-7'
        ),
        'callback' => 'norick_confirm_panel',
    ];
    return $panels;
    });

    function norick_confirm_panel($post)
    {
    $value = get_post_meta($post->id(), '_norick_confirm_message', true);
    ?>
    <h2>
	<?php
        echo esc_html__(
        'Confirmation screen message',
        'norick-confirm-for-contact-form-7'
            );
    ?>
</h2>
    <input
	type="text"
	name="norick_confirm_message"
	value="<?php echo esc_attr($value); ?>"
	class="large-text"/>
    <p class="description">
	<?php
        echo esc_html__(
        'Example: Please confirm your input before submission.',
        'norick-confirm-for-contact-form-7'
            );
    ?>
</p>

    <?php
        }

        add_action('wpcf7_save_contact_form', function ($post) {

            if (!isset($_POST['norick_confirm_message'])) {
                return;
            }

            update_post_meta(
                $post->id(),
                '_norick_confirm_message',
                sanitize_text_field(
                    wp_unslash($_POST['norick_confirm_message'])
                )
            );
        });

        add_action('plugins_loaded', 'norick_confirm_load_textdomain');

        function norick_confirm_load_textdomain()
        {

            load_plugin_textdomain(
                'norick-confirm-for-contact-form-7',
                false,
                dirname(plugin_basename(__FILE__)) . '/languages'
            );
        }

    require_once WPCF7C_PLUGIN_DIR . '/settings.php';
