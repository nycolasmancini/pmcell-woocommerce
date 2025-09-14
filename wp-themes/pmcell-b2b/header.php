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

    <header id="masthead" class="site-header">
        <div class="header-container">
            
            <div class="site-branding">
                <?php
                if (function_exists('the_custom_logo') && has_custom_logo()) {
                    the_custom_logo();
                } else {
                    ?>
                    <h1 class="site-title">
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="logo">
                            <?php bloginfo('name'); ?>
                        </a>
                    </h1>
                    <p class="site-description"><?php bloginfo('description'); ?></p>
                    <?php
                }
                ?>
            </div>

            <nav id="site-navigation" class="main-navigation">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_id' => 'primary-menu',
                    'fallback_cb' => 'pmcell_fallback_menu',
                ));
                ?>
            </nav>

            <div class="user-area">
                <?php if (is_user_logged_in()) : ?>
                    <div class="account-menu">
                        <a href="<?php echo wc_get_account_endpoint_url('dashboard'); ?>" class="btn btn-primary">
                            Minha Conta
                        </a>
                    </div>
                <?php else : ?>
                    <div class="login-register">
                        <a href="<?php echo wp_login_url(); ?>" class="btn btn-primary">
                            Entrar
                        </a>
                    </div>
                <?php endif; ?>

                <?php if (function_exists('woocommerce_mini_cart')) : ?>
                    <div class="mini-cart">
                        <a href="<?php echo wc_get_cart_url(); ?>" class="cart-link">
                            <span class="cart-icon">🛒</span>
                            <span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </header>

    <?php
    // Menu mobile toggle
    ?>
    <button class="mobile-menu-toggle" aria-controls="primary-menu" aria-expanded="false">
        <span></span>
        <span></span>
        <span></span>
    </button>

    <div id="content" class="site-content">

<?php
// Função de fallback para menu
function pmcell_fallback_menu() {
    echo '<ul id="primary-menu" class="menu">';
    echo '<li><a href="' . home_url('/') . '">Início</a></li>';
    if (function_exists('woocommerce')) {
        echo '<li><a href="' . get_permalink(wc_get_page_id('shop')) . '">Produtos</a></li>';
        echo '<li><a href="' . get_permalink(wc_get_page_id('cart')) . '">Carrinho</a></li>';
        echo '<li><a href="' . get_permalink(wc_get_page_id('myaccount')) . '">Minha Conta</a></li>';
    }
    echo '</ul>';
}
?>