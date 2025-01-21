<?php

/**
 * Plugin Name: Thank you Custom Page
 * Plugin URI: https://github.com/Lepatron973/thank_you_custom_page
 * Description: Enhance your order confirmation page with a WebbaBooking calendar, allowing users to reserve their session or appointment after completing their order.
 * Version: 0.3
 * Author: Lepatron973
 * Author URI: https://sunitronics.fr
 * Github Plugin URI: https://github.com/Lepatron973/thank_you_custom_page
 */

defined('ABSPATH') || exit;

use ConfirmationPage\GitHub_Updater;

// Inclure l'autoloader personnalisé
require_once __DIR__ . '/autoloader.php';


if (is_admin()) {
    require_once plugin_dir_path(__FILE__) . 'updater.php';
    new GitHub_Updater(__FILE__, 'Lepatron973', 'thank_you_custom_page');
}

use ConfirmationPage\Core\Init;

// Initialiser les services du plugin
function customize_thank_page_bootstrap()
{
    Init::register_services();
}

add_action('plugins_loaded', 'customize_thank_page_bootstrap');

//création de la bdd lors de l'ativation du plugin
register_activation_hook(__FILE__, 'customize_thank_page_create_table');

function customize_thank_page_create_table()
{

    global $wpdb;

    $table_name = $wpdb->prefix . 'customize_thank_page_products';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
       id INT(11) NOT NULL AUTO_INCREMENT,
       product_id integer NOT NULL,
       PRIMARY KEY (id)
   ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    $table_name = $wpdb->prefix . 'customize_thank_page_admin';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
       id INT(11) NOT NULL AUTO_INCREMENT,
       message_title VARCHAR(255) NOT NULL,
       indication TEXT NOT NULL,
       message_question VARCHAR(255) NOT NULL,
       button_question VARCHAR(255) NOT NULL,
       google_form_link VARCHAR(255) NOT NULL,
       PRIMARY KEY (id)
   ) $charset_collate;";
    dbDelta($sql);
}

//suppression de la bdd lors de la désactivation

register_deactivation_hook(__FILE__, 'customize_thank_page_remove_table');

function customize_thank_page_remove_table()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'customize_thank_page_products';

    $wpdb->query("DROP TABLE IF EXISTS $table_name");
    // table 2
    $table_name = $wpdb->prefix . 'customize_thank_page_admin';

    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}
