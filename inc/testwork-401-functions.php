<?php 

/**
 * Добавить тип продукта в список товаров
 */
add_action( 'woocommerce_after_shop_loop_item_title', 'action_testwork_401_product_type_loop', 20 );
function action_testwork_401_product_type_loop() {
	$product_type = get_post_meta(get_the_ID(), 'testwork_401_product_type', true);
	echo '<span class="custom-type">' . $product_type . '</span>';
}

/**
 * Добавить тип продукта на страницу товара
 */
add_action( 'woocommerce_product_meta_end', 'action_testwork_401_product_type', 20 );
function action_testwork_401_product_type() { 
	$product_type = get_post_meta(get_the_ID(), 'testwork_401_product_type', true); ?>
	<?php if( $product_type ): ?>
		<span class="custom-type">
			<?= _e( 'Тип', 'textdomain' )?>: <b><?= esc_html($product_type) ?></b>
		</span>
	<?php endif; ?>
<?php
}

/**
 * Отключить sidebar на страницах woocommerce
 */
add_action( 'get_header', 'remove_storefront_sidebar' );
function remove_storefront_sidebar() {
	if ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) {
		remove_action( 'storefront_sidebar', 'storefront_get_sidebar', 10 );
	}
}