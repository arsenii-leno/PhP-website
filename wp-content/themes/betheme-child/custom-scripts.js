/**
 * Med Clinic Custom Scripts
 * Version: 1.2.0
 * Architecture: Object-Literal Pattern (Enterprise Standard)
 */
(function($) {
    'use strict';

    const MedClinic = {

        // Головний ініціалізатор
        init: function() {
            this.bindCoreEvents();
            this.initSmartCheckout();
            this.initBackNavigation();
            this.initToasts();
            this.initSocialBar();
            this.initLogoLink();
            this.initNovaPoshtaSelects();
        },

        // Утиліта: Debounce (Запобігає витоку пам'яті при масових AJAX-подіях)
        debounce: function(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        },

        // Глобальні події WooCommerce
        bindCoreEvents: function() {
            $(document.body).on('updated_wc_div', () => {
                // Блокування різкого стрибка екрану при оновленні кошика
                setTimeout(() => $('html, body').stop(true, false), 5);
                this.checkEmptyCart();
            });
        },

        // 1. Smart Checkout (Кнопки та скроли)
        initSmartCheckout: function() {

            const injectBtn = this.debounce(() => {

                const $updateBtn = $('button[name="update_cart"]');

                if (
                    $updateBtn.length &&
                    $('.med-smart-checkout-btn').length === 0
                ) {

                    $updateBtn.after(
                        '<button type="button" class="med-smart-checkout-btn">Перейти до оплати</button>'
                    );

                }

            }, 100);

            injectBtn();

            $(document.body).on('updated_wc_div', injectBtn);

            $(document).on('click', '.med-smart-checkout-btn', function(e) {

                e.preventDefault();

                const $target = $('.cart-collaterals');

                if (!$target.length) {
                    return;
                }

                $('html, body').animate({
                    scrollTop: $target.offset().top - 80
                }, 600);

            });

        },

        initNovaPoshtaSelects: function() {

            const initSelectWoo = () => {

                $('#billing_np_city, #billing_np_warehouse').each(function() {

                    const $select = $(this);

                    if (
                        $select.length &&
                        !$select.hasClass('select2-hidden-accessible')
                    ) {

                        $select.selectWoo({
                            width: '100%',
                            language: 'uk'
                        });

                    }

                });

            };

            initSelectWoo();

            $(document.body).on(
                'updated_checkout updated_wc_div',
                this.debounce(initSelectWoo, 100)
            );

        },

        // 2. Авто-рефреш порожнього кошика
        checkEmptyCart: function() {
            if ($('.woocommerce-cart-form__cart-item').length === 0 && window.location.pathname.includes('/cart')) {
                window.location.reload();
            }
        },

        // 3. Навігація "Назад" (Категорії та Товари)
        initBackNavigation: function() {
            const injectCategoryBtn = () => {
                const path = window.location.pathname;
                const search = window.location.search;
                const isCategoryPage = $('body').hasClass('archive') ||
                    $('body').hasClass('tax-product_cat') ||
                    path.includes('/catalog/') ||
                    path.includes('/product-category/') ||
                    search.includes('product_cat=');

                if (isCategoryPage && $('.med-back-to-catalog').length === 0) {
                    const backBtnHTML = `
                        <div class="med-back-nav">
                            <a href="/catalog/" class="med-back-to-catalog">
                                <span class="med-arrow">←</span> Повернутися до каталогу
                            </a>
                        </div>
                    `;
                    const $target = $('.woocommerce-result-count').length > 0 ? $('.woocommerce-result-count') : $('.woocommerce-products-header');
                    if ($target.length > 0) {
                        $target[$target.hasClass('woocommerce-result-count') ? 'before' : 'after'](backBtnHTML);
                    }
                }
            };

            // Первинний запуск
            injectCategoryBtn();

            // AJAX запуск (із захистом Debounce в 150мс)
            const debouncedInject = this.debounce(injectCategoryBtn, 150);
            $(document.body).on('updated_wc_div updated_addons updated_post_meta hashchange vertical_layouts_refresh', debouncedInject);

            // Логіка для сторінки єдиного товару
            if (window.location.pathname.includes('/product/')) {
                if ($('.product_title').length > 0 && $('.med-smart-back').length === 0) {
                    $('.product_title').before(`
                        <div class="med-back-nav" style="margin-bottom: 20px;">
                            <a href="#" class="med-back-to-catalog med-smart-back">
                                <span class="med-arrow">←</span> Повернутися назад
                            </a>
                        </div>
                    `);
                }

                $(document).on('click', '.med-smart-back', function(e) {
                    e.preventDefault();
                    if (document.referrer && document.referrer.includes(window.location.hostname)) {
                        window.history.back();
                    } else {
                        window.location.href = '/catalog/';
                    }
                });
            }
        },

        // 4. Універсальні Toast-повідомлення
        initToasts: function() {
            const initTimer = ($toast) => {
                let hideTimeout;
                const start = () => {
                    hideTimeout = setTimeout(() => {
                        $toast.fadeOut(600, function() { $(this).remove(); });
                    }, 2500);
                };

                start();
                $toast.on('mouseenter', () => clearTimeout(hideTimeout)).on('mouseleave', start);
            };

            // Статичні повідомлення
            const $staticToast = $('.woocommerce-notices-wrapper .woocommerce-message');
            if ($staticToast.length > 0) {
                initTimer($staticToast);
            }

            // AJAX додавання в кошик
            $(document.body).on('added_to_cart', () => {
                const path = window.location.pathname;
                const isArchive = $('body').hasClass('archive') ||
                    path.includes('/product-category/') ||
                    window.location.search.includes('product_cat=') ||
                    path.includes('/catalog/');

                if (isArchive) {
                    $('.med-ajax-toast-wrapper').remove();

                    const ajaxToastHTML = `
                        <div class="woocommerce-notices-wrapper med-ajax-toast-wrapper">
                            <div class="woocommerce-message" role="alert">
                                <a href="/cart/" class="button wc-forward">Переглянути кошик</a> 
                                Товар успішно додано у ваш кошик.
                            </div>
                        </div>
                    `;

                    $('body').append(ajaxToastHTML);
                    initTimer($('.med-ajax-toast-wrapper .woocommerce-message'));
                }
            });
        },

        // 5. Соціальні іконки (Захист конверсії на Checkout)
        initSocialBar: function() {
            const isWooCommerceFlow = /\/(cart|checkout|order-received)/.test(window.location.pathname);
            const $socialBar = $('.med-header-social');

            if ($socialBar.length === 0) return;

            if (isWooCommerceFlow) {
                $socialBar.remove(); // Прибираємо, щоб не відволікати від оплати
                return;
            }

            const $headerCart = $('a#header_cart');
            if ($headerCart.length > 0) {
                $headerCart.before($socialBar);
            }
        },

        // 6. Клікабельне лого
        initLogoLink: function() {
            const $logo = $('.logo span#logo');
            if ($logo.length > 0 && $logo.parent('a').length === 0) {
                $logo.wrap('<a href="/" title="На Головну"></a>');
            }
        }
    };

    // Глобальна функція для HTML (щоб не зламати старі onClick)
    window.trackSocialClick = function(platform) {
        if (typeof gtag !== 'undefined') {
            gtag('event', 'click_social', {
                'platform': platform,
                'page_location': window.location.href,
                'page_title': document.title
            });
        }
    };

    // Запуск ядра JS після завантаження DOM
    $(document).ready(function() {
        MedClinic.init();
    });

})(jQuery);
