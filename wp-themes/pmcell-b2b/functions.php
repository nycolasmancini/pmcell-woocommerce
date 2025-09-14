<?php
/**
 * PMCell B2B Theme Functions - CLEAN VERSION
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * =============================================================================
 * CORREÇÕES DE COMPATIBILIDADE PHP 8.2+ E SUPRESSÃO DE ERROS
 * =============================================================================
 */

// Suprimir erros deprecated em produção
if (!defined('WP_DEBUG') || !WP_DEBUG) {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED & ~E_STRICT);
    @ini_set('display_errors', 0);
    @ini_set('log_errors', 1);
}

// Corrigir propriedades dinâmicas deprecated no PHP 8.2+
add_action('init', 'pmcell_fix_dynamic_properties_warnings', 1);
function pmcell_fix_dynamic_properties_warnings() {
    if (version_compare(PHP_VERSION, '8.2.0', '>=')) {
        // Silenciar warnings específicos de propriedades dinâmicas
        set_error_handler(function($errno, $errstr, $errfile, $errline) {
            // Ignorar warnings de propriedades dinâmicas do WooCommerce
            if (strpos($errstr, 'Creation of dynamic property') !== false) {
                return true; // Suprimir o warning
            }
            if (strpos($errstr, '::$ID is deprecated') !== false || 
                strpos($errstr, '::$filter is deprecated') !== false) {
                return true; // Suprimir warnings específicos
            }
            // Para outros erros, usar handler padrão
            return false;
        }, E_DEPRECATED | E_USER_DEPRECATED | E_STRICT);
    }
}

// Compatibilidade com WooCommerce - prevenir acesso a propriedades inexistentes
add_filter('woocommerce_product_class', 'pmcell_wc_product_compatibility', 10, 2);
function pmcell_wc_product_compatibility($classname, $product_type) {
    // Não modificar em modo debug
    if (defined('WP_DEBUG') && WP_DEBUG) {
        return $classname;
    }
    
    // Wrapper para capturar e silenciar warnings de propriedades
    if (class_exists($classname)) {
        return $classname;
    }
    
    return $classname;
}

// Filtro para limpar output de warnings que escaparam
add_action('wp_head', 'pmcell_clean_php_warnings_output', 1);
function pmcell_clean_php_warnings_output() {
    if (!is_admin()) {
        ob_start('pmcell_filter_php_warnings');
    }
}

function pmcell_filter_php_warnings($buffer) {
    // Remover warnings específicos que podem aparecer no HTML
    $patterns = array(
        '/Deprecated:.*?WC_Product_Simple::\$ID.*?line \d+/is',
        '/Deprecated:.*?WC_Product_Simple::\$filter.*?line \d+/is',
        '/Deprecated:.*?Creation of dynamic property.*?line \d+/is',
        '/Deprecated:.*?sidebar\.php.*?since version.*?line \d+/is',
        '/\s*<br\s*\/?>\s*/i' // Remove <br> tags órfãos de warnings
    );
    
    foreach ($patterns as $pattern) {
        $buffer = preg_replace($pattern, '', $buffer);
    }
    
    return $buffer;
}

// Theme support
add_theme_support('woocommerce');
add_theme_support('post-thumbnails');
add_theme_support('title-tag');
add_theme_support('custom-logo');

// CRITICAL: Desabilitar layout padrão WooCommerce que interfere com nosso grid
add_filter('woocommerce_enqueue_styles', 'pmcell_dequeue_wc_layout');
function pmcell_dequeue_wc_layout($enqueue_styles) {
    // Remover apenas o layout, mantendo outros estilos essenciais
    unset($enqueue_styles['woocommerce-layout']);
    return $enqueue_styles;
}

// Modern CSS loading system - hierarchical and optimized
function pmcell_enqueue_styles() {
    // Inter font - load first for FOUC prevention
    wp_enqueue_style('inter-font', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap', array(), null);
    
    // 1. Modern Design System - Base foundation
    wp_enqueue_style('pmcell-design-system', get_template_directory_uri() . '/css/modern-design-system.css', array(), '2.0.0');
    
    // 2. Main theme stylesheet - built on design system
    wp_enqueue_style('pmcell-main', get_stylesheet_uri(), array('pmcell-design-system'), '2.0.0');
    
    // 3. Modern Product Cards - enhanced product display
    wp_enqueue_style('pmcell-product-cards', get_template_directory_uri() . '/css/modern-product-cards.css', array('pmcell-design-system'), '2.0.0');
    
    // 4. Shop Header - modern header layout
    wp_enqueue_style('pmcell-shop-header', get_template_directory_uri() . '/css/shop-header.css', array('pmcell-design-system'), '2.7.0');
    
    // 5. Micro-interactions - subtle animations and feedback
    wp_enqueue_style('pmcell-micro-interactions', get_template_directory_uri() . '/css/micro-interactions.css', array('pmcell-design-system'), '2.0.0');
    
    // 6. Mobile Optimizations - responsive enhancements
    wp_enqueue_style('pmcell-mobile-optimizations', get_template_directory_uri() . '/css/mobile-optimizations.css', array('pmcell-design-system'), '2.0.0');
    
    // 7. Shop Layout - general shop styles (maintain compatibility)
    wp_enqueue_style('pmcell-shop-layout', get_template_directory_uri() . '/css/shop-layout.css', array('pmcell-design-system'), '2.0.0');
    
    // 8. WooCommerce Override - CRITICAL: Load after WooCommerce styles to force our grid
    wp_enqueue_style('pmcell-woocommerce-override', 
        get_template_directory_uri() . '/css/woocommerce-override.css',
        array('woocommerce-layout', 'woocommerce-general', 'pmcell-product-cards'), 
        '1.0.0'
    );
    
    // 9. SCROLL FIX - NUCLEAR: Emergency CSS to force scroll functionality with maximum priority
    wp_enqueue_style('pmcell-scroll-fix', 
        get_template_directory_uri() . '/css/scroll-fix.css', 
        array(), 
        '1.0.0', 
        'all'
    );
    wp_add_inline_style('pmcell-scroll-fix', '/* FORCE SCROLL LOAD - EMERGENCY */ body { overflow-y: auto !important; }');
}
add_action('wp_enqueue_scripts', 'pmcell_enqueue_styles', 10);

// Enqueue JavaScript files
function pmcell_enqueue_scripts() {
    // Cart update script for WooCommerce pages
    if (function_exists('is_woocommerce')) {
        wp_enqueue_script('pmcell-cart-update', 
            get_template_directory_uri() . '/js/cart-update.js', 
            array('jquery', 'wc-add-to-cart'), 
            '1.1.0', 
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'pmcell_enqueue_scripts');

// WooCommerce cart fragments for AJAX updates
function pmcell_add_to_cart_fragments($fragments) {
    ob_start();
    ?>
    <a href="<?php echo wc_get_cart_url(); ?>" class="cart-link">
        <svg class="cart-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM20.25 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <?php if (WC()->cart->get_cart_contents_count() > 0) : ?>
            <span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
        <?php endif; ?>
    </a>
    <?php
    $fragments['a.cart-link'] = ob_get_clean();
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'pmcell_add_to_cart_fragments');

// No more inline CSS - everything will be in files

/**
 * =============================================================================
 * SISTEMA DE PREÇOS POR QUANTIDADE B2B
 * Sistema customizado para preços escalonados por produto
 * =============================================================================
 */

// 1. ADICIONAR CAMPOS PERSONALIZADOS NO ADMIN DE PRODUTO
add_action('woocommerce_product_options_pricing', 'pmcell_add_bulk_pricing_fields');
function pmcell_add_bulk_pricing_fields() {
    echo '<div class="options_group">';
    echo '<h3>' . __('Preços B2B por Quantidade', 'pmcell') . '</h3>';
    
    // Campo para quantidade mínima
    woocommerce_wp_text_input(array(
        'id' => '_pmcell_bulk_min_qty',
        'label' => __('Quantidade Mínima para Preço Especial', 'pmcell'),
        'description' => __('Quantidade mínima para aplicar o preço especial B2B', 'pmcell'),
        'type' => 'number',
        'custom_attributes' => array(
            'step' => '1',
            'min' => '1'
        )
    ));
    
    // Campo para preço especial
    woocommerce_wp_text_input(array(
        'id' => '_pmcell_bulk_special_price',
        'label' => __('Preço Especial (R$)', 'pmcell'),
        'description' => __('Preço unitário quando comprar a quantidade mínima ou mais', 'pmcell'),
        'type' => 'number',
        'custom_attributes' => array(
            'step' => '0.01',
            'min' => '0'
        )
    ));
    
    echo '</div>';
}

// 2. SALVAR OS CAMPOS PERSONALIZADOS
add_action('woocommerce_process_product_meta', 'pmcell_save_bulk_pricing_fields');
function pmcell_save_bulk_pricing_fields($post_id) {
    // Salvar quantidade mínima
    $bulk_min_qty = isset($_POST['_pmcell_bulk_min_qty']) ? sanitize_text_field($_POST['_pmcell_bulk_min_qty']) : '';
    update_post_meta($post_id, '_pmcell_bulk_min_qty', $bulk_min_qty);
    
    // Salvar preço especial
    $bulk_special_price = isset($_POST['_pmcell_bulk_special_price']) ? sanitize_text_field($_POST['_pmcell_bulk_special_price']) : '';
    update_post_meta($post_id, '_pmcell_bulk_special_price', $bulk_special_price);
}

// 3. LÓGICA DE APLICAÇÃO DOS PREÇOS NO CARRINHO
add_action('woocommerce_before_calculate_totals', 'pmcell_apply_bulk_pricing', 9999);
function pmcell_apply_bulk_pricing($cart) {
    if (is_admin() && !defined('DOING_AJAX')) return;
    if (did_action('woocommerce_before_calculate_totals') >= 2) return;

    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        $product_id = $cart_item['product_id'];
        $quantity = $cart_item['quantity'];
        
        // Obter configurações de preço bulk
        $bulk_min_qty = get_post_meta($product_id, '_pmcell_bulk_min_qty', true);
        $bulk_special_price = get_post_meta($product_id, '_pmcell_bulk_special_price', true);
        
        // Verificar se tem configuração de bulk pricing e se quantidade atinge o mínimo
        if (!empty($bulk_min_qty) && !empty($bulk_special_price) && $quantity >= intval($bulk_min_qty)) {
            // Aplicar preço especial
            $cart_item['data']->set_price(floatval($bulk_special_price));
        }
    }
}

// 4. EXIBIR TABELA DE PREÇOS NA PÁGINA DO PRODUTO
add_action('woocommerce_single_product_summary', 'pmcell_display_bulk_pricing_table', 25);
function pmcell_display_bulk_pricing_table() {
    global $product;
    
    $product_id = $product->get_id();
    $bulk_min_qty = get_post_meta($product_id, '_pmcell_bulk_min_qty', true);
    $bulk_special_price = get_post_meta($product_id, '_pmcell_bulk_special_price', true);
    $regular_price = $product->get_regular_price();
    
    // Só exibir se tiver configuração de bulk pricing
    if (!empty($bulk_min_qty) && !empty($bulk_special_price) && !empty($regular_price)) {
        $savings = floatval($regular_price) - floatval($bulk_special_price);
        $savings_percent = round(($savings / floatval($regular_price)) * 100, 1);
        
        echo '<div class="pmcell-bulk-pricing-table">';
        echo '<h3 class="bulk-pricing-title">💰 Preços por Quantidade</h3>';
        echo '<div class="pricing-tiers">';
        
        // Tier 1: Preço Normal
        echo '<div class="pricing-tier tier-normal">';
        echo '<div class="tier-qty">1 - ' . (intval($bulk_min_qty) - 1) . ' peças</div>';
        echo '<div class="tier-price">R$ ' . number_format(floatval($regular_price), 2, ',', '.') . ' cada</div>';
        echo '</div>';
        
        // Tier 2: Preço Especial
        echo '<div class="pricing-tier tier-special">';
        echo '<div class="tier-qty">' . $bulk_min_qty . '+ peças</div>';
        echo '<div class="tier-price">R$ ' . number_format(floatval($bulk_special_price), 2, ',', '.') . ' cada</div>';
        echo '<div class="tier-savings">Economia de ' . $savings_percent . '% (R$ ' . number_format($savings, 2, ',', '.') . ' por peça)</div>';
        echo '</div>';
        
        echo '</div>';
        echo '<p class="bulk-pricing-note">💡 <strong>Dica:</strong> Compre ' . $bulk_min_qty . ' ou mais peças e ganhe desconto automático!</p>';
        echo '</div>';
    }
}

// 5. ADICIONAR ESTILOS CSS PARA A TABELA DE PREÇOS
add_action('wp_head', 'pmcell_bulk_pricing_styles');
function pmcell_bulk_pricing_styles() {
    ?>
    <style>
    .pmcell-bulk-pricing-table {
        margin: 20px 0;
        padding: 20px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        border: 2px solid #e9ecef;
    }
    
    .bulk-pricing-title {
        color: #2c5aa0;
        font-size: 18px;
        font-weight: 600;
        margin: 0 0 15px 0;
        text-align: center;
    }
    
    .pricing-tiers {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 15px;
    }
    
    .pricing-tier {
        padding: 15px;
        border-radius: 8px;
        text-align: center;
        position: relative;
    }
    
    .tier-normal {
        background: #fff;
        border: 2px solid #dee2e6;
    }
    
    .tier-special {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border: 2px solid #28a745;
        position: relative;
    }
    
    .tier-special::before {
        content: "🎯 MELHOR PREÇO";
        position: absolute;
        top: -10px;
        left: 50%;
        transform: translateX(-50%);
        background: #ffc107;
        color: #000;
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: bold;
    }
    
    .tier-qty {
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 5px;
    }
    
    .tier-price {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 5px;
    }
    
    .tier-savings {
        font-size: 12px;
        font-weight: 500;
        opacity: 0.9;
    }
    
    .bulk-pricing-note {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 6px;
        padding: 10px;
        margin: 0;
        font-size: 14px;
        text-align: center;
        color: #856404;
    }
    
    @media (max-width: 768px) {
        .pricing-tiers {
            grid-template-columns: 1fr;
        }
        
        .pmcell-bulk-pricing-table {
            padding: 15px;
        }
    }
    </style>
    <?php
}

// 6. ATUALIZAÇÃO DINÂMICA DE PREÇOS VIA JAVASCRIPT
add_action('wp_footer', 'pmcell_bulk_pricing_script');
function pmcell_bulk_pricing_script() {
    if (is_product()) {
        global $product;
        
        $product_id = $product->get_id();
        $bulk_min_qty = get_post_meta($product_id, '_pmcell_bulk_min_qty', true);
        $bulk_special_price = get_post_meta($product_id, '_pmcell_bulk_special_price', true);
        $regular_price = $product->get_regular_price();
        
        if (!empty($bulk_min_qty) && !empty($bulk_special_price)) {
            ?>
            <script>
            jQuery(document).ready(function($) {
                var bulkMinQty = <?php echo intval($bulk_min_qty); ?>;
                var regularPrice = <?php echo floatval($regular_price); ?>;
                var specialPrice = <?php echo floatval($bulk_special_price); ?>;
                
                function updatePriceDisplay() {
                    var qty = parseInt($('.qty').val()) || 1;
                    var pricePerUnit = qty >= bulkMinQty ? specialPrice : regularPrice;
                    var totalPrice = pricePerUnit * qty;
                    
                    // Atualizar display do preço
                    $('.woocommerce-Price-amount bdi').text('R$ ' + pricePerUnit.toFixed(2).replace('.', ','));
                    
                    // Destacar tier ativo na tabela
                    $('.pricing-tier').removeClass('active');
                    if (qty >= bulkMinQty) {
                        $('.tier-special').addClass('active');
                    } else {
                        $('.tier-normal').addClass('active');
                    }
                }
                
                // Executar ao mudar quantidade
                $(document).on('change input', '.qty', updatePriceDisplay);
                
                // CSS para tier ativo
                $('<style>.pricing-tier.active { transform: scale(1.05); box-shadow: 0 4px 15px rgba(0,0,0,0.2); }</style>').appendTo('head');
            });
            </script>
            <?php
        }
    }
}

/**
 * =============================================================================
 * FUNÇÕES HELPER PARA NOVOS CARDS DE PRODUTOS
 * Suporte para os cards customizados com bulk pricing dinâmico
 * =============================================================================
 */

// OBTER CATEGORIA PRINCIPAL DO PRODUTO
function pmcell_get_product_main_category($product_id) {
    $terms = wp_get_post_terms($product_id, 'product_cat', array('orderby' => 'term_order', 'order' => 'ASC', 'fields' => 'names'));
    
    if (!empty($terms) && !is_wp_error($terms)) {
        // Retorna a primeira categoria (principal)
        return $terms[0];
    }
    
    return '';
}

// OBTER QUANTIDADE ATUAL DO PRODUTO NO CARRINHO
function pmcell_get_cart_item_quantity($product_id) {
    // Verificações de segurança
    if (!function_exists('WC') || !WC() || !WC()->cart || !$product_id) {
        return 0;
    }
    
    // Verificar se carrinho existe e tem itens
    $cart = WC()->cart;
    if (!$cart || !method_exists($cart, 'get_cart') || empty($cart->get_cart())) {
        return 0;
    }
    
    // Procurar produto no carrinho
    try {
        foreach ($cart->get_cart() as $cart_item) {
            if (isset($cart_item['product_id']) && $cart_item['product_id'] == $product_id) {
                return intval($cart_item['quantity']);
            }
        }
    } catch (Exception $e) {
        // Log error mas não quebrar a página
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('PMCell Cart Error: ' . $e->getMessage());
        }
    }
    
    return 0;
}

// FORMATAR INFORMAÇÕES DE BULK PRICING
function pmcell_format_bulk_pricing_info($product_id) {
    $bulk_min_qty = get_post_meta($product_id, '_pmcell_bulk_min_qty', true);
    $bulk_special_price = get_post_meta($product_id, '_pmcell_bulk_special_price', true);
    
    if (!empty($bulk_min_qty) && !empty($bulk_special_price)) {
        return sprintf(
            '+ %d un. - R$ %s',
            intval($bulk_min_qty),
            number_format(floatval($bulk_special_price), 2, ',', '.')
        );
    }
    
    return '';
}

// AJAX: ATUALIZAR QUANTIDADE NO CARRINHO
add_action('wp_ajax_pmcell_update_cart_quantity', 'pmcell_ajax_update_cart_quantity');
add_action('wp_ajax_nopriv_pmcell_update_cart_quantity', 'pmcell_ajax_update_cart_quantity');

function pmcell_ajax_update_cart_quantity() {
    // Verificar nonce de segurança
    check_ajax_referer('pmcell_cart_nonce', 'nonce');
    
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    
    // Validar dados
    if (!$product_id || $quantity < 0 || $quantity > 9999) {
        wp_send_json_error('Dados inválidos');
        return;
    }
    
    // Obter o produto
    $product = wc_get_product($product_id);
    if (!$product) {
        wp_send_json_error('Produto não encontrado');
        return;
    }
    
    // Verificar se já existe no carrinho
    $cart_item_key = null;
    foreach (WC()->cart->get_cart() as $key => $cart_item) {
        if ($cart_item['product_id'] == $product_id) {
            $cart_item_key = $key;
            break;
        }
    }
    
    try {
        if ($quantity == 0) {
            // Remover do carrinho se quantidade for 0
            if ($cart_item_key) {
                WC()->cart->remove_cart_item($cart_item_key);
            }
        } else {
            if ($cart_item_key) {
                // Atualizar quantidade existente
                WC()->cart->set_quantity($cart_item_key, $quantity);
            } else {
                // Adicionar novo item ao carrinho
                WC()->cart->add_to_cart($product_id, $quantity);
            }
        }
        
        // Recalcular totais
        WC()->cart->calculate_totals();
        
        // Obter dados atualizados do carrinho
        $cart_count = WC()->cart->get_cart_contents_count();
        $cart_total = WC()->cart->get_cart_total();
        
        // Calcular preço atual baseado na quantidade
        $bulk_min_qty = get_post_meta($product_id, '_pmcell_bulk_min_qty', true);
        $bulk_special_price = get_post_meta($product_id, '_pmcell_bulk_special_price', true);
        $regular_price = $product->get_regular_price();
        
        $is_bulk_active = !empty($bulk_min_qty) && !empty($bulk_special_price) && $quantity >= intval($bulk_min_qty);
        $current_price = $is_bulk_active ? floatval($bulk_special_price) : floatval($regular_price);
        
        wp_send_json_success(array(
            'quantity' => $quantity,
            'cart_count' => $cart_count,
            'cart_total' => $cart_total,
            'current_price' => $current_price,
            'is_bulk_active' => $is_bulk_active,
            'formatted_price' => 'R$ ' . number_format($current_price, 2, ',', '.'),
            'message' => $quantity > 0 ? 'Carrinho atualizado' : 'Item removido do carrinho'
        ));
        
    } catch (Exception $e) {
        wp_send_json_error('Erro ao atualizar carrinho: ' . $e->getMessage());
    }
}

// ENQUEUE DO JAVASCRIPT DOS CARDS DE PRODUTOS
add_action('wp_enqueue_scripts', 'pmcell_enqueue_product_cards_script');
function pmcell_enqueue_product_cards_script() {
    if (is_shop() || is_product_category() || is_product_tag()) {
        wp_enqueue_script(
            'pmcell-product-cards',
            get_template_directory_uri() . '/js/product-cards.js',
            array('jquery'),
            '1.0.0',
            true
        );
        
        // Localizar script com dados necessários
        wp_localize_script('pmcell-product-cards', 'pmcell_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('pmcell_cart_nonce'),
            'messages' => array(
                'updating' => 'Atualizando...',
                'error' => 'Erro ao atualizar carrinho',
                'success' => 'Carrinho atualizado'
            )
        ));
    }
}

// HOOKS PARA FRAGMENTOS DO CARRINHO (AJAX)
add_filter('woocommerce_add_to_cart_fragments', 'pmcell_cart_quantity_fragments');
function pmcell_cart_quantity_fragments($fragments) {
    // Fragment para quantidade específica de produtos (usado nos cards)
    ob_start();
    ?>
    <span class="pmcell-cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
    <?php
    $fragments['.pmcell-cart-count'] = ob_get_clean();
    
    return $fragments;
}

// ADICIONAR CLASSE CSS PARA PRODUTOS COM BULK PRICING ATIVO
add_filter('woocommerce_post_class', 'pmcell_add_bulk_pricing_class', 10, 2);
function pmcell_add_bulk_pricing_class($classes, $product) {
    if (!is_a($product, 'WC_Product')) {
        return $classes;
    }
    
    $product_id = $product->get_id();
    $cart_quantity = pmcell_get_cart_item_quantity($product_id);
    $bulk_min_qty = get_post_meta($product_id, '_pmcell_bulk_min_qty', true);
    
    if (!empty($bulk_min_qty) && $cart_quantity >= intval($bulk_min_qty)) {
        $classes[] = 'bulk-pricing-active';
    }
    
    return $classes;
}

?>
