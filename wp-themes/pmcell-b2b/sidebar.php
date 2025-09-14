<?php
/**
 * PMCell B2B Sidebar Template
 * Arquivo sidebar básico para evitar warnings do WordPress
 * 
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div id="secondary" class="widget-area sidebar" role="complementary">
    
    <?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
        
        <div class="sidebar-content">
            <?php dynamic_sidebar( 'sidebar-1' ); ?>
        </div>
        
    <?php else : ?>
        
        <!-- Sidebar vazia - mantém compatibilidade com WordPress -->
        <div class="sidebar-placeholder">
            <?php
            // Para temas que não utilizam sidebar, mantém estrutura vazia
            // mas evita o warning do WordPress sobre arquivo ausente
            ?>
        </div>
        
    <?php endif; ?>
    
</div><!-- #secondary -->