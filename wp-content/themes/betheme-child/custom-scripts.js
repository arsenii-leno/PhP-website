jQuery(document.body).ready(function($) {
    
    /**
     * 1. SMART CHECKOUT: SMOOTH SCROLL & NAVIGATION
     */
    
    // Prevent default WooCommerce scroll jump on cart update
    $(document.body).on('updated_wc_div', function() {
        setTimeout(function() {
            $('html, body').stop(true, false);
        }, 5);
    });

    // Inject "Proceed to Payment" button after the native update button
    function injectSmartButton() {
        if ($('.med-smart-checkout-btn').length === 0 && $('button[name="update_cart"]').length > 0) {
            $('button[name="update_cart"]').after('<button type="button" class="med-smart-checkout-btn">Перейти до оплати</button>');
        }
    }
    
    injectSmartButton();
    $(document.body).on('updated_wc_div', injectSmartButton);

    // Smooth scroll to cart totals/collaterals
    $(document).on('click', '.med-smart-checkout-btn', function(e) {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: $('.cart-collaterals').offset().top - 80
        }, 600);
    });


    /**
     * 2. AUTO-REFRESH ON EMPTY CART
     */
    $(document.body).on('updated_wc_div', function() {
        if ($('.woocommerce-cart-form__cart-item').length === 0 && window.location.href.includes('/cart')) {
            window.location.reload();
        }
    });


    /**
     * 3. BACK NAVIGATION (CATEGORIES & SINGLE PRODUCTS)
     */
    
    // Injects "Back to Catalog" button on category/archive pages (compatible with AJAX filters)
    function injectCategoryBackButton() {
        var isCategoryPage = $('body').hasClass('archive') || 
                             $('body').hasClass('tax-product_cat') || 
                             window.location.href.includes('/product-category/') || 
                             window.location.search.includes('product_cat=');

        if (isCategoryPage && $('.med-back-to-catalog').length === 0) {
            var backBtnHTML = `
                <div class="med-back-nav">
                    <a href="/catalog/" class="med-back-to-catalog">
                        <span class="med-arrow">←</span> Повернутися до каталогу
                    </a>
                </div>
            `;
            
            var $target = $('.woocommerce-result-count').length > 0 ? $('.woocommerce-result-count') : $('.woocommerce-products-header');
            if ($target.length > 0) {
                $target[$target.hasClass('woocommerce-result-count') ? 'before' : 'after'](backBtnHTML);
            }
        }
    }

    // Initial injection on load
    injectCategoryBackButton();

    // Re-inject button when content reloads via AJAX filters, pagination or sorting
    $(document.body).on('updated_wc_div updated_addons updated_post_meta hashchange vertical_layouts_refresh', function() {
        setTimeout(injectCategoryBackButton, 100);
    });

    // Single Product Pages: Smart History Back
    if (window.location.href.includes('/product/')) {
        var productBackBtnHTML = `
            <div class="med-back-nav" style="margin-bottom: 20px;">
                <a href="#" class="med-back-to-catalog med-smart-back">
                    <span class="med-arrow">←</span> Повернутися назад
                </a>
            </div>
        `;
        
        if ($('.product_title').length > 0) {
            $('.product_title').before(productBackBtnHTML);
        }

        $('.med-smart-back').on('click', function(e) {
            e.preventDefault();
            var currentHost = window.location.hostname;
            
            // Navigate back if referrer is internal, otherwise fallback to global catalog
            if (document.referrer && document.referrer.includes(currentHost)) {
                window.history.back();
            } else {
                window.location.href = '/catalog/';
            }
        });
    }


    /**
     * 4. UNIVERSAL TOAST MESSAGES (STATIC & AJAX)
     */
    
    // Toast auto-hide timer with hover pause functionality
    function initToastTimer($toastElement) {
        var hideTimeout;
        
        function startTimer() {
            hideTimeout = setTimeout(function() {
                $toastElement.fadeOut(600, function() {
                    $(this).remove(); 
                });
            }, 2000); 
        }
        
        startTimer();

        $toastElement.on('mouseenter', function() {
            clearTimeout(hideTimeout);
        }).on('mouseleave', function() {
            startTimer();
        });
    }

    // Initialize static WooCommerce notices on page load
    var $staticToast = $('.woocommerce-notices-wrapper .woocommerce-message');
    if ($staticToast.length > 0) {
        initToastTimer($staticToast);
    }

    // Display floating toast notification on AJAX add-to-cart event (Supports filtered archives)
    $(document.body).on('added_to_cart', function() {
        var isArchive = $('body').hasClass('archive') || 
                        window.location.href.includes('/product-category/') || 
                        window.location.search.includes('product_cat=') ||
                        window.location.pathname.includes('/catalog/');

        if (isArchive) {
            $('.med-ajax-toast-wrapper').remove(); // Clear active toasts
            
            var ajaxToastHTML = `
                <div class="woocommerce-notices-wrapper med-ajax-toast-wrapper">
                    <div class="woocommerce-message" role="alert">
                        <a href="/cart/" class="button wc-forward">Переглянути кошик</a> 
                        Товар успішно додано у ваш кошик.
                    </div>
                </div>
            `;
            
            $('body').append(ajaxToastHTML);
            initToastTimer($('.med-ajax-toast-wrapper .woocommerce-message'));
        }
    });

});


/**
 * 5. SOCIAL MEDIA INTEGRATION & ANALYTICS (VANILLA JS)
 */

// Track outbound social media links via Google Analytics (gtag.js)
function trackSocialClick(platform) {
    if (typeof gtag !== 'undefined') {
        gtag('event', 'click_social', {
            'platform': platform,
            'page_location': window.location.href,
            'page_title': document.title
        });
    }
    console.log('Social click tracked:', platform);
}

// DomReady: Manage visibility and placement of the social icons bar
document.addEventListener('DOMContentLoaded', function() {
    const isWooCommerceFlow = /\/(cart|checkout|order-received)/.test(window.location.pathname);
    const socialBar = document.querySelector('.med-header-social');
    
    if (!socialBar) return;

    // Remove social bar entirely on checkout/cart funnels to prevent conversion drops
    if (isWooCommerceFlow) {
        socialBar.remove();
        return;
    }

    // Relocate social bar to header next to the cart icon
    const headerCart = document.querySelector('a#header_cart');
    if (headerCart && headerCart.parentNode) {
        headerCart.before(socialBar);
    } else {
        console.warn("Cart icon not found. Social bar remains in default container.");
    }
});

// MainPage: Add clickable logo
jQuery(document).ready(function($) {
    $('.logo span#logo').wrap('<a href="/" title="На Головну"></a>');
});