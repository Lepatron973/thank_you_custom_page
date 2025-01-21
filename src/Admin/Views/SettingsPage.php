<div class="wrap">
    <h1><?php esc_html_e('Réglage Page de confirmation', 'audio-carousel'); ?></h1>
    <h2><?php esc_html_e('Ce plugin permet de modifier les informations contenus dans la page de confirmation de commande.
    Vous pourrez ainsi indiquer quels produits sont concernés par l\'affichage du calendrier de rendez-vous ainsi que le message de réponse au questionnaire.', 'audio-carousel'); ?></h2>
    <h3><?php esc_html_e('Le plugin est aussi utilisable via un shortcode, copiez ce code sur la page de confirmation de commande que vous avec choisis: [custom_thank_you_page]', 'audio-carousel'); ?></h3>



    <?php
    //récupération de la liste des produits
    $args = array(
        'status' => 'publish', // Récupérer uniquement les produits publiés
        'limit' => -1,         // -1 pour récupérer tous les produits
    );
    $settings = \ConfirmationPage\Admin\Controllers\AdminController::get_all_settings();
    $product_concerned = \ConfirmationPage\Admin\Controllers\AdminController::get_all_products();
    $ids_product_concerned = [];
    foreach($product_concerned as $product){
        $ids_product_concerned[] = $product['product_id'];
    }
    $products = wc_get_products($args);
    $title = isset($settings[0]['message_title']) ? $settings[0]['message_title'] : null;
    $indication = isset($settings[0]['indication']) ? $settings[0]['indication'] : null;
    $message = isset($settings[0]['message_question']) ? $settings[0]['message_question'] : null;
    $button = isset($settings[0]['button_question']) ? $settings[0]['button_question'] : null;
    $link = isset($settings[0]['google_form_link']) ? $settings[0]['google_form_link'] : null;
     ?>
    <!-- Formulaire pour ajouter un nouvel audio -->
    <form method="post" action="" enctype="multipart/form-data">
    <?php wp_nonce_field('customize_thank_page_upload', 'customize_thank_page_nonce'); ?>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="audio_title"><?php esc_html_e('Titre au dessus du calendrier', 'audio-carousel'); ?></label>
            </th>
            <td>
                <textarea name="title" id="title"  class="regular-text" rows="6" >
                <?= $title; ?>
                </textarea>
             
            </td>
        </tr>
        <tr>
        <th scope="row">
            <label for="audio_title"><?php esc_html_e('Indication des étapes à suivre', 'audio-carousel'); ?></label>
        </th>
            <td style='max-width:300px'>
                <?php
                $settings = array(
                    'textarea_name' => 'indication', // Le nom du champ qui sera utilisé lors de l'enregistrement
                    'textarea_rows' => 6,           // Le nombre de lignes affichées
                    'media_buttons' => false,       // Désactiver les boutons pour ajouter des médias
                    'teeny'         => true,        // Utiliser une version simplifiée de l'éditeur
                    'quicktags'     => true,        // Inclure les outils QuickTags (facultatif)
                );

                wp_editor(wp_kses_post( wp_unslash($indication)), 'indication_editor', $settings);
                ?>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="audio_title"><?php esc_html_e('Message pour le questionnaire', 'audio-carousel'); ?></label>
            </th>
            <td style='max-width:300px'>
                <?php
                $settings = array(
                    'textarea_name' => 'question_indication', // Le nom du champ qui sera utilisé lors de l'enregistrement
                    'textarea_rows' => 6,           // Le nombre de lignes affichées
                    'media_buttons' => false,       // Désactiver les boutons pour ajouter des médias
                    'teeny'         => true,        // Utiliser une version simplifiée de l'éditeur
                    'quicktags'     => true,        // Inclure les outils QuickTags (facultatif)
                );

                wp_editor(wp_kses_post( wp_unslash($message)), 'question_indication_editor', $settings);
                ?>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="audio_title"><?php esc_html_e('Lien Google Form', 'audio-carousel'); ?></label>
            </th>
            <td>
                <input type="text" name="google_form_link" id='google_form_link' value="<?= $link; ?>" id="question_button" class="regular-text" >
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="question_button"><?php esc_html_e('Valeur du bouton questionnaire', 'audio-carousel'); ?></label>
            </th>
            <td>
                <input type="text" name="question_button" value="<?= $button; ?>" id="question_button" class="regular-text" >
            </td>
        </tr>
        <tr>
            <th scope="row">
            <label for="produit">Choisissez un produit :</label>
            </th>
            <td>
            <select name="product_id" id="produit">
            <option value="">-- Sélectionnez un produit --</option>
            <?php foreach ($products as $product) : ?>
                <? if(!in_array($product->get_id(),$ids_product_concerned)): ?>
                <option value="<?php echo $product->get_id(); ?>">
                    <?php echo $product->get_name(); ?>
                </option>
                <? endif; ?>
            <?php endforeach; ?>
        </select>
            </td>
        </tr>
    </table>
    
        
        
       
    <?php submit_button(__('Enregistrer', 'audio-carousel')); ?>
</form>



    <hr>
    <!-- Liste des audios existants -->
    <h2><?php esc_html_e('Produits concernés', 'audio-carousel'); ?></h2>
    <?php

if (!empty($product_concerned)): ?>
    <table class="widefat fixed striped">
        <thead>
            <tr>
                <th><?php esc_html_e('Title', 'audio-carousel'); ?></th>
                <th><?php esc_html_e('Id', 'audio-carousel'); ?></th>
                <th><?php esc_html_e('Actions', 'audio-carousel'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $index => $product): ?>
                <? if(in_array($product->get_id(),$ids_product_concerned)): ?>
                <tr>
                    <td><?php echo esc_html($product->get_name()); ?></td>
                    <td>
                        
                            <?php echo esc_html($product->get_id()); ?>
                       
                    </td>
                    <td>
                        <form method="post" action="" style="display:inline;">
                            <?php wp_nonce_field('product_nonce', 'delete_product_nonce'); ?>
                            <input type="hidden" name="product_id" value="<?php echo esc_attr($product->get_id()); ?>">
                            <input type="submit" name="delete_product" class="button button-link-delete" value="<?php esc_attr_e('supprimer', 'audio-carousel'); ?>">
                        </form>
                    </td>
                </tr>
                <? else: ?>
                    <tr style="display : none;">
                    <td><?php echo esc_html_e('Aucun produits n\'à été ajouté.', 'audio-carousel'); ?></td>
                </tr>
                <? endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p><?php esc_html_e('Aucun produits n\'à été ajouté.', 'audio-carousel'); ?></p>
<?php endif; ?>

</div>

