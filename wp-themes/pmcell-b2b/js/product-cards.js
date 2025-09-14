/**
 * PMCell B2B Product Cards - Interactive JavaScript
 * Gerencia seletores de quantidade e bulk pricing dinâmico
 * Version: 1.0.0
 */

(function($) {
    'use strict';

    class PMCellProductCard {
        constructor(cardElement) {
            this.card = cardElement;
            this.$card = $(cardElement);
            this.productId = this.card.dataset.productId;
            this.regularPrice = parseFloat(this.card.dataset.regularPrice) || 0;
            this.bulkPrice = parseFloat(this.card.dataset.bulkPrice) || 0;
            this.bulkMinQty = parseInt(this.card.dataset.bulkMinQty) || 0;
            this.currentCartQty = parseInt(this.card.dataset.currentCartQty) || 0;
            
            // Elementos DOM
            this.$quantityInput = this.$card.find('.qty-input');
            this.$decreaseBtn = this.$card.find('.qty-decrease');
            this.$increaseBtn = this.$card.find('.qty-increase');
            this.$priceBadge = this.$card.find('.price-badge');
            this.$badgePrice = this.$card.find('.badge-price');
            this.$quantitySelector = this.$card.find('.quantity-selector');
            this.$loadingState = this.$card.find('.quantity-loading');
            
            // Estado
            this.isUpdating = false;
            this.updateTimeout = null;
            
            this.init();
        }
        
        init() {
            this.setupEventListeners();
            this.updatePriceDisplay(this.currentCartQty);
            
            // Debug logging
            if (window.pmcell_debug) {
                console.log(`PMCell Card initialized for product ${this.productId}`, {
                    regularPrice: this.regularPrice,
                    bulkPrice: this.bulkPrice,
                    bulkMinQty: this.bulkMinQty,
                    currentCartQty: this.currentCartQty
                });
            }
        }
        
        setupEventListeners() {
            // Botões de aumentar/diminuir
            this.$decreaseBtn.on('click', (e) => {
                e.preventDefault();
                this.decreaseQuantity();
            });
            
            this.$increaseBtn.on('click', (e) => {
                e.preventDefault();
                this.increaseQuantity();
            });
            
            // Input de quantidade (apenas para visualização, readonly)
            this.$quantityInput.on('focus', () => {
                this.$quantityInput.select();
            });
            
            // Prevent manual input para manter controle total
            this.$quantityInput.on('keydown', (e) => {
                e.preventDefault();
                
                // Permitir apenas setas do teclado
                if (e.keyCode === 38) { // Seta para cima
                    this.increaseQuantity();
                } else if (e.keyCode === 40) { // Seta para baixo
                    this.decreaseQuantity();
                }
            });
            
            // Detectar quando input perde foco com valor alterado
            this.$quantityInput.on('change', () => {
                let newValue = parseInt(this.$quantityInput.val()) || 0;
                newValue = Math.max(0, Math.min(9999, newValue)); // Validar limites
                this.setQuantity(newValue);
            });
        }
        
        decreaseQuantity() {
            if (this.isUpdating) return;
            
            const currentQty = parseInt(this.$quantityInput.val()) || 0;
            const newQty = Math.max(0, currentQty - 1);
            this.setQuantity(newQty);
        }
        
        increaseQuantity() {
            if (this.isUpdating) return;
            
            const currentQty = parseInt(this.$quantityInput.val()) || 0;
            const newQty = Math.min(9999, currentQty + 1);
            this.setQuantity(newQty);
        }
        
        setQuantity(quantity) {
            if (this.isUpdating) return;
            
            const validQty = Math.max(0, Math.min(9999, parseInt(quantity) || 0));
            
            // Atualizar UI imediatamente para responsividade
            this.$quantityInput.val(validQty);
            this.updatePriceDisplay(validQty);
            
            // Debounce para evitar múltiplas chamadas AJAX
            clearTimeout(this.updateTimeout);
            this.updateTimeout = setTimeout(() => {
                this.updateCart(validQty);
            }, 300);
        }
        
        updatePriceDisplay(quantity) {
            const isBulkActive = this.bulkMinQty > 0 && this.bulkPrice > 0 && quantity >= this.bulkMinQty;
            const currentPrice = isBulkActive ? this.bulkPrice : this.regularPrice;
            
            // Atualizar badge de preço
            this.$priceBadge.attr('data-bulk-active', isBulkActive ? 'true' : 'false');
            this.$badgePrice.text(`R$ ${this.formatPrice(currentPrice)}`);
            
            // Atualizar classe do card
            this.$card.toggleClass('bulk-pricing-active', isBulkActive);
            
            // Feedback visual
            if (isBulkActive && quantity === this.bulkMinQty) {
                this.showBulkActivatedFeedback();
            }
        }
        
        updateCart(quantity) {
            if (this.isUpdating) return;
            
            this.setLoadingState(true);
            
            const data = {
                action: 'pmcell_update_cart_quantity',
                product_id: this.productId,
                quantity: quantity,
                nonce: pmcell_ajax.nonce
            };
            
            $.ajax({
                url: pmcell_ajax.ajax_url,
                type: 'POST',
                data: data,
                timeout: 10000,
                success: (response) => {
                    if (response.success) {
                        this.handleCartUpdateSuccess(response.data);
                    } else {
                        this.handleCartUpdateError(response.data || 'Erro desconhecido');
                    }
                },
                error: (xhr, status, error) => {
                    this.handleCartUpdateError('Erro de conexão: ' + error);
                },
                complete: () => {
                    this.setLoadingState(false);
                }
            });
        }
        
        handleCartUpdateSuccess(data) {
            // Atualizar dados locais
            this.currentCartQty = data.quantity || 0;
            
            // Atualizar display com dados do servidor
            this.updatePriceDisplay(this.currentCartQty);
            
            // Atualizar fragmentos do WooCommerce (contador do carrinho)
            if (data.cart_count !== undefined) {
                $('.pmcell-cart-count').text(data.cart_count);
                $(document.body).trigger('wc_fragment_refresh');
            }
            
            // Feedback visual de sucesso
            this.showSuccessFeedback();
            
            // Debug logging
            if (window.pmcell_debug) {
                console.log(`Cart updated successfully for product ${this.productId}`, data);
            }
        }
        
        handleCartUpdateError(errorMessage) {
            // Reverter para quantidade anterior (do servidor)
            this.$quantityInput.val(this.currentCartQty);
            this.updatePriceDisplay(this.currentCartQty);
            
            // Mostrar mensagem de erro
            this.showErrorFeedback(errorMessage);
            
            console.error('PMCell Cart Update Error:', errorMessage);
        }
        
        setLoadingState(isLoading) {
            this.isUpdating = isLoading;
            
            if (isLoading) {
                this.$quantitySelector.addClass('updating').hide();
                this.$loadingState.show();
            } else {
                this.$quantitySelector.removeClass('updating').show();
                this.$loadingState.hide();
            }
        }
        
        showSuccessFeedback() {
            this.$quantitySelector.addClass('success');
            setTimeout(() => {
                this.$quantitySelector.removeClass('success');
            }, 1000);
        }
        
        showErrorFeedback(message) {
            this.$quantitySelector.addClass('error');
            setTimeout(() => {
                this.$quantitySelector.removeClass('error');
            }, 2000);
            
            // Opcional: mostrar tooltip ou notificação
            if (message && window.pmcell_show_notifications !== false) {
                this.showTooltip(message, 'error');
            }
        }
        
        showBulkActivatedFeedback() {
            // Adicionar classe especial para animação de bulk ativado
            this.$card.addClass('bulk-just-activated');
            setTimeout(() => {
                this.$card.removeClass('bulk-just-activated');
            }, 2000);
        }
        
        showTooltip(message, type = 'info') {
            // Criar e mostrar tooltip simples
            const $tooltip = $(`
                <div class="pmcell-tooltip pmcell-tooltip-${type}">
                    ${message}
                </div>
            `);
            
            $tooltip.css({
                position: 'absolute',
                top: '-40px',
                left: '50%',
                transform: 'translateX(-50%)',
                background: type === 'error' ? '#dc3545' : '#28a745',
                color: 'white',
                padding: '8px 12px',
                borderRadius: '6px',
                fontSize: '12px',
                whiteSpace: 'nowrap',
                zIndex: 1000,
                opacity: 0
            });
            
            this.$quantitySelector.css('position', 'relative').append($tooltip);
            
            $tooltip.animate({ opacity: 1 }, 200);
            
            setTimeout(() => {
                $tooltip.animate({ opacity: 0 }, 200, () => {
                    $tooltip.remove();
                });
            }, 3000);
        }
        
        formatPrice(price) {
            return price.toFixed(2).replace('.', ',');
        }
    }
    
    // Gerenciador global dos cards
    class PMCellProductCardsManager {
        constructor() {
            this.cards = [];
            this.init();
        }
        
        init() {
            this.initializeCards();
            this.setupGlobalEventListeners();
        }
        
        initializeCards() {
            $('.pmcell-product-card').each((index, element) => {
                const card = new PMCellProductCard(element);
                this.cards.push(card);
            });
            
            console.log(`PMCell: Initialized ${this.cards.length} product cards`);
        }
        
        setupGlobalEventListeners() {
            // Reinitializar cards após AJAX do WooCommerce
            $(document.body).on('wc_fragments_refreshed', () => {
                this.reinitializeCards();
            });
            
            // Teclado shortcuts globais
            $(document).on('keydown', (e) => {
                // ESC para limpar todas as seleções
                if (e.keyCode === 27) {
                    $('.qty-input:focus').blur();
                }
            });
        }
        
        reinitializeCards() {
            // Reinitializar apenas novos cards
            $('.pmcell-product-card').each((index, element) => {
                if (!$(element).data('pmcell-initialized')) {
                    const card = new PMCellProductCard(element);
                    this.cards.push(card);
                    $(element).data('pmcell-initialized', true);
                }
            });
        }
    }
    
    // CSS adicional via JavaScript para estados dinâmicos
    const additionalCSS = `
        .pmcell-product-card.bulk-just-activated .price-badge {
            animation: bulkActivatedPulse 1s ease-out;
        }
        
        @keyframes bulkActivatedPulse {
            0% { transform: scale(1.05); }
            50% { transform: scale(1.15); }
            100% { transform: scale(1.05); }
        }
        
        .quantity-selector.error {
            border-color: #dc3545;
            box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.2);
        }
        
        .pmcell-tooltip {
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            pointer-events: none;
        }
        
        .pmcell-tooltip::before {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 4px solid transparent;
            border-top-color: inherit;
        }
    `;
    
    // Injetar CSS adicional
    $('<style>').text(additionalCSS).appendTo('head');
    
    // Função para limpar erros PHP que possam aparecer no DOM
    function cleanPHPErrors() {
        // Remover text nodes com mensagens de erro
        $('body').contents().filter(function() {
            return this.nodeType === 3 && 
                   (this.textContent.includes('Deprecated:') || 
                    this.textContent.includes('Creation of dynamic property'));
        }).remove();
        
        // Remover elementos vazios que sobraram
        $('br:empty').remove();
        
        // Remover parágrafos ou divs que só contêm texto de erro
        $('p, div').each(function() {
            const text = $(this).text().trim();
            if (text.includes('Deprecated:') || 
                text.includes('Creation of dynamic property') ||
                text.includes('sidebar.php')) {
                $(this).remove();
            }
        });
    }
    
    // Inicializar quando DOM estiver pronto
    $(document).ready(() => {
        // Limpar erros primeiro
        cleanPHPErrors();
        
        // Inicializar cards
        window.pmcell_cards_manager = new PMCellProductCardsManager();
        
        // Limpar erros novamente após 1 segundo (caso algum apareça via AJAX)
        setTimeout(cleanPHPErrors, 1000);
        
        // Habilitar debug em desenvolvimento
        if (window.location.hostname === 'localhost' || window.location.hostname.includes('dev')) {
            window.pmcell_debug = true;
        }
    });
    
    // Expor para uso global se necessário
    window.PMCellProductCard = PMCellProductCard;
    window.PMCellProductCardsManager = PMCellProductCardsManager;

})(jQuery);