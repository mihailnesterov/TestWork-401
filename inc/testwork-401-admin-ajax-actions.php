<?php

/**
 * Ajax удаления картинки
 */
if ( ! function_exists( 'remove_custom_image' ) ) {
	function remove_custom_image( ) {
        
        $product_id = $_POST['product_id'];

        if ( $product_id ) {
            update_post_meta( $product_id, 'testwork_401_custom_product_image', "");
        }
		
        wp_die();
	}

    if( wp_doing_ajax() ) {
        add_action('wp_ajax_remove_custom_image', 'remove_custom_image');
    }
}
