<?php

namespace ConfirmationPage\Core;

use ConfirmationPage\Admin\Controllers\AdminController;
use ConfirmationPage\Main\Controllers\MainController;

class Init {
    public static function register_services() {
        // Initialiser les contrôleurs administratifs
        add_action('admin_menu', [AdminController::class, 'init_settings_page']);
        
        add_shortcode('custom_thank_you_page', [MainController::class, 'custom_thank_you_message_shortcode'],10,2);
    }
}
