/**
 * JavaScript personalizado para PMCell B2B Theme
 * Funcionalidades específicas para e-commerce B2B
 */

// Verificação robusta de jQuery e inicialização segura
(function() {
    'use strict';
    
    var maxAttempts = 50; // Máximo 5 segundos (50 x 100ms)
    var attempts = 0;
    
    // Função para inicializar quando jQuery estiver disponível
    function initWhenReady() {
        attempts++;
        
        if (typeof jQuery !== 'undefined' && typeof jQuery.fn !== 'undefined') {
            // jQuery encontrado, inicializar
            jQuery(document).ready(function($) {
                // Verificação tripla de segurança
                if (typeof PMCellB2B !== 'undefined' && typeof PMCellB2B.init === 'function') {
                    try {
                        PMCellB2B.init();
                        console.log('PMCell B2B: Inicializado com sucesso!');
                    } catch (error) {
                        console.error('PMCell B2B: Erro na inicialização:', error);
                    }
                } else {
                    console.warn('PMCell B2B: Objeto PMCellB2B não encontrado');
                }
            });
        } else if (attempts < maxAttempts) {
            // jQuery ainda não carregou, tentar novamente
            console.warn('PMCell B2B: jQuery não encontrado (tentativa ' + attempts + '/' + maxAttempts + ')');
            setTimeout(initWhenReady, 100);
        } else {
            // Máximo de tentativas atingido
            console.error('PMCell B2B: jQuery não foi encontrado após ' + maxAttempts + ' tentativas. Verifique se o jQuery está carregado corretamente.');
        }
    }
    
    // Iniciar quando o DOM estiver pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initWhenReady);
    } else {
        initWhenReady();
    }
})();

// Namespace principal
var PMCellB2B = {
    
    // Inicialização
    init: function() {
        this.setupQuantityCalculator();
        this.setupMobileMenu();
        this.setupProductFilters();
        this.setupCartUpdates();
        this.setupFormValidation();
        this.setupPriceAlerts();
        this.setupLazyLoading();
        this.setupTooltips();
        this.handleBackToTop();
    },
    
    // Calculadora de quantidade e desconto em tempo real
    setupQuantityCalculator: function() {
        $(document).on('input change', '.quantity input, .qty', function() {
            var quantity = parseInt($(this).val()) || 0;
            var productId = $(this).data('product-id') || 0;
            var basePrice = parseFloat($(this).data('base-price')) || 0;
            
            if (quantity > 0 && basePrice > 0) {
                PMCellB2B.calculateDiscount(quantity, basePrice, $(this));
            }
        });
        
        // Criar calculadora de desconto se não existir
        if ($('.discount-calculator').length === 0 && $('.single-product').length > 0) {
            PMCellB2B.createDiscountCalculator();
        }
    },
    
    // Criar calculadora de desconto
    createDiscountCalculator: function() {
        var calculator = `
            <div class="discount-calculator">
                <h4>🧮 Calculadora de Desconto</h4>
                <div class="quantity-selector">
                    <label for="calc-quantity">Quantidade:</label>
                    <input type="number" id="calc-quantity" min="1" value="1" class="calc-qty">
                    <button type="button" class="btn btn-secondary calculate-btn">Calcular</button>
                </div>
                <div class="discount-result" style="display: none;">
                    <div class="result-info"></div>
                </div>
            </div>
        `;
        
        $('.single-product-summary').append(calculator);
        
        // Event handler para calculadora
        $(document).on('click', '.calculate-btn', function() {
            var qty = parseInt($('#calc-quantity').val()) || 1;
            var basePrice = PMCellB2B.getBasePrice();
            
            if (basePrice > 0) {
                PMCellB2B.showDiscountResult(qty, basePrice);
            }
        });
    },
    
    // Calcular desconto baseado na quantidade
    calculateDiscount: function(quantity, basePrice, element) {
        var discount = 0;
        var discountText = '';
        
        if (quantity >= 100) {
            discount = 0.15;
            discountText = '15% de desconto para 100+ unidades';
        } else if (quantity >= 50) {
            discount = 0.10;
            discountText = '10% de desconto para 50+ unidades';
        } else if (quantity >= 20) {
            discount = 0.05;
            discountText = '5% de desconto para 20+ unidades';
        }
        
        var finalPrice = basePrice * (1 - discount);
        var totalPrice = finalPrice * quantity;
        var savings = (basePrice - finalPrice) * quantity;
        
        // Atualizar display de preço se existir
        var priceDisplay = element.closest('.product').find('.price-display');
        if (priceDisplay.length === 0) {
            priceDisplay = $('<div class="price-display"></div>');
            element.closest('.product').append(priceDisplay);
        }
        
        var priceInfo = `
            <div class="calculated-price">
                <div class="unit-price">Preço unitário: R$ ${finalPrice.toFixed(2)}</div>
                <div class="total-price">Total: R$ ${totalPrice.toFixed(2)}</div>
                ${savings > 0 ? `<div class="savings">Economia: R$ ${savings.toFixed(2)}</div>` : ''}
                ${discountText ? `<div class="discount-info">${discountText}</div>` : ''}
            </div>
        `;
        
        priceDisplay.html(priceInfo);
    },
    
    // Mostrar resultado da calculadora
    showDiscountResult: function(quantity, basePrice) {
        var discount = 0;
        var discountPercent = 0;
        
        if (quantity >= 100) {
            discount = 0.15;
            discountPercent = 15;
        } else if (quantity >= 50) {
            discount = 0.10;
            discountPercent = 10;
        } else if (quantity >= 20) {
            discount = 0.05;
            discountPercent = 5;
        }
        
        var finalPrice = basePrice * (1 - discount);
        var totalPrice = finalPrice * quantity;
        var savings = (basePrice - finalPrice) * quantity;
        
        var resultHtml = `
            <div class="price-breakdown">
                <div class="original-total">Preço original total: R$ ${(basePrice * quantity).toFixed(2)}</div>
                ${discount > 0 ? `<div class="discount-applied">Desconto aplicado: ${discountPercent}%</div>` : ''}
                <div class="final-total">Preço final total: R$ ${totalPrice.toFixed(2)}</div>
                ${savings > 0 ? `<div class="total-savings">Você economiza: R$ ${savings.toFixed(2)}</div>` : ''}
            </div>
        `;
        
        $('.discount-result .result-info').html(resultHtml);
        $('.discount-result').fadeIn();
    },
    
    // Obter preço base do produto
    getBasePrice: function() {
        var priceText = $('.price .amount').first().text();
        var price = parseFloat(priceText.replace(/[^\d,]/g, '').replace(',', '.')) || 0;
        return price;
    },
    
    // Menu mobile
    setupMobileMenu: function() {
        $('.mobile-menu-toggle').on('click', function() {
            $(this).toggleClass('active');
            $('.main-navigation').toggleClass('mobile-open');
            $('body').toggleClass('menu-open');
        });
        
        // Fechar menu ao clicar fora
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.main-navigation, .mobile-menu-toggle').length) {
                $('.mobile-menu-toggle').removeClass('active');
                $('.main-navigation').removeClass('mobile-open');
                $('body').removeClass('menu-open');
            }
        });
    },
    
    // Filtros de produtos
    setupProductFilters: function() {
        // Filtro por categoria
        $(document).on('change', '.category-filter', function() {
            var category = $(this).val();
            PMCellB2B.filterProducts('category', category);
        });
        
        // Filtro por faixa de preço
        $(document).on('change', '.price-filter', function() {
            var priceRange = $(this).val();
            PMCellB2B.filterProducts('price', priceRange);
        });
        
        // Filtro por marca
        $(document).on('change', '.brand-filter', function() {
            var brand = $(this).val();
            PMCellB2B.filterProducts('brand', brand);
        });
        
        // Reset filtros
        $(document).on('click', '.reset-filters', function() {
            PMCellB2B.resetFilters();
        });
    },
    
    // Aplicar filtros de produtos
    filterProducts: function(filterType, filterValue) {
        $('.products .product').each(function() {
            var product = $(this);
            var shouldShow = true;
            
            // Implementar lógica de filtro baseada nos data attributes
            if (filterType === 'category' && filterValue !== '') {
                shouldShow = product.hasClass('product-category-' + filterValue);
            } else if (filterType === 'price' && filterValue !== '') {
                var productPrice = parseFloat(product.data('price')) || 0;
                var priceRange = filterValue.split('-');
                var minPrice = parseFloat(priceRange[0]) || 0;
                var maxPrice = parseFloat(priceRange[1]) || Infinity;
                shouldShow = productPrice >= minPrice && productPrice <= maxPrice;
            } else if (filterType === 'brand' && filterValue !== '') {
                shouldShow = product.hasClass('product-brand-' + filterValue);
            }
            
            if (shouldShow) {
                product.fadeIn();
            } else {
                product.fadeOut();
            }
        });
    },
    
    // Reset filtros
    resetFilters: function() {
        $('.filter-group select').val('');
        $('.products .product').fadeIn();
    },
    
    // Atualizações do carrinho
    setupCartUpdates: function() {
        // Atualizar carrinho via AJAX
        $(document).on('click', '.update-cart', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var originalText = button.text();
            
            button.text('Atualizando...').addClass('loading');
            
            // Simular atualização (implementar AJAX real)
            setTimeout(function() {
                button.text(originalText).removeClass('loading');
                PMCellB2B.showAlert('Carrinho atualizado com sucesso!', 'success');
            }, 1500);
        });
        
        // Remover item do carrinho
        $(document).on('click', '.remove-item', function(e) {
            e.preventDefault();
            
            if (confirm('Tem certeza que deseja remover este item?')) {
                var row = $(this).closest('tr');
                row.fadeOut(function() {
                    row.remove();
                    PMCellB2B.updateCartTotals();
                });
            }
        });
    },
    
    // Atualizar totais do carrinho
    updateCartTotals: function() {
        // Implementar cálculo de totais
        var subtotal = 0;
        
        $('.cart-item').each(function() {
            var price = parseFloat($(this).find('.price').data('price')) || 0;
            var qty = parseInt($(this).find('.qty').val()) || 0;
            subtotal += price * qty;
        });
        
        $('.cart-subtotal').text('R$ ' + subtotal.toFixed(2));
        
        // Calcular desconto total
        var discount = PMCellB2B.calculateTotalDiscount(subtotal);
        var total = subtotal - discount;
        
        $('.cart-discount').text('R$ ' + discount.toFixed(2));
        $('.cart-total').text('R$ ' + total.toFixed(2));
    },
    
    // Calcular desconto total
    calculateTotalDiscount: function(subtotal) {
        var discount = 0;
        
        if (subtotal >= 10000) {
            discount = subtotal * 0.15;
        } else if (subtotal >= 5000) {
            discount = subtotal * 0.10;
        } else if (subtotal >= 2000) {
            discount = subtotal * 0.05;
        }
        
        return discount;
    },
    
    // Validação de formulários
    setupFormValidation: function() {
        // Validação de CNPJ
        $(document).on('blur', 'input[name="billing_cnpj"]', function() {
            var cnpj = $(this).val();
            if (cnpj && !PMCellB2B.validateCNPJ(cnpj)) {
                PMCellB2B.showFieldError($(this), 'CNPJ inválido');
            } else {
                PMCellB2B.clearFieldError($(this));
            }
        });
        
        // Validação de email
        $(document).on('blur', 'input[type="email"]', function() {
            var email = $(this).val();
            if (email && !PMCellB2B.validateEmail(email)) {
                PMCellB2B.showFieldError($(this), 'Email inválido');
            } else {
                PMCellB2B.clearFieldError($(this));
            }
        });
        
        // Validação de telefone
        $(document).on('blur', 'input[type="tel"]', function() {
            var phone = $(this).val();
            if (phone && !PMCellB2B.validatePhone(phone)) {
                PMCellB2B.showFieldError($(this), 'Telefone inválido');
            } else {
                PMCellB2B.clearFieldError($(this));
            }
        });
    },
    
    // Validar CNPJ
    validateCNPJ: function(cnpj) {
        cnpj = cnpj.replace(/[^\d]+/g, '');
        
        if (cnpj.length !== 14) return false;
        
        // Validação básica (implementar algoritmo completo se necessário)
        if (/^(\d)\1{13}$/.test(cnpj)) return false;
        
        return true; // Simplificado para demo
    },
    
    // Validar email
    validateEmail: function(email) {
        var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    },
    
    // Validar telefone
    validatePhone: function(phone) {
        var re = /^\(\d{2}\)\s\d{4,5}-\d{4}$/;
        return re.test(phone);
    },
    
    // Mostrar erro no campo
    showFieldError: function(field, message) {
        PMCellB2B.clearFieldError(field);
        field.addClass('error');
        field.after('<span class="field-error">' + message + '</span>');
    },
    
    // Limpar erro do campo
    clearFieldError: function(field) {
        field.removeClass('error');
        field.siblings('.field-error').remove();
    },
    
    // Alertas de preço
    setupPriceAlerts: function() {
        // Monitorar mudanças de preço
        $(document).on('click', '.price-alert-btn', function() {
            var productId = $(this).data('product-id');
            var currentPrice = $(this).data('current-price');
            
            PMCellB2B.showPriceAlertModal(productId, currentPrice);
        });
    },
    
    // Modal de alerta de preço
    showPriceAlertModal: function(productId, currentPrice) {
        var modalHtml = `
            <div id="price-alert-modal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h3>Alerta de Preço</h3>
                    <p>Preço atual: R$ ${currentPrice}</p>
                    <form class="price-alert-form">
                        <div class="form-group">
                            <label>Avisar quando o preço for igual ou menor que:</label>
                            <input type="number" step="0.01" min="0" name="target_price" required>
                        </div>
                        <div class="form-group">
                            <label>Email para notificação:</label>
                            <input type="email" name="notification_email" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Criar Alerta</button>
                    </form>
                </div>
            </div>
        `;
        
        $('body').append(modalHtml);
        $('#price-alert-modal').fadeIn();
    },
    
    // Lazy loading de imagens
    setupLazyLoading: function() {
        if ('IntersectionObserver' in window) {
            var lazyImages = document.querySelectorAll('img[data-src]');
            var imageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var img = entry.target;
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        imageObserver.unobserve(img);
                    }
                });
            });
            
            lazyImages.forEach(function(img) {
                imageObserver.observe(img);
            });
        }
    },
    
    // Tooltips
    setupTooltips: function() {
        $(document).on('mouseenter', '[data-tooltip]', function() {
            var tooltip = $('<div class="tooltip">' + $(this).data('tooltip') + '</div>');
            $('body').append(tooltip);
            
            var offset = $(this).offset();
            tooltip.css({
                top: offset.top - tooltip.outerHeight() - 10,
                left: offset.left + ($(this).outerWidth() / 2) - (tooltip.outerWidth() / 2)
            }).fadeIn();
        });
        
        $(document).on('mouseleave', '[data-tooltip]', function() {
            $('.tooltip').remove();
        });
    },
    
    // Botão voltar ao topo
    handleBackToTop: function() {
        var backToTop = $('<button id="back-to-top" title="Voltar ao topo">↑</button>');
        $('body').append(backToTop);
        
        $(window).scroll(function() {
            if ($(window).scrollTop() > 300) {
                backToTop.fadeIn();
            } else {
                backToTop.fadeOut();
            }
        });
        
        backToTop.on('click', function() {
            $('html, body').animate({scrollTop: 0}, 600);
        });
    },
    
    // Mostrar alerta
    showAlert: function(message, type) {
        var alertClass = 'b2b-alert ' + (type || 'info');
        var alertHtml = '<div class="' + alertClass + '">' + message + '</div>';
        
        $('.site-content').prepend(alertHtml);
        
        setTimeout(function() {
            $('.b2b-alert').first().fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }
};

// CSS foi movido para css/custom.css para evitar erros de jQuery