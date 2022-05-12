<?php 

/**
 * Вывод блока с кастомными полями в карточку товара
 */
add_action('woocommerce_product_options_general_product_data', 'testwork_401_generate_html');
function testwork_401_generate_html() { 
    $image = get_post_meta( get_the_ID(), 'testwork_401_custom_product_image', true );
    ?>

    <div class="options_group testwork-401-fields">
        <h3>TestWork 401</h3>
        
        <?php 
        woocommerce_wp_text_input(
            array(
                'id' => 'testwork_401_product_title',
                'label' => 'Название товара',
                'class' => 'short input',
                'value' => get_the_title()
            )
        );
        ?>
        
        <div class="custom-image-field">
            <p>Картинка товара</p>
            <figure>
                <img 
                    id="custom_product_image" 
                    src="<?= esc_attr($image) ?>" 
                    alt="Добавить картинку товара" 
                    style="display: <?= $image === "" ? 'none' : 'block' ?>"
                />

                <button 
                    id="upload_custom_product_image"
                    type="button" 
                    title="Загрузить картинку"
                    style="display: <?= $image === "" ? 'block' : 'none' ?>"
                >&plus;</button>

                <button 
                    id="remove_custom_product_image"
                    type="button" 
                    title="Удалить картинку"
                    style="display: <?= $image === "" ? 'none' : 'block' ?>"
                >&#10005;</button>
            </figure>
            <?php 
                woocommerce_wp_text_input(
                    array(
                        'id' => 'testwork_401_custom_product_image',
                        'type' => 'file',
                        'label' => 'Картинка товара',
                        'class' => 'short input',
                        'style' => 'display:none',
                        'custom_attributes' => array(
                            'accept' => "image/*"
                        )
                    )
                );
            ?>
        </div>
        
        <?php 
            woocommerce_wp_text_input(
                array(
                    'id' => 'testwork_401_post_date',
                    'type' => 'datetime-local',
                    'label' => 'Дата создания',
                    'class' => 'datepicker short input',
                    'value' => stripslashes(get_the_date('Y-m-d\TH:i'))
                )
            );
            woocommerce_wp_select( array(
                'id' => 'testwork_401_product_type',
                'value' => get_post_meta( get_the_ID(), 'testwork_401_product_type', true ),
                'wrapper_class' => 'testwork-401-wp-select',
                'class' => 'short select',
                'label' => 'Тип продукта',
                'options' => array( 
                    '' => 'Не выбран...',
                    'rare' => 'Rare', 
                    'frequent' => 'Frequent', 
                    'unusual' => 'Unusual'
                ),
            ) );
        ?>
        
        <?php 
            woocommerce_wp_text_input(
                array(
                    'id' => 'testwork_401_product_price',
                    'label' => 'Цена товара',
                    'class' => 'short input',
                    'wrapper_class' => 'show_if_simple show_if_external',
                    'value' => wc_get_product(get_the_ID())->get_regular_price(),
                    'data_type' => 'price'
                )
            );
        ?>

        <div>
            <button type="button" id="clear_product_custom_fields_btn">&#10005; Очистить поля</button>
        </div>
    </div>
<?php
}

/**
 * Сохранить кастомные поля
 */
add_action('woocommerce_process_product_meta', 'testwork_401_save_product_fields');
function testwork_401_save_product_fields( $product_id ) {

    $args = array('ID' => $product_id);
    
    $post_title = sanitize_text_field($_POST['testwork_401_product_title']);
    $post_date = sanitize_text_field($_POST['testwork_401_post_date']);
    $product_type = sanitize_text_field($_POST['testwork_401_product_type']);  

    if( isset($post_title) ) {
        $args['post_title'] = $post_title;
    }

    if( isset($post_date) ) {
        $args['post_date'] = $post_date;
    }

    if( isset($product_type) ) {
        $args['meta_input']['testwork_401_product_type'] = $product_type;        
    }

    if(isset($_FILES)) {
        $upload = wp_handle_upload(
            $_FILES['testwork_401_custom_product_image'], 
            array('test_form' => FALSE)
        );
        if($upload["url"]) {
            update_post_meta( $product_id, 'testwork_401_custom_product_image', esc_html($upload["url"]));
        }
    }

    wp_update_post( wp_slash($args) );

    wc_delete_product_transients( $product_id );
}

/**
 * Заменить картинку в списке товара на кастомную
 */
add_filter( 'wp_get_attachment_image_src', 'testwork_401_change_product_image_src', 99, 4 );
function testwork_401_change_product_image_src( $image, $attachment_id, $size, $icon ) {

    $src =  get_post_meta( get_the_ID(), 'testwork_401_custom_product_image', true );
    
    if( $src === "" ) return $image;
    
    $image  = array( $src, 150, 150 );
    
    return $image;
}

/**
 * Добавить js, который обрабатывает элементы в карточке товара
 */
add_action('admin_footer', 'testwork_401_add_product_scripts');
function testwork_401_add_product_scripts() { 
    
    /**
     * Подключаем js в админке
     */
    wp_enqueue_script( 
        'testwork-401-admin-scripts', 
        get_stylesheet_directory_uri() . '/js/testwork-401-admin-scripts.js' 
    );

    /**
     * Добавляем в html админки product.id (для манипуляций в js)
     */
	wp_localize_script( 
		'testwork-401-admin-scripts', 
		'product', 
		array(
			'id' => get_the_ID()
		)
	);
}

/**
 * Добавить колонку "Тип" в таблицу товаров
 */
add_filter( 'manage_edit-product_columns', 'change_product_columns_filter',10, 1 );
function change_product_columns_filter( $columns ) {
    array_splice( $columns, 4, 0, array( 'testwork_401_product_type' => __( 'Тип', 'textdomain' ) ) ) ;
return $columns;
}

/**
 * Добавить значения типов в таблицу товаров
 */
add_action( 'manage_product_posts_custom_column', 'change_product_field_product_type', 10, 2 );
function change_product_field_product_type( $column, $postid ) {
    if ( $column == 'testwork_401_product_type' ) {
        $product_type = get_post_meta( $postid, 'testwork_401_product_type', true ); ?>
            <span><?= $product_type ? esc_html($product_type) : '-' ?></span>
        <?php
    }
}

/**
 * Добавить ссылку "Создать товар" в toolbar
 */
add_action('admin_bar_menu', 'admin_add_toolbar_items', 98);
function admin_add_toolbar_items( $admin_bar ) {

    if ( ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
        
        $admin_bar->add_menu( array(
            'id'    => 'testwork-401-admin-bar-item',
            'title' => __( 'Создать товар', 'textdomain' ),
            'href'  => get_dashboard_url() . 'edit.php?post_type=product&page=testwork-401-create-product',
        ));
    }
}

/**
 * Добавить ссылку на страницу создания товара
 */
add_action('admin_menu', 'add_admin_menu_page' );
function add_admin_menu_page() {
    add_submenu_page(
        'edit.php?post_type=product',
        'Создать товар', 
        'Создать', 
        'manage_options', 
        'testwork-401-create-product', 
        'render_admin_menu_page_html',
        1
    );
}
/**
 * Вывод формы на страницу товара
 */
function render_admin_menu_page_html() {
    if ( get_current_screen()->id !== 'product_page_testwork-401-create-product' ) return;
    ?>
    <form name="post" action="edit.php?post_type=product" method="post" id="post" enctype="multipart/form-data">
        <div class="options_group testwork-401-fields">
            <h1>TestWork 401 создать товар...</h1>
            <hr />
            
            <div class="custom-item-field">
                <label for="testwork_401_product_title">Название товара</label>
                <input 
                    id="testwork_401_product_title" 
                    name="testwork_401_product_title"
                    type="text"
                    class="short input"
                    required
                />
            </div>
            
            <div class="custom-image-field custom-item-field">
                <p>Картинка товара</p>
                <figure>
                    <img 
                        id="custom_product_image" 
                        src="" 
                        alt="Добавить картинку товара" 
                        style="display: none"
                    />

                    <button 
                        id="upload_custom_product_image"
                        type="button" 
                        title="Загрузить картинку"
                        style="display: block"
                    >&plus;</button>

                    <button 
                        id="remove_custom_product_image"
                        type="button" 
                        title="Удалить картинку"
                        style="display: none"
                    >&#10005;</button>
                </figure>

                <input 
                    id="testwork_401_custom_product_image" 
                    name="testwork_401_custom_product_image"
                    type="file" 
                    accept="image/*"
                    class="short input"
                    style="display:none"
                    required
                />
            </div>

            <div class="custom-item-field">
                <label for="testwork_401_post_date">Дата создания</label>
                <input 
                    id="testwork_401_post_date" 
                    name="testwork_401_post_date"
                    type="datetime-local" 
                    value="<?= stripslashes(date('Y-m-d\TH:i')) ?>" 
                    class="datepicker short input"
                />
            </div>

            <div class="custom-item-field">
                <label for="testwork_401_product_type">Тип продукта</label>
                <select 
                    name="testwork_401_product_type" 
                    id="testwork_401_product_type"
                    class="short select"
                >
                    <option value="">Не выбран...</option>
                    <option value="rare">Rare</option>
                    <option value="frequent">Frequent</option>
                    <option value="unusual">Unusual</option>
                </select>
            </div>

            <div class="custom-item-field">
                <label for="testwork_401_product_price">Цена товара</label>
                <input 
                    id="testwork_401_product_price" 
                    name="testwork_401_product_price"
                    type="number" 
                    min="0" 
                    step="0.01"
                    value="0" 
                />
            </div>

            <div class="custom-item-field">
                <button type="button" id="clear_product_custom_fields_btn">&#10005; Очистить</button>
                <button type="submit" name="save" id="publish" class="new-submit-button">&#10003; Сохранить</button>
            </div>
        </div>
    </form>
    <?php
}
