/**
 * JavaScript para Pesquisa Avançada da Shop PMCell
 * Funcionalidades: Autocomplete, AJAX search, filtros avançados
 */

(function($) {
    'use strict';

    // Configurações da pesquisa
    const SEARCH_CONFIG = {
        minChars: 2,
        delay: 300,
        maxResults: 8,
        cache: true,
        cacheExpiry: 300000 // 5 minutos
    };

    // Cache para resultados
    const searchCache = new Map();

    // Classe principal da pesquisa
    class PMCellShopSearch {
        constructor() {
            this.init();
            this.bindEvents();
            this.setupCache();
        }

        init() {
            this.searchInput = $('#shop-search-input');
            this.searchForm = $('.shop-search-form');
            this.dropdown = $('#search-dropdown');
            this.resultsContainer = $('.search-results-container');
            this.currentRequest = null;
            this.searchTimeout = null;
            this.isSearching = false;
            this.selectedIndex = -1;
        }

        bindEvents() {
            // Input events
            this.searchInput.on('input', (e) => {
                this.handleInput(e);
            });

            this.searchInput.on('focus', (e) => {
                this.handleFocus(e);
            });

            this.searchInput.on('blur', (e) => {
                // Delay para permitir clique em resultado
                setTimeout(() => this.handleBlur(e), 200);
            });

            // Keyboard navigation
            this.searchInput.on('keydown', (e) => {
                this.handleKeyDown(e);
            });

            // Form submission
            this.searchForm.on('submit', (e) => {
                this.handleSubmit(e);
            });

            // Click outside
            $(document).on('click', (e) => {
                if (!this.searchForm.has(e.target).length) {
                    this.hideDropdown();
                }
            });

            // Resultado click
            $(document).on('click', '.search-result-item', (e) => {
                this.handleResultClick(e);
            });

            // Mobile search toggle
            $('.shop-mobile-toggle').on('click', () => {
                this.toggleMobileSearch();
            });
        }

        setupCache() {
            // Limpar cache expirado periodicamente
            setInterval(() => {
                this.clearExpiredCache();
            }, 60000); // A cada minuto
        }

        handleInput(e) {
            const query = e.target.value.trim();
            
            if (query.length < SEARCH_CONFIG.minChars) {
                this.hideDropdown();
                return;
            }

            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.performSearch(query);
            }, SEARCH_CONFIG.delay);
        }

        handleFocus(e) {
            const query = e.target.value.trim();
            if (query.length >= SEARCH_CONFIG.minChars) {
                this.showDropdown();
            }
        }

        handleBlur(e) {
            this.hideDropdown();
        }

        handleKeyDown(e) {
            const items = $('.search-result-item');
            
            switch(e.keyCode) {
                case 38: // Up arrow
                    e.preventDefault();
                    this.selectedIndex = Math.max(-1, this.selectedIndex - 1);
                    this.updateSelection(items);
                    break;
                    
                case 40: // Down arrow
                    e.preventDefault();
                    this.selectedIndex = Math.min(items.length - 1, this.selectedIndex + 1);
                    this.updateSelection(items);
                    break;
                    
                case 13: // Enter
                    if (this.selectedIndex >= 0 && items.length > 0) {
                        e.preventDefault();
                        items.eq(this.selectedIndex).click();
                    }
                    break;
                    
                case 27: // Escape
                    this.hideDropdown();
                    this.searchInput.blur();
                    break;
            }
        }

        handleSubmit(e) {
            const query = this.searchInput.val().trim();
            if (!query) {
                e.preventDefault();
                return;
            }
            
            // Permitir submissão normal do form
            this.hideDropdown();
        }

        handleResultClick(e) {
            e.preventDefault();
            const $item = $(e.currentTarget);
            const url = $item.data('url');
            
            if (url) {
                // Track analytics se necessário
                this.trackSearchClick(url, $item.data('title'));
                
                // Navegar para o produto
                window.location.href = url;
            }
        }

        updateSelection(items) {
            items.removeClass('selected');
            if (this.selectedIndex >= 0) {
                items.eq(this.selectedIndex).addClass('selected');
            }
        }

        async performSearch(query) {
            if (this.isSearching) {
                return;
            }

            // Verificar cache primeiro
            if (SEARCH_CONFIG.cache && searchCache.has(query)) {
                const cached = searchCache.get(query);
                if (Date.now() - cached.timestamp < SEARCH_CONFIG.cacheExpiry) {
                    this.displayResults(cached.results, query);
                    return;
                }
            }

            this.isSearching = true;
            this.showLoading();

            try {
                const results = await this.fetchSearchResults(query);
                
                // Cache results
                if (SEARCH_CONFIG.cache) {
                    searchCache.set(query, {
                        results: results,
                        timestamp: Date.now()
                    });
                }

                this.displayResults(results, query);
                
            } catch (error) {
                console.error('Erro na pesquisa:', error);
                this.showError();
            } finally {
                this.isSearching = false;
            }
        }

        fetchSearchResults(query) {
            return new Promise((resolve, reject) => {
                // Cancelar request anterior se existir
                if (this.currentRequest) {
                    this.currentRequest.abort();
                }

                this.currentRequest = $.ajax({
                    url: pmcell_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'pmcell_shop_search',
                        query: query,
                        nonce: pmcell_ajax.nonce,
                        max_results: SEARCH_CONFIG.maxResults
                    },
                    timeout: 10000
                });

                this.currentRequest
                    .done((response) => {
                        if (response.success) {
                            resolve(response.data);
                        } else {
                            reject(new Error(response.data || 'Erro na pesquisa'));
                        }
                    })
                    .fail((xhr, status, error) => {
                        if (status !== 'abort') {
                            reject(new Error(error));
                        }
                    });
            });
        }

        displayResults(results, query) {
            this.selectedIndex = -1;
            
            if (!results || results.length === 0) {
                this.showNoResults(query);
                return;
            }

            let html = '';
            results.forEach((item, index) => {
                html += this.renderResultItem(item, index);
            });

            this.resultsContainer.html(html);
            this.showDropdown();
        }

        renderResultItem(item, index) {
            const imageUrl = item.image || this.getPlaceholderImage();
            const price = item.price ? `<div class="search-result-price">${item.price}</div>` : '';
            const category = item.category ? `<p class="search-result-category">${item.category}</p>` : '';
            
            return `
                <div class="search-result-item" data-url="${item.url}" data-title="${item.title}" data-index="${index}">
                    <img src="${imageUrl}" alt="${item.title}" class="search-result-image" loading="lazy">
                    <div class="search-result-info">
                        <h4 class="search-result-title">${this.highlightQuery(item.title)}</h4>
                        ${category}
                    </div>
                    ${price}
                </div>
            `;
        }

        highlightQuery(text) {
            const query = this.searchInput.val().trim();
            if (!query) return text;
            
            const regex = new RegExp(`(${this.escapeRegex(query)})`, 'gi');
            return text.replace(regex, '<strong>$1</strong>');
        }

        escapeRegex(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        getPlaceholderImage() {
            return 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAiIGhlaWdodD0iNTAiIHZpZXdCb3g9IjAgMCA1MCA1MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjUwIiBoZWlnaHQ9IjUwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0yNSAzMEM4Ljc1IDMwIDI1IDMwIDI1IDMwWiIgZmlsbD0iIzlDQTNBRiIvPgo8L3N2Zz4K';
        }

        showLoading() {
            this.resultsContainer.html(`
                <div class="search-loading">
                    <span>🔍 Pesquisando...</span>
                </div>
            `);
            this.showDropdown();
        }

        showError() {
            this.resultsContainer.html(`
                <div class="search-no-results">
                    <span>❌ Erro na pesquisa. Tente novamente.</span>
                </div>
            `);
            this.showDropdown();
        }

        showNoResults(query) {
            this.resultsContainer.html(`
                <div class="search-no-results">
                    <span>Nenhum resultado encontrado para "${query}"</span>
                </div>
            `);
            this.showDropdown();
        }

        showDropdown() {
            this.dropdown.show();
        }

        hideDropdown() {
            this.dropdown.hide();
            this.selectedIndex = -1;
        }

        toggleMobileSearch() {
            // Implementar toggle de busca mobile se necessário
            const searchContainer = $('.shop-search-container');
            searchContainer.toggleClass('mobile-active');
        }

        clearExpiredCache() {
            const now = Date.now();
            for (let [key, value] of searchCache.entries()) {
                if (now - value.timestamp > SEARCH_CONFIG.cacheExpiry) {
                    searchCache.delete(key);
                }
            }
        }

        trackSearchClick(url, title) {
            // Implementar tracking analytics se necessário
            if (typeof gtag !== 'undefined') {
                gtag('event', 'search_result_click', {
                    'search_term': this.searchInput.val(),
                    'result_title': title,
                    'result_url': url
                });
            }
        }
    }

    // Adicionar estilos CSS específicos para estados
    const dynamicStyles = `
        <style>
            .search-result-item.selected {
                background-color: var(--pmcell-primary-light) !important;
            }
            
            .search-result-item strong {
                color: var(--pmcell-primary);
                font-weight: 600;
            }
            
            .shop-search-container.mobile-active {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 9999;
                background: white;
                padding: 1rem;
                box-shadow: var(--shadow-lg);
            }
            
            @media (max-width: 768px) {
                .search-dropdown {
                    position: fixed;
                    top: 60px;
                    left: 1rem;
                    right: 1rem;
                    width: auto;
                }
            }
        </style>
    `;

    // Inicializar quando o DOM estiver pronto
    $(document).ready(function() {
        // Adicionar estilos dinâmicos
        $('head').append(dynamicStyles);
        
        // Inicializar pesquisa apenas se estiver na página da shop
        if ($('.shop-header').length > 0) {
            new PMCellShopSearch();
        }
    });

    // Fazer a classe disponível globalmente se necessário
    window.PMCellShopSearch = PMCellShopSearch;

})(jQuery);