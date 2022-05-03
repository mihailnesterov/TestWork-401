/**
 * TestWork 401 Admin Scripts
 */
    const form = document.querySelector('form#post');
    const image = document.querySelector('#custom_product_image');
    const inputFile = document.querySelector('#testwork_401_custom_product_image');
    const uploadImageBtn = document.querySelector('#upload_custom_product_image');
    const removeImageBtn = document.querySelector('#remove_custom_product_image');

    const productPrice = document.querySelector('#testwork_401_product_price');
    const wcRegularPrice = document.querySelector('#_regular_price');
    const clearFieldsBtn = document.querySelector('#clear_product_custom_fields_btn');
    const publishingAction = document.querySelector('#publishing-action');
    const saveBtn = document.querySelector('input[type="submit"][name="save"]#publish');

    const product_id = product.id;

    if( form ) {
        form.setAttribute("enctype", "multipart/form-data");
    }

    /**
     * Загрузка картинки
     */
    if( inputFile ) {

        if( image ) {
            image.addEventListener('click', () => inputFile.click());
        } 

        if ( uploadImageBtn ) {
            uploadImageBtn.addEventListener('click', () => inputFile.click());
        }

        inputFile.addEventListener('change', () => {
            const file = inputFile.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (() => e => image.src = e.target.result)(file);
                reader.readAsDataURL(file);
                image.style.display = 'block';
            }
        });
    }

    /**
     * Удаление картинки
     */
    if( removeImageBtn ) {

        if( image.src === "" ) { 
            removeImageBtn.style.display = 'none';
        }

        /**
         * Удаляем картинку ajax-запросом
         */
        removeImageBtn.addEventListener('click', () => {
            if( confirm('Удалить картинку?') ) {
                jQuery.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'remove_custom_image',
                        product_id
                    },
                    success(res) {
                        image.src = "";
                        image.style.display = 'none';
                        removeImageBtn.style.display = 'none';
                        uploadImageBtn.style.display = 'block';
                        alert('Картинка удалена');
                    },
                    error(error) {
                        console.log(`Ajax error remove custom image: ${JSON.stringify(error)}`);
                    }
                });
            }
        });
    }

    /**
     * Редактирование базовой цены товара
     */
    if( productPrice ) {
        productPrice.addEventListener('input', e => wcRegularPrice.value = e.target.value);
    }

    /**
     * Очистка полей
     */
    if( clearFieldsBtn ) {
        clearFieldsBtn.addEventListener('click', () => {
            const productTitle = document.querySelector('#testwork_401_product_title');
            const productDate = document.querySelector('#testwork_401_post_date');
            const productType = document.querySelector('#testwork_401_product_type');

            if( productTitle ) {
                productTitle.value = '';
            }

            if( productPrice ) {
                productPrice.value = '';
                wcRegularPrice.value = '';
            }

            if( productDate ) {
                productDate.value = '';
            }

            if( productType ) {
                productType.value = '';
            }

            image.src = "";
            image.style.display = 'none';
            removeImageBtn.style.display = 'none';
            uploadImageBtn.style.display = 'block';
        });
    }

    /**
     * Добавить свою кнопку submit (удалить основную)
     */
    if( form && saveBtn && publishingAction ) {
        
        saveBtn.remove();
        
        const newSubmitButton = document.createElement('button');
        
        newSubmitButton.setAttribute('type', 'submit');
        newSubmitButton.classList.add('new-submit-button');
        newSubmitButton.innerHTML = '&check; Сохранить товар';
        
        publishingAction.appendChild(newSubmitButton);
        
        form.addEventListener('submit', () => {
            const spinner = publishingAction.querySelector('.spinner');
            spinner.style.visibility = 'visible';
        });
    }