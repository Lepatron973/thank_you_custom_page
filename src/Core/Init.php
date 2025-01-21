<?php

namespace ConfirmationPage\Core;

use ConfirmationPage\Admin\Controllers\AdminController;
use ConfirmationPage\Main\Controllers\MainController;

class Init
{
    public static function register_services()
    {
        // Initialiser les contrôleurs administratifs
        add_action('admin_menu', [AdminController::class, 'init_settings_page']);
        add_action('woocommerce_before_thankyou', [MainController::class, 'custom_thank_you_message']);
        add_shortcode('custom_thank_you_page', [MainController::class, 'custom_thank_you_message'], 10, 2);
    }
}
