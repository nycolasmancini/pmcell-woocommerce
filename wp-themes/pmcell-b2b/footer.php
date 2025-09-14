    </div><!-- #content -->

    <footer id="colophon" class="site-footer">
        <div class="container">
            
            <div class="footer-widgets">
                <div class="footer-widget-area">
                    <?php if (is_active_sidebar('footer-1')) : ?>
                        <div class="footer-column">
                            <?php dynamic_sidebar('footer-1'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (is_active_sidebar('footer-2')) : ?>
                        <div class="footer-column">
                            <?php dynamic_sidebar('footer-2'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="footer-column">
                        <h4>Contato B2B</h4>
                        <div class="contact-info">
                            <p><strong>Telefone:</strong> (11) 99999-9999</p>
                            <p><strong>Email:</strong> vendas@pmcell.com.br</p>
                            <p><strong>Horário:</strong> Seg-Sex 8h às 18h</p>
                            <p><strong>WhatsApp:</strong> (11) 99999-9999</p>
                        </div>
                        
                        <div class="payment-methods">
                            <h5>Formas de Pagamento</h5>
                            <p>💳 Cartão de Crédito<br>
                            💰 Boleto Bancário<br>
                            🏦 Transferência Bancária<br>
                            📄 Faturamento (clientes aprovados)</p>
                        </div>
                    </div>
                    
                    <div class="footer-column">
                        <h4>Informações B2B</h4>
                        <ul class="footer-links">
                            <li><a href="/sobre-nos">Sobre Nós</a></li>
                            <li><a href="/politica-b2b">Política B2B</a></li>
                            <li><a href="/termos-condicoes">Termos e Condições</a></li>
                            <li><a href="/politica-privacidade">Política de Privacidade</a></li>
                            <li><a href="/cadastro-revendedor">Seja um Revendedor</a></li>
                            <li><a href="/central-ajuda">Central de Ajuda</a></li>
                        </ul>
                        
                        <div class="social-links">
                            <h5>Redes Sociais</h5>
                            <a href="#" target="_blank">📘 Facebook</a>
                            <a href="#" target="_blank">📷 Instagram</a>
                            <a href="#" target="_blank">💼 LinkedIn</a>
                            <a href="#" target="_blank">📱 WhatsApp</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <div class="footer-info">
                    <div class="company-info">
                        <p><strong><?php bloginfo('name'); ?></strong></p>
                        <p>CNPJ: 00.000.000/0001-00</p>
                        <p>Endereço: Rua Exemplo, 123 - São Paulo, SP</p>
                        <p>CEP: 00000-000</p>
                    </div>
                    
                    <div class="copyright">
                        <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. Todos os direitos reservados.</p>
                        <p>E-commerce B2B para acessórios de celular</p>
                    </div>
                </div>
                
                <?php if (has_nav_menu('footer')) : ?>
                    <nav class="footer-navigation">
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'footer',
                            'menu_id' => 'footer-menu',
                            'depth' => 1,
                        ));
                        ?>
                    </nav>
                <?php endif; ?>
            </div>
            
        </div>
    </footer>

</div><!-- #page -->

<?php if (!is_user_logged_in()) : ?>
    <!-- Modal de Login DESABILITADO TEMPORARIAMENTE - CAUSAVA BLOQUEIO DE SCROLL -->
    <!--
    <div id="login-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Acesso Restrito</h2>
            <p>Para visualizar preços e fazer pedidos, é necessário estar logado com uma conta B2B aprovada.</p>
            
            <div class="login-options">
                <a href="<?php echo wp_login_url(); ?>" class="btn btn-primary">
                    Fazer Login
                </a>
                <a href="<?php echo wp_registration_url(); ?>" class="btn btn-secondary">
                    Criar Conta B2B
                </a>
            </div>
            
            <div class="contact-info">
                <p><strong>Precisa de ajuda?</strong></p>
                <p>Entre em contato conosco:</p>
                <p>📞 (11) 99999-9999</p>
                <p>📧 vendas@pmcell.com.br</p>
            </div>
        </div>
    </div>
    -->
<?php endif; ?>

<!-- WhatsApp Float Button -->
<div class="whatsapp-float">
    <a href="https://wa.me/5511999999999?text=Olá! Gostaria de informações sobre produtos B2B" 
       target="_blank" 
       class="whatsapp-button"
       title="Fale conosco no WhatsApp">
        💬 WhatsApp
    </a>
</div>

<style>
/* Estilos adicionais para o footer */
.footer-widgets {
    padding: 2rem 0;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.footer-widget-area {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.footer-column h4,
.footer-column h5 {
    color: var(--white);
    margin-bottom: 1rem;
    font-size: 1.1rem;
}

.footer-links {
    list-style: none;
}

.footer-links li {
    margin-bottom: 0.5rem;
}

.footer-links a {
    color: #bdc3c7;
    text-decoration: none;
    transition: color 0.3s;
}

.footer-links a:hover {
    color: var(--white);
}

.contact-info p,
.payment-methods p {
    margin-bottom: 0.5rem;
    color: #bdc3c7;
}

.social-links a {
    display: inline-block;
    margin-right: 1rem;
    margin-bottom: 0.5rem;
    color: #bdc3c7;
    text-decoration: none;
    transition: color 0.3s;
}

.social-links a:hover {
    color: var(--white);
}

.footer-bottom {
    padding: 1.5rem 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.company-info,
.copyright {
    color: #bdc3c7;
    font-size: 0.9rem;
}

/* Modal de Login - CORRIGIDO: Só aplica fixed quando visível */
.modal {
    display: none;
    position: absolute;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    pointer-events: none;
}

.modal.active {
    display: block;
    position: fixed;
    pointer-events: auto;
}

.modal-content {
    background-color: var(--white);
    margin: 10% auto;
    padding: 2rem;
    border-radius: 8px;
    width: 90%;
    max-width: 500px;
    position: relative;
}

.close {
    position: absolute;
    right: 1rem;
    top: 1rem;
    font-size: 2rem;
    cursor: pointer;
    color: var(--dark-gray);
}

.login-options {
    margin: 2rem 0;
    display: flex;
    gap: 1rem;
}

/* WhatsApp Float */
.whatsapp-float {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    z-index: 999;
}

.whatsapp-button {
    background-color: #25d366;
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 50px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3);
    transition: all 0.3s;
}

.whatsapp-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(37, 211, 102, 0.4);
    color: white;
    text-decoration: none;
}

/* Responsivo */
@media (max-width: 768px) {
    .footer-bottom {
        flex-direction: column;
        text-align: center;
    }
    
    .login-options {
        flex-direction: column;
    }
    
    .whatsapp-float {
        bottom: 1rem;
        right: 1rem;
    }
    
    .whatsapp-button {
        padding: 0.8rem 1.2rem;
    }
}
</style>

<script>
// EMERGENCY SCROLL FIX - Force scroll to work immediately
(function() {
    'use strict';
    
    // FORCE SCROLL - IMMEDIATE EXECUTION
    function forceScrollFix() {
        // Force body and html to allow scroll
        document.documentElement.style.overflow = 'visible';
        document.documentElement.style.overflowY = 'auto';
        document.documentElement.style.height = 'auto';
        document.body.style.overflow = 'visible';
        document.body.style.overflowY = 'auto';
        document.body.style.height = 'auto';
        document.body.style.minHeight = '100vh';
        
        // Remove any position fixed from modals not active
        var modals = document.querySelectorAll('.modal:not(.active)');
        modals.forEach(function(modal) {
            modal.style.position = 'absolute';
            modal.style.height = '0';
            modal.style.visibility = 'hidden';
            modal.style.pointerEvents = 'none';
        });
        
        // Force all major containers to allow overflow
        var containers = document.querySelectorAll('#page, .site, #primary, #main, .pmcell-shop-container, .content-area, .site-main');
        containers.forEach(function(container) {
            container.style.overflow = 'visible';
            container.style.height = 'auto';
            container.style.maxHeight = 'none';
        });
    }
    
    // Execute immediately
    forceScrollFix();
    
    // Execute when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', forceScrollFix);
    }
    
    // Execute after window load (fallback)
    window.addEventListener('load', forceScrollFix);
    
    // Monitor for changes and re-apply fix
    if (window.MutationObserver) {
        var observer = new MutationObserver(function(mutations) {
            var needsFix = false;
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && 
                    (mutation.attributeName === 'style' || mutation.attributeName === 'class')) {
                    needsFix = true;
                }
            });
            if (needsFix) {
                setTimeout(forceScrollFix, 100);
            }
        });
        
        observer.observe(document.body, {
            attributes: true,
            childList: true,
            subtree: true
        });
    }
})();

// JavaScript para modal de login
document.addEventListener('DOMContentLoaded', function() {
    // Modal de login para produtos sem preço
    var modal = document.getElementById('login-modal');
    var closeModal = document.querySelector('.close');
    
    // Interceptar cliques em produtos quando não logado
    document.querySelectorAll('.add_to_cart_button, .product .price').forEach(function(element) {
        element.addEventListener('click', function(e) {
            <?php if (!is_user_logged_in()) : ?>
                e.preventDefault();
                modal.style.display = 'block';
                modal.classList.add('active');
            <?php endif; ?>
        });
    });
    
    // Fechar modal
    if (closeModal) {
        closeModal.addEventListener('click', function() {
            modal.style.display = 'none';
            modal.classList.remove('active');
        });
    }
    
    // Fechar modal clicando fora
    window.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
            modal.classList.remove('active');
        }
    });
});
</script>

<?php wp_footer(); ?>

</body>
</html>