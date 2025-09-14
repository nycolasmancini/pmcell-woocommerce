<?php
/**
 * PMCell B2B - Product Card Template with Bulk Pricing
 * Custom template para cards de produtos com preços dinâmicos por quantidade
 * 
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Verificações de segurança mais robustas
if ( ! $product || ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
    return;
}

// Verificar se WooCommerce está ativo
if ( ! function_exists( 'WC' ) ) {
    return;
}

// Obter dados do produto com verificações
$product_id = $product->get_id();
if ( ! $product_id ) {
    return;
}

$product_name = $product->get_name();
$product_image_url = wp_get_attachment_image_url($product->get_image_id(), 'woocommerce_thumbnail');
$regular_price = $product->get_regular_price();

// Verificar se dados essenciais existem
if ( empty( $product_name ) ) {
    return;
}

// Obter dados de bulk pricing
$bulk_min_qty = get_post_meta($product_id, '_pmcell_bulk_min_qty', true);
$bulk_special_price = get_post_meta($product_id, '_pmcell_bulk_special_price', true);

// Obter categoria principal
$product_category = pmcell_get_product_main_category($product_id);

// Obter quantidade atual no carrinho
$cart_quantity = pmcell_get_cart_item_quantity($product_id);

// Determinar preço atual baseado na quantidade do carrinho
$is_bulk_active = !empty($bulk_min_qty) && !empty($bulk_special_price) && $cart_quantity >= intval($bulk_min_qty);
$current_price = $is_bulk_active ? floatval($bulk_special_price) : floatval($regular_price);

// Link do produto
$product_link = get_permalink($product);
?>

<li <?php wc_product_class('pmcell-product-card', $product); ?> 
    data-product-id="<?php echo esc_attr($product_id); ?>"
    data-regular-price="<?php echo esc_attr($regular_price); ?>"
    data-bulk-price="<?php echo esc_attr($bulk_special_price); ?>"
    data-bulk-min-qty="<?php echo esc_attr($bulk_min_qty); ?>"
    data-current-cart-qty="<?php echo esc_attr($cart_quantity); ?>">
    
    <!-- Container da Imagem com Badge de Preço -->
    <div class="product-image-container">
        <a href="<?php echo esc_url($product_link); ?>" class="product-image-link">
            <?php if ($product_image_url): ?>
                <img src="<?php echo esc_url($product_image_url); ?>" 
                     alt="<?php echo esc_attr($product_name); ?>"
                     class="product-image"
                     loading="lazy">
            <?php else: ?>
                <div class="product-image-placeholder">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                </div>
            <?php endif; ?>
        </a>
        
        <!-- Badge de Preço Dinâmico -->
        <div class="price-badge" data-bulk-active="<?php echo $is_bulk_active ? 'true' : 'false'; ?>">
            <!-- Ícone de Tag de Preço -->
            <svg class="price-tag-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                <path d="M21.41 11.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.42l9 9c.36.36.86.58 1.41.58.55 0 1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41 0-.55-.23-1.06-.59-1.42zM5.5 7C4.67 7 4 6.33 4 5.5S4.67 4 5.5 4 7 4.67 7 5.5 6.33 7 5.5 7z"/>
            </svg>
            <span class="badge-price">R$ <?php echo number_format($current_price, 2, ',', '.'); ?></span>
        </div>
        
        <?php if ($product->is_on_sale()): ?>
            <span class="onsale">Oferta</span>
        <?php endif; ?>
    </div>
    
    <!-- Informações do Produto -->
    <div class="product-info">
        <!-- Nome do Produto -->
        <h3 class="product-name">
            <a href="<?php echo esc_url($product_link); ?>"><?php echo esc_html($product_name); ?></a>
        </h3>
        
        <!-- Categoria do Produto -->
        <?php if (!empty($product_category)): ?>
            <span class="product-category"><?php echo esc_html($product_category); ?></span>
        <?php endif; ?>
        
        <!-- Informação de Bulk Pricing -->
        <?php if (!empty($bulk_min_qty) && !empty($bulk_special_price)): ?>
            <div class="bulk-pricing-info">
                + <?php echo esc_html($bulk_min_qty); ?> un. - R$ <?php echo number_format(floatval($bulk_special_price), 2, ',', '.'); ?>
            </div>
        <?php endif; ?>
        
        <!-- Seletor de Quantidade -->
        <div class="quantity-selector" data-product-id="<?php echo esc_attr($product_id); ?>">
            <button type="button" class="qty-decrease" aria-label="Diminuir quantidade">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13H5v-2h14v2z"/>
                </svg>
            </button>
            
            <input type="number" 
                   class="qty-input" 
                   value="<?php echo esc_attr($cart_quantity); ?>" 
                   min="0" 
                   max="9999"
                   aria-label="Quantidade"
                   readonly>
            
            <button type="button" class="qty-increase" aria-label="Aumentar quantidade">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                </svg>
            </button>
        </div>
        
        <!-- Estado de Loading (oculto por padrão) -->
        <div class="quantity-loading" style="display: none;">
            <svg class="loading-spinner" width="20" height="20" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-dasharray="31.416" stroke-dashoffset="31.416">
                    <animate attributeName="stroke-dasharray" dur="2s" values="0 31.416;15.708 15.708;0 31.416" repeatCount="indefinite"/>
                    <animate attributeName="stroke-dashoffset" dur="2s" values="0;-15.708;-31.416" repeatCount="indefinite"/>
                </circle>
            </svg>
            <span>Atualizando...</span>
        </div>
    </div>
</li>