#!/bin/bash

# WooCommerce Installation Script for PMCell
# Baseado na documentação oficial: https://woocommerce.com/documentation/plugins/woocommerce/

set -e

echo "🚀 Instalando WooCommerce para PMCell..."

# Verificar se WP-CLI está disponível
if ! command -v wp &> /dev/null; then
    echo "❌ WP-CLI não encontrado. Instalando..."
    curl -O https://raw.githubusercontent.com/wp-cli/wp-cli/v2.8.1/phar/wp-cli.phar
    chmod +x wp-cli.phar
    sudo mv wp-cli.phar /usr/local/bin/wp
    echo "✅ WP-CLI instalado com sucesso!"
fi

# Verificar se WordPress está instalado
if ! wp core is-installed; then
    echo "❌ WordPress não está instalado. Execute o setup básico primeiro."
    exit 1
fi

echo "📦 Instalando WooCommerce..."

# Instalar WooCommerce (plugin oficial)
wp plugin install woocommerce --activate

# Instalar páginas essenciais do WooCommerce
echo "📄 Criando páginas essenciais..."
wp wc tool run install_pages --user=1

# Configurar moeda para Real Brasileiro
echo "💰 Configurando moeda para Brasil..."
wp option update woocommerce_currency BRL
wp option update woocommerce_currency_pos left_space
wp option update woocommerce_price_thousand_sep .
wp option update woocommerce_price_decimal_sep ,
wp option update woocommerce_price_num_decimals 2

# Configurar país e região padrão
echo "🇧🇷 Configurando Brasil como país padrão..."
wp option update woocommerce_default_country 'BR:SP'
wp option update woocommerce_allowed_countries 'specific'
wp option update woocommerce_specific_allowed_countries '["BR"]'

# Configurar unidades brasileiras
echo "📏 Configurando unidades de medida..."
wp option update woocommerce_weight_unit kg
wp option update woocommerce_dimension_unit cm

# Configurar opções de entrega
echo "📦 Configurando opções de entrega..."
wp option update woocommerce_ship_to_countries 'specific'
wp option update woocommerce_specific_ship_to_countries '["BR"]'

# Habilitar gestão de estoque
echo "📊 Habilitando gestão de estoque..."
wp option update woocommerce_manage_stock yes
wp option update woocommerce_notify_low_stock_amount 5
wp option update woocommerce_notify_no_stock_amount 0

# Configurar impostos (preparar para futura implementação)
echo "🧾 Preparando configurações de impostos..."
wp option update woocommerce_calc_taxes yes
wp option update woocommerce_prices_include_tax yes
wp option update woocommerce_tax_based_on billing
wp option update woocommerce_tax_display_cart incl
wp option update woocommerce_tax_display_shop incl

# Configurar checkout
echo "💳 Configurando checkout..."
wp option update woocommerce_terms_page_id 0
wp option update woocommerce_checkout_privacy_policy_text "Seus dados pessoais serão utilizados para processar seu pedido, dar suporte à sua experiência em nosso site e para outros propósitos descritos na nossa %s."

# Configurar email settings
echo "📧 Configurando emails..."
wp option update woocommerce_email_from_name "PMCell São Paulo"

# Definir permalink structure adequado para WooCommerce
echo "🔗 Configurando permalinks..."
wp rewrite structure '/%postname%/'
wp rewrite flush

# Atualizar capabilities
wp user add-cap admin manage_woocommerce

echo "✅ WooCommerce instalado e configurado com sucesso!"
echo ""
echo "📋 Próximos passos:"
echo "1. Execute: ./scripts/configure-brazil.sh"
echo "2. Configure o Mercado Pago em: WP Admin > WooCommerce > Configurações > Pagamentos"
echo "3. Configure o frete em: WP Admin > WooCommerce > Configurações > Entrega"
echo ""
echo "🌐 Acesse: http://localhost:8080/wp-admin para continuar a configuração"