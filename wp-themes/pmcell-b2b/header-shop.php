<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#main"><?php esc_html_e('Skip to content', 'pmcell-b2b'); ?></a>

    <?php if (is_user_logged_in()) : ?>
        <div class="admin-bar">
            <div class="container">
                <div class="user-info">
                    Bem-vindo, <?php echo wp_get_current_user()->display_name; ?> | 
                    <a href="<?php echo wp_logout_url(); ?>">Sair</a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Header Customizado para Shop -->
    <header id="masthead" class="site-header shop-header">
        <div class="shop-header-container">
            
            <!-- Logo e Branding PMCell -->
            <div class="shop-branding">
                <div class="logo-container">
                    <?php
                    if (function_exists('the_custom_logo') && has_custom_logo()) {
                        $custom_logo_id = get_theme_mod('custom_logo');
                        $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
                        ?>
                        <div class="company-logo">
                            <img src="<?php echo esc_url($logo[0]); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" class="logo-image">
                        </div>
                    <?php } else { ?>
                        <div class="company-logo logo-placeholder">
                            <!-- Placeholder para logo -->
                            <div class="logo-placeholder-icon">📱</div>
                        </div>
                    <?php } ?>
                </div>
                
                <div class="company-info">
                    <h1 class="company-name">PMCELL</h1>
                </div>
            </div>

            <!-- Barra de Pesquisa Central -->
            <div class="shop-search-container">
                <form role="search" method="get" class="shop-search-form" action="<?php echo esc_url(home_url('/')); ?>">
                    <div class="search-input-wrapper">
                        <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <input type="search" 
                               class="search-field" 
                               placeholder="Pesquisa" 
                               value="<?php echo get_search_query(); ?>" 
                               name="s"
                               autocomplete="off"
                               id="shop-search-input">
                        <input type="hidden" name="post_type" value="product">
                    </div>
                    
                    <!-- Dropdown de Resultados AJAX -->
                    <div class="search-dropdown" id="search-dropdown" style="display: none;">
                        <div class="search-results-container">
                            <!-- Resultados serão carregados via AJAX -->
                        </div>
                    </div>
                </form>
            </div>

            <!-- Área do Usuário -->
            <div class="shop-user-area">
                <?php if (is_user_logged_in()) : ?>
                    <div class="account-menu">
                        <a href="<?php echo wc_get_account_endpoint_url('dashboard'); ?>" class="btn btn-account">
                            <svg class="user-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0ZM12 14a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Minha Conta
                        </a>
                    </div>
                <?php else : ?>
                    <div class="login-register">
                        <a href="<?php echo wp_login_url(); ?>" class="btn btn-login">
                            <svg class="user-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0ZM12 14a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Entrar
                        </a>
                    </div>
                <?php endif; ?>

                <?php if (function_exists('woocommerce_mini_cart')) : ?>
                    <div class="mini-cart">
                        <a href="<?php echo wc_get_cart_url(); ?>" class="cart-link">
                            <svg class="cart-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM20.25 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <?php if (WC()->cart->get_cart_contents_count() > 0) : ?>
                                <span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                            <?php endif; ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

        </div>
        
        <!-- Menu de Navegação (se necessário) -->
        <?php if (has_nav_menu('primary')) : ?>
        <nav class="shop-navigation" id="shop-navigation">
            <div class="container">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_id' => 'shop-menu',
                    'menu_class' => 'shop-menu',
                    'fallback_cb' => 'pmcell_shop_fallback_menu',
                ));
                ?>
            </div>
        </nav>
        <?php endif; ?>
    </header>

    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle shop-mobile-toggle" aria-controls="shop-menu" aria-expanded="false">
        <span></span>
        <span></span>
        <span></span>
    </button>

    <div id="content" class="site-content">

<?php
// Função de fallback para menu shop
if (!function_exists('pmcell_shop_fallback_menu')) {
    function pmcell_shop_fallback_menu() {
        echo '<ul id="shop-menu" class="shop-menu">';
        echo '<li><a href="' . home_url('/') . '">Início</a></li>';
        if (function_exists('woocommerce')) {
            echo '<li class="current-menu-item"><a href="' . get_permalink(wc_get_page_id('shop')) . '">Produtos</a></li>';
            
            // Adicionar categorias principais
            $categories = get_terms(array(
                'taxonomy' => 'product_cat',
                'hide_empty' => true,
                'parent' => 0,
                'number' => 5
            ));
            
            if (!empty($categories)) {
                foreach ($categories as $category) {
                    echo '<li><a href="' . get_term_link($category) . '">' . esc_html($category->name) . '</a></li>';
                }
            }
        }
        echo '</ul>';
    }
}
?>