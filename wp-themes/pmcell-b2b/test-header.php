<?php
/**
 * Arquivo de teste para verificar se o header customizado está funcionando
 * Este arquivo pode ser removido após os testes
 */

// Simular ambiente WordPress para teste
if (!function_exists('wp_head')) {
    function wp_head() { echo "<!-- WP Head Mock -->"; }
}
if (!function_exists('wp_body_open')) {
    function wp_body_open() { echo "<!-- WP Body Open Mock -->"; }
}
if (!function_exists('language_attributes')) {
    function language_attributes() { echo 'lang="pt-BR"'; }
}
if (!function_exists('bloginfo')) {
    function bloginfo($show) {
        switch($show) {
            case 'charset': echo 'UTF-8'; break;
            case 'name': echo 'PMCell B2B'; break;
            case 'description': echo 'Acessórios para Celular - Atacado'; break;
        }
    }
}
if (!function_exists('body_class')) {
    function body_class() { echo 'class="woocommerce-shop pmcell-shop"'; }
}
if (!function_exists('esc_html_e')) {
    function esc_html_e($text, $domain) { echo $text; }
}
if (!function_exists('is_user_logged_in')) {
    function is_user_logged_in() { return false; } // Simular usuário não logado
}
if (!function_exists('home_url')) {
    function home_url($path = '') { return 'https://pmcell.com.br' . $path; }
}
if (!function_exists('get_search_query')) {
    function get_search_query() { return ''; }
}
if (!function_exists('wp_login_url')) {
    function wp_login_url() { return home_url('/wp-login.php'); }
}
if (!function_exists('has_nav_menu')) {
    function has_nav_menu($location) { return true; }
}

// Mock de configurações customizadas
function get_theme_mod($setting, $default = '') {
    return $default;
}

function has_custom_logo() {
    return true; // Simular que tem logo
}

function the_custom_logo() {
    echo '<img src="https://via.placeholder.com/60x60/FF6B5A/FFFFFF?text=PMC" alt="PMCell Logo" class="logo-image">';
}

// Incluir o header personalizado
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Teste Header PMCell Shop</title>
    <style>
        /* Incluir variáveis CSS básicas para teste */
        :root {
            --pmcell-primary: #FF6B5A;
            --pmcell-primary-dark: #E5533D;
            --pmcell-primary-light: #FFE8E5;
            --gray-900: #1F2937;
            --gray-700: #4B5563;
            --gray-400: #9CA3AF;
            --gray-100: #F3F4F6;
            --white: #FFFFFF;
            --success: #059669;
            --warning: #D97706;
            --font-primary: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --space-md: 1rem;
            --space-lg: 1.5rem;
            --space-xl: 2rem;
            --radius-sm: 4px;
            --radius-md: 8px;
            --radius-lg: 12px;
            --radius-full: 9999px;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        }
        
        body {
            margin: 0;
            padding: 0;
            font-family: var(--font-primary);
            background-color: #f9fafb;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .test-info {
            background: #e0f2fe;
            border: 1px solid #0891b2;
            padding: 1rem;
            margin: 2rem;
            border-radius: 8px;
            color: #0c4a6e;
        }
        
        .test-content {
            padding: 2rem;
            background: white;
            margin: 2rem;
            border-radius: 12px;
            box-shadow: var(--shadow-md);
        }
    </style>
    
    <!-- Incluir CSS do header -->
    <link rel="stylesheet" href="css/shop-header.css">
    
    <!-- jQuery para JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="woocommerce-shop pmcell-shop">

<div class="test-info">
    <h2>🧪 Teste do Header Customizado PMCell</h2>
    <p><strong>Instruções:</strong></p>
    <ul>
        <li>✅ Verificar se o header aparece branco com logo e nome PMCELL</li>
        <li>✅ Testar a barra de pesquisa (digite algo e pressione Enter)</li>
        <li>✅ Verificar responsividade redimensionando a janela</li>
        <li>✅ Botões de usuário e carrinho devem estar visíveis</li>
        <li>✅ Design deve seguir a identidade visual PMCell</li>
    </ul>
</div>

<?php
// Incluir o header customizado
include 'header-shop.php';
?>

<div class="test-content">
    <h1>Conteúdo da Página Shop</h1>
    <p>Este é um teste do header customizado para a seção /shop/ da PMCell.</p>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin: 2rem 0;">
        <div style="padding: 1rem; background: #f3f4f6; border-radius: 8px;">
            <h3>Produto 1</h3>
            <p>Capa para iPhone</p>
            <span style="color: var(--pmcell-primary); font-weight: bold;">R$ 25,90</span>
        </div>
        <div style="padding: 1rem; background: #f3f4f6; border-radius: 8px;">
            <h3>Produto 2</h3>
            <p>Película de Vidro</p>
            <span style="color: var(--pmcell-primary); font-weight: bold;">R$ 15,90</span>
        </div>
        <div style="padding: 1rem; background: #f3f4f6; border-radius: 8px;">
            <h3>Produto 3</h3>
            <p>Carregador Turbo</p>
            <span style="color: var(--pmcell-primary); font-weight: bold;">R$ 45,90</span>
        </div>
    </div>
    
    <div class="test-info">
        <h3>📋 Checklist de Funcionalidades:</h3>
        <ul>
            <li>🔲 Header branco com logo à esquerda</li>
            <li>🔲 Nome "PMCELL" em negrito</li>
            <li>🔲 "SÃO PAULO" abaixo do nome, alinhado à direita</li>
            <li>🔲 Barra de pesquisa centralizada</li>
            <li>🔲 Botões de usuário e carrinho à direita</li>
            <li>🔲 Layout responsivo em mobile</li>
            <li>🔲 Cores seguem paleta PMCell (laranja: #FF6B5A)</li>
        </ul>
    </div>
</div>

<!-- Incluir JavaScript -->
<script>
// Mock de AJAX para teste
var pmcell_ajax = {
    ajax_url: '/wp-admin/admin-ajax.php',
    nonce: 'test-nonce-123'
};

// Simular alguns resultados de pesquisa
$(document).ready(function() {
    console.log('🚀 Header PMCell carregado com sucesso!');
    
    // Override do AJAX para teste
    if (typeof PMCellShopSearch !== 'undefined') {
        PMCellShopSearch.prototype.fetchSearchResults = function(query) {
            return Promise.resolve([
                {
                    id: 1,
                    title: 'Capa iPhone 13 Pro Max',
                    url: '#produto1',
                    image: 'https://via.placeholder.com/50x50',
                    price: 'R$ 25,90',
                    category: 'Capas',
                    sku: 'CAP001'
                },
                {
                    id: 2,
                    title: 'Película de Vidro Samsung',
                    url: '#produto2',
                    image: 'https://via.placeholder.com/50x50',
                    price: 'R$ 15,90',
                    category: 'Películas',
                    sku: 'PEL001'
                }
            ]);
        };
    }
});
</script>
<script src="js/shop-search.js"></script>

</body>
</html>

<?php
// Fechar qualquer output buffering
if (ob_get_level()) {
    ob_end_flush();
}
?>