<?php

namespace ConfirmationPage\Main\Controllers;
use ConfirmationPage\Utility\Controller;

class MainController extends Controller {

    public static function load_enqueue_assets() {
        // Swiper.js
        wp_enqueue_style('swiper-css', 'https://unpkg.com/swiper/swiper-bundle.min.css');
        wp_enqueue_script('swiper-js', 'https://unpkg.com/swiper/swiper-bundle.min.js', array(), null, true);
    
        // Plugin styles et scripts
        wp_enqueue_style('audio-carousel-style', plugins_url('Public/assets/style.css', dirname(__DIR__)));
        // wp_enqueue_script('audio-carousel-script', plugins_url('Public/assets/script.js', dirname(__DIR__)), array('jquery', 'swiper-js'), null, true);
    }


    public static function custom_thank_you_message($order_id) {
        $products = self::get_all_products();
        $settings = self::get_all_settings();
        $order = wc_get_order(($order_id));
        // echo "<pre>";
        $title = !isset($settings[0]['message_title']) || $settings[0]['message_title'] == "" ? "Veuillez choisir la date de votre rendez-vous téléphonique" : ($settings[0]['message_title']);
        $message = !isset($settings[0]['message_question']) || $settings[0]['message_question'] == "" ? "Une fois le rendez-vous pris veuillez répondre à ce formulaire en cliquant sur le bouton ci-dessous" : ($settings[0]['message_question']);
        $link = !isset($settings[0]['google_form_link']) || $settings[0]['google_form_link'] == "" ? "#" : ($settings[0]['google_form_link']);
        $button = !isset($settings[0]['button_question']) || $settings[0]['button_question'] == ""  ? "Répondre" : ($settings[0]['button_question']);
       

        echo "<h1>$title</h1>";
        echo do_shortcode('[webbabooking service=1]');
    
        echo "<br>";
        echo "<h2 style='color:red'>$message</h2>";
        echo "<button class='button-wbk button-next-wbk' > <a style='color:white' href='$link'>$button</a> </button>";
       
    }

    public static function custom_thank_you_message_shortcode($order_id) {
        
        $products = self::get_all_products();
        $settings = self::get_all_settings();
        $ids_product_concerned = [];
        foreach($products as $product){
            array_push($ids_product_concerned,$product['product_id']) ;
        }
        $display_custom_page = false;
        // Récupérer la clé de commande depuis l'URL
        $order_key = isset($_GET['order_key']) ? sanitize_text_field($_GET['order_key']) : null;

        // Récupérer l'ID de la commande depuis la clé
        $order_id = $order_key ? wc_get_order_id_by_order_key($order_key) : null;

        // Débogage pour vérifier l'ID de la commande
        if($order_id){

            $order = wc_get_order(($order_id));
            foreach($order->get_items() as $items){
                if(in_array($items->get_product_id(),$ids_product_concerned)){
                    $display_custom_page = true;
                }
            }
        }
        if($display_custom_page){
            add_action('woocommerce_thankyou', [MainController::class, 'custom_thank_you_message'], 10, 2);
            $title = !isset($settings[0]['message_title']) || $settings[0]['message_title'] == "" ? "Veuillez choisir la date de votre rendez-vous téléphonique" : ($settings[0]['message_title']);
            $indication = !isset($settings[0]['indication']) || $settings[0]['indication'] == "" ? NULL : wp_kses_post( wp_unslash(($settings[0]['indication'])));
            $message = !isset($settings[0]['message_question']) || $settings[0]['message_question'] == "" ? "Une fois le rendez-vous pris veuillez répondre à ce formulaire en cliquant sur le bouton ci-dessous" : wp_kses_post( wp_unslash(($settings[0]['message_question'])));
            $link = !isset($settings[0]['google_form_link']) || $settings[0]['google_form_link'] == "" ? "#" : ($settings[0]['google_form_link']);
            $button = !isset($settings[0]['button_question']) || $settings[0]['button_question'] == ""  ? "Répondre" : ($settings[0]['button_question']);
            ob_start();
    
            echo "<h1>$title</h1>";
            echo "<p style='color:black'>$indication</p>";
            echo do_shortcode('[webbabooking service=1]');
        
            echo "<br>";
            echo "<h2 style='color:red'>$message</h2>";
            echo "<button class='button-wbk button-next-wbk' style='background-colo:#89023e !important' > <a style='color:white' href='$link'>$button</a> </button>";
            return ob_get_clean();
        }else{
            // Chargez le template WooCommerce "thank you"
            remove_all_actions('woocommerce_thankyou');
            echo "<div style='color:black'>";
            wc_get_template(
                'checkout/thankyou.php', // Chemin relatif au répertoire WooCommerce
                array('order' => wc_get_order($order_id)) // Passez les données nécessaires au template
            );
            echo "</div>";
            
        }
    }
    
}