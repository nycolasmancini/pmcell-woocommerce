/**
 * PMCell B2B Cart Badge Real-time Update
 * Updates cart count badge when products are added/removed
 */

jQuery(function($) {
    'use strict';
    
    // Configuration
    const config = {
        cartLinkSelector: '.cart-link',
        cartCountSelector: '.cart-count',
        animationClass: 'updating',
        animationDuration: 300
    };
    
    /**
     * Update cart count via AJAX
     */
    function updateCartCount() {
        // Use WooCommerce AJAX endpoint
        const ajaxUrl = wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'get_cart_totals');
        
        $.get(ajaxUrl)
            .done(function(data) {
                if (data && data.cart_contents_count !== undefined) {
                    updateCartBadge(data.cart_contents_count);
                }
            })
            .fail(function() {
                // Fallback: refresh fragments
                $(document.body).trigger('wc_fragment_refresh');
            });
    }
    
    /**
     * Update cart badge visual
     * @param {number} count - Number of items in cart
     */
    function updateCartBadge(count) {
        const $cartLink = $(config.cartLinkSelector);
        let $cartCount = $cartLink.find(config.cartCountSelector);
        
        // Add animation class temporarily
        $cartCount.addClass(config.animationClass);
        
        if (count > 0) {
            if ($cartCount.length === 0) {
                // Create badge if it doesn't exist
                $cartLink.append(`<span class="${config.cartCountSelector.replace('.', '')}">${count}</span>`);
                $cartCount = $cartLink.find(config.cartCountSelector);
            } else {
                // Update existing badge
                $cartCount.text(count);
            }
            
            // Show badge with fade in effect
            $cartCount.fadeIn(200);
        } else {
            // Hide badge with fade out effect
            $cartCount.fadeOut(200, function() {
                $(this).remove();
            });
        }
        
        // Remove animation class after animation completes
        setTimeout(() => {
            $cartCount.removeClass(config.animationClass);
        }, config.animationDuration);
    }
    
    /**
     * Handle WooCommerce cart events
     */
    function bindCartEvents() {
        // Listen for WooCommerce cart events
        $(document.body).on('added_to_cart', function(event, fragments, cart_hash) {
            updateCartCount();
        });
        
        $(document.body).on('removed_from_cart', function() {
            updateCartCount();
        });
        
        $(document.body).on('updated_cart_totals', function() {
            updateCartCount();
        });
        
        // Handle fragment refresh events
        $(document.body).on('wc_fragments_refreshed wc_fragments_loaded', function() {
            updateCartCount();
        });
        
        // Handle quantity changes in cart
        $(document.body).on('change', '.qty', function() {
            // Debounce the update to avoid excessive requests
            clearTimeout(window.cartUpdateTimeout);
            window.cartUpdateTimeout = setTimeout(updateCartCount, 500);
        });
    }
    
    /**
     * Initialize cart update functionality
     */
    function init() {
        // Only run on shop pages
        if (typeof wc_add_to_cart_params === 'undefined') {
            return;
        }
        
        bindCartEvents();
        
        // Initial count update
        updateCartCount();
        
        // Debug info
        if (window.console && window.console.log) {
            console.log('PMCell Cart Badge Update initialized');
        }
    }
    
    // Initialize when DOM is ready
    $(document).ready(init);
    
    // Expose functions for debugging
    window.pmcellCart = {
        updateCount: updateCartCount,
        updateBadge: updateCartBadge
    };
});