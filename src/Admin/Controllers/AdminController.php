<?php
namespace ConfirmationPage\Admin\Controllers;

use ConfirmationPage\Utility\Controller;


class AdminController extends Controller {

    // Initialiser la page des paramètres
    public static function init_settings_page() {
        add_menu_page(
            'Réglage Page de confirmation', // Titre de la page
            'Confirmation Page',          // Texte du menu
            'manage_options',          // Capacité requise
            'customize-thank-page',          // Slug de la page
            [self::class, 'render_settings_page'], // Fonction de rendu
            'dashicons-text-page', // Icône du menu
            100                          // Position dans le menu
        );

        // Ajouter un sous-menu pour la liste des audios
        //self::customize_thank_page_add_admin_submenu();
    }

    // Rendu de la page principale des paramètres
    public static function render_settings_page() {
        self::handle_audio_submission();
        include dirname(plugin_dir_path(__FILE__), 2) . '/Admin/Views/SettingsPage.php';
    }

   
    public static function insert_product($product_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'customize_thank_page_products';
    
        $wpdb->insert(
            $table_name,
            [
                'product_id' => sanitize_text_field($product_id),
            ],
            ['%s', '%s']
        );
    }
    public static function insert_settings_page($title, $message_question, $button_value,$link,$indication) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'customize_thank_page_admin';
        
       if(!empty(self::get_all_settings())){

        $message_question = wp_kses_post($message_question); // Nettoie le contenu tout en préservant les balises HTML
        $message_question = nl2br($message_question); // Convertit les retours à la ligne (\n) en <br>

        $indication = wp_kses_post($indication); // Nettoie le contenu tout en préservant les balises HTML
        $indication = nl2br($indication); // Convertit les retours à la ligne (\n) en <br>
        $wpdb->update(
            $table_name,
            array(
                'message_title' => sanitize_text_field($title),
                'indication' => ($indication),
                'message_question' => $message_question,
                'button_question' => sanitize_text_field($button_value),
                'google_form_link' => sanitize_url($link),
            ),
            array(
                'id' => 1,
            )
        );
       }else{
        $wpdb->insert(
            $table_name,
            [
                'message_title' => sanitize_text_field($title),
                'indication' => ($indication),
                'message_question' => $message_question,
                'button_question' => sanitize_text_field($button_value),
                'google_form_link' => sanitize_url($link),
            ],
            ['%s', '%s']
        );
       }
    }
    
    public static function delete_product($product_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'customize_thank_page_products';
    
        $wpdb->delete($table_name, ['product_id' => intval($product_id)], ['%d']);
    }
        
    public static function custom_upload_subdirectory($upload) {
        // Nom du sous-répertoire
        $subdir = '/audio_caroussel';
    
        // Modifier les chemins et URL
        $upload['subdir'] = $subdir;
        $upload['path']   = $upload['basedir'] . $subdir;
        $upload['url']    = $upload['baseurl'] . $subdir;
    
        return $upload;
    }
    
    
    
    public static function handle_audio_submission() {
       
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                    // Vérifiez le nonce
            if (isset($_POST['customize_thank_page_nonce']) || isset($_POST['delete_product_nonce'])) {
               
                if(isset($_POST['customize_thank_page_nonce']) && wp_verify_nonce($_POST['customize_thank_page_nonce'], 'customize_thank_page_upload')){
                    if (isset($_POST['question_indication'], $_POST['question_button'])) {
                        self::insert_settings_page($_POST['title'],$_POST['question_indication'], $_POST['question_button'],$_POST['google_form_link'],$_POST['indication']);
                        add_settings_error('customize_thank_page', 'product_added', __('Product added successfully.', 'customize-thank-page'), 'success');
                    }
                    if (isset($_POST['product_id'])) {
                        self::insert_product($_POST['product_id']);
                        add_settings_error('customize_thank_page', 'product_added', __('Product added successfully.', 'customize-thank-page'), 'success');
                    }
                }
                
    
                if (isset($_POST['delete_product_nonce']) && wp_verify_nonce($_POST['delete_product_nonce'], 'product_nonce')) {
                    if (isset($_POST['delete_product'])) {
                        self::delete_product($_POST['product_id']);
                        add_settings_error('customize_thank_page', 'product_deleted', __('Audio deleted successfully.', 'customize-thank-page'), 'success');
                    }
                }
            }
            
        }
       
    }
    
}
