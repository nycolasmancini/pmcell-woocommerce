#!/bin/bash

# Script Completo de Setup do PMCell WooCommerce
# Instala e configura tudo automaticamente

set -e

echo "🚀 PMCell WooCommerce - Setup Completo"
echo "======================================"
echo ""

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Função para logs coloridos
log_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

log_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

log_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

log_error() {
    echo -e "${RED}❌ $1${NC}"
}

# Verificar se estamos no diretório correto
if [ ! -f "scripts/install-woocommerce.sh" ]; then
    log_error "Execute este script do diretório raiz do projeto"
    exit 1
fi

# Verificar se WordPress está instalado
log_info "Verificando pré-requisitos..."
if ! wp core is-installed 2>/dev/null; then
    log_error "WordPress não está instalado. Execute o setup básico primeiro."
    exit 1
fi
log_success "WordPress instalado e funcionando"

# Verificar WP-CLI
if ! command -v wp &> /dev/null; then
    log_error "WP-CLI não encontrado. Instale o WP-CLI primeiro."
    exit 1
fi
log_success "WP-CLI disponível"

# Fazer backup antes de começar
log_info "Fazendo backup preventivo..."
if [ -f "scripts/backup.sh" ]; then
    ./scripts/backup.sh > /dev/null 2>&1
    log_success "Backup preventivo criado"
else
    log_warning "Script de backup não encontrado"
fi

echo ""
echo "🎯 Iniciando instalação completa do WooCommerce..."
echo ""

# Etapa 1: Instalar WooCommerce
log_info "Etapa 1/4: Instalando WooCommerce base..."
if ./scripts/install-woocommerce.sh; then
    log_success "WooCommerce instalado com sucesso"
else
    log_error "Falha na instalação do WooCommerce"
    exit 1
fi

echo ""

# Etapa 2: Configurar plugins brasileiros
log_info "Etapa 2/4: Configurando plugins brasileiros..."
if ./scripts/configure-brazil.sh; then
    log_success "Plugins brasileiros configurados"
else
    log_error "Falha na configuração dos plugins brasileiros"
    exit 1
fi

echo ""

# Etapa 3: Configurar Mercado Pago
log_info "Etapa 3/4: Configurando Mercado Pago..."
if ./scripts/setup-mercadopago.sh; then
    log_success "Mercado Pago configurado"
else
    log_error "Falha na configuração do Mercado Pago"
    exit 1
fi

echo ""

# Etapa 4: Configurar Melhor Envio
log_info "Etapa 4/4: Configurando Melhor Envio..."
if ./scripts/setup-melhor-envio.sh; then
    log_success "Melhor Envio configurado"
else
    log_error "Falha na configuração do Melhor Envio"
    exit 1
fi

echo ""

# Verificações finais
log_info "Executando verificações finais..."

# Verificar plugins essenciais
REQUIRED_PLUGINS=("woocommerce" "woocommerce-extra-checkout-fields-for-brazil" "woocommerce-mercadopago" "melhor-envio-cotacao")
FAILED_PLUGINS=()

for plugin in "${REQUIRED_PLUGINS[@]}"; do
    if wp plugin is-active "$plugin" 2>/dev/null; then
        log_success "Plugin ativo: $plugin"
    else
        log_warning "Plugin não ativo: $plugin"
        FAILED_PLUGINS+=("$plugin")
    fi
done

# Verificar páginas essenciais
PAGES=("shop" "cart" "checkout" "my-account")
for page in "${PAGES[@]}"; do
    if wp post list --name="$page" --post_type=page --format=count | grep -q "1"; then
        log_success "Página criada: $page"
    else
        log_warning "Página não encontrada: $page"
    fi
done

# Verificar configurações básicas
CURRENCY=$(wp option get woocommerce_currency)
if [ "$CURRENCY" = "BRL" ]; then
    log_success "Moeda configurada: $CURRENCY"
else
    log_warning "Moeda não configurada corretamente: $CURRENCY"
fi

COUNTRY=$(wp option get woocommerce_default_country)
if [[ "$COUNTRY" == *"BR"* ]]; then
    log_success "País configurado: $COUNTRY"
else
    log_warning "País não configurado corretamente: $COUNTRY"
fi

# Gerar relatório final
echo ""
echo "📊 Gerando relatório de instalação..."

REPORT="./data/setup-report-$(date +"%Y%m%d_%H%M%S").txt"
cat > "$REPORT" << EOF
PMCell WooCommerce - Relatório de Instalação
===========================================
Data: $(date)
WordPress: $(wp core version)
WooCommerce: $(wp plugin get woocommerce --field=version 2>/dev/null || echo "N/A")

Plugins Instalados:
------------------
$(wp plugin list --status=active --format=table)

Páginas WooCommerce:
-------------------
$(wp post list --post_type=page --meta_key="_wp_page_template" --format=table)

Configurações:
--------------
Moeda: $(wp option get woocommerce_currency)
País: $(wp option get woocommerce_default_country)
Gestão de Estoque: $(wp option get woocommerce_manage_stock)
Cálculo de Impostos: $(wp option get woocommerce_calc_taxes)

URLs Importantes:
----------------
Home: $(wp option get home)
Loja: $(wp option get home)/shop
Carrinho: $(wp option get home)/cart
Checkout: $(wp option get home)/checkout
Minha Conta: $(wp option get home)/my-account
Admin: $(wp option get home)/wp-admin

Zonas de Entrega:
----------------
$(wp wc shipping_zone list --format=table)
EOF

log_success "Relatório salvo em: $REPORT"

echo ""
echo "🎉 INSTALAÇÃO CONCLUÍDA COM SUCESSO!"
echo "=================================="
echo ""
log_success "PMCell WooCommerce está pronto para uso!"
echo ""
echo "📋 Próximos passos importantes:"
echo ""
echo "1. 🔑 Configure suas credenciais do Mercado Pago:"
echo "   • Acesse: $(wp option get home)/wp-admin"
echo "   • Vá em: WooCommerce > Configurações > Pagamentos"
echo "   • Configure PIX, Cartão e Boleto"
echo ""
echo "2. 📦 Configure o Melhor Envio:"
echo "   • Acesse: WooCommerce > Configurações > Entrega"
echo "   • Configure seu Token de API"
echo ""
echo "3. 📱 Adicione seus produtos:"
echo "   • Acesse: Produtos > Adicionar Novo"
echo "   • Use as categorias já criadas"
echo ""
echo "4. 🧪 Teste o processo de compra:"
echo "   • Acesse: $(wp option get home)/shop"
echo "   • Faça um pedido de teste"
echo ""
echo "🌐 URLs importantes:"
echo "• Loja: $(wp option get home)/shop"
echo "• Admin: $(wp option get home)/wp-admin"
echo "• Relatório: $REPORT"
echo ""

if [ ${#FAILED_PLUGINS[@]} -gt 0 ]; then
    echo "⚠️  Plugins que precisam de atenção:"
    for plugin in "${FAILED_PLUGINS[@]}"; do
        echo "   • $plugin"
    done
    echo ""
fi

log_success "Setup completo! Sua loja PMCell está pronta! 🛒✨"