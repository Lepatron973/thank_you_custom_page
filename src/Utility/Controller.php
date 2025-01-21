<?php
 namespace ConfirmationPage\Utility;


class Controller {

    public static function get_all_products() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'customize_thank_page_products';
    
        return $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
    }
    public static function get_all_settings() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'customize_thank_page_admin';
    
        return $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
    }

}