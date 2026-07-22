<?php
/**
 * Plugin Name: Med Clinic Core
 * Description: Ядро бізнес-логіки медичної клініки. Оптимізує Checkout, замінює сторонні плагіни та надає легку форму зворотного зв'язку без CF7.
 * Version: 1.2.0
 * Author: Lead Engineer & Arsen Kozak
 *
 * Requires PHP: 8.2
 */

declare(strict_types=1);

namespace MedClinic\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Головний клас-ініціалізатор ядра.
 */
final class Bootstrap {
    private static ?self $instance = null;

    private function __construct() {
        $this->init();
    }

    public static function get_instance(): self {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Реєстрація модулів та хуків
     */
    private function init(): void {
        // Модуль швидкого замовлення WooCommerce
        add_action( 'woocommerce_after_add_to_cart_button', [ $this, 'render_quick_buy_button' ], 15 );
        add_filter( 'woocommerce_add_to_cart_redirect', [ $this, 'redirect_to_checkout_on_quick_buy' ], 10, 1 );

        // Модуль оптимізації Checkout (Заміна Saphali)
        add_filter( 'woocommerce_checkout_fields', [ $this, 'optimize_checkout_fields' ], 9999 );

        // Модуль кастомної безопечної контактної форми (Заміна Contact Form 7)
        add_shortcode( 'med_contact_form', [ $this, 'render_contact_form_shortcode' ] );
    }

    /**
     * Рендеринг легкого шорткоду [med_contact_form] без вразливостей Contact Form 7.
     */
    public function render_contact_form_shortcode(): string {
        // Проста обробка відправки (якщо форма була відправлена)
        $message_status = '';
        if ( isset( $_POST['med_submit_lead'] ) && wp_verify_nonce( $_POST['med_nonce'] ?? '', 'med_lead_action' ) ) {
            $name  = sanitize_text_field( $_POST['med_name'] ?? '' );
            $phone = sanitize_text_field( $_POST['med_phone'] ?? '' );

            if ( ! empty( $phone ) ) {
                $to      = get_option( 'admin_email' );
                $subject = 'Нова заявка з сайту (Форма запису)';
                $body    = "Ім'я: {$name}\nТелефон: {$phone}\nДата: " . current_time( 'mysql' );
                $headers = [ 'Content-Type: text/plain; charset=UTF-8' ];

                wp_mail( $to, $subject, $body, $headers );
                $message_status = '<div style="color: #2e7d32; font-weight: bold; margin-bottom: 10px;">Дякуємо! Заявку прийнято, ми вам зателефонуємо.</div>';
            } else {
                $message_status = '<div style="color: #c62828; font-weight: bold; margin-bottom: 10px;">Будь ласка, введіть номер телефону.</div>';
            }
        }

        ob_start();
        ?>
        <div class="med-custom-form-wrapper">
            <?php echo $message_status; ?>
            <form method="post" class="wpcf7-form">
                <?php wp_nonce_field( 'med_lead_action', 'med_nonce' ); ?>
                
                <p>
                    <label for="med_name">Ваше ім'я</label>
                    <input type="text" id="med_name" name="med_name" class="wpcf7-form-control" placeholder="Іван" required>
                </p>

                <p>
                    <label for="med_phone">Номер телефону</label>
                    <input type="tel" id="med_phone" name="med_phone" class="wpcf7-form-control" placeholder="+380..." required>
                </p>

                <p>
                    <button type="submit" name="med_submit_lead" class="wpcf7-submit">Записатися на прийом</button>
                </p>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Рендеринг кнопки "Купити в один клік"
     */
    public function render_quick_buy_button(): void {
        global $product;

        if ( ! $product || ! $product->is_purchasable() ) {
            return;
        }

        printf(
            '<button type="submit" name="quick_buy" value="%d" class="single_add_to_cart_button button alt quick-buy-btn" style="margin-left: 10px; background-color: #0073aa; color: #fff;">%s</button>',
            absint( $product->get_id() ),
            esc_html__( 'Купити в 1 клік', 'betheme' )
        );
    }

    /**
     * Редірект на Checkout після кліку на "Купити в 1 клік"
     */
    public function redirect_to_checkout_on_quick_buy( string $url ): string {
        if ( isset( $_REQUEST['quick_buy'] ) ) {
            return function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : $url;
        }
        return $url;
    }

    /**
     * Оптимізація полів Checkout
     */
    public function optimize_checkout_fields( array $fields ): array {
        $fields_to_remove = [
            'billing_company',
            'billing_country',
            'billing_address_1',
            'billing_address_2',
            'billing_city',
            'billing_state',
            'billing_postcode'
        ];

        foreach ( $fields_to_remove as $field_key ) {
            if ( isset( $fields['billing'][ $field_key ] ) ) {
                unset( $fields['billing'][ $field_key ] );
            }
        }

        if ( isset( $fields['shipping'] ) ) {
            unset( $fields['shipping'] );
        }

        if ( isset( $fields['billing']['billing_phone'] ) ) {
            $fields['billing']['billing_phone']['required'] = true;
        }

        return $fields;
    }
}

// ТИМЧАСОВИЙ ХІРУРГІЧНИЙ ФІКС ДЛЯ ПРИМУСОВОЇ АКТИВАЦІЇ WPBAKERY
add_action( 'admin_init', function() {
    $plugin_slug = 'js_composer/js_composer.php';
    $active_plugins = get_option( 'active_plugins' );

    if ( is_array( $active_plugins ) && ! in_array( $plugin_slug, $active_plugins, true ) ) {
        $active_plugins[] = $plugin_slug;
        update_option( 'active_plugins', $active_plugins );
        wp_cache_flush();
    }
} );

// ДИНАМІЧНЕ МАСОВЕ ОГОЛОШЕННЯ СУМІСНОСТІ З HPOS
add_action( 'before_woocommerce_init', function() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        $active_plugins = get_option( 'active_plugins' );

        if ( is_array( $active_plugins ) ) {
            foreach ( $active_plugins as $plugin_file ) {
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
                    'custom_order_table',
                    $plugin_file,
                    true
                );
            }
        }
    }
} );

// Ховаємо адмін-бар на фронтенді для всіх, окрім адміністраторів
add_action( 'init', function() {
    if ( is_user_logged_in() && ! current_user_can( 'administrator' ) ) {
        add_filter( 'show_admin_bar', '__return_false' );
    }
} );

// Запуск
Bootstrap::get_instance();