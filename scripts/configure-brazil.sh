#!/bin/bash

# Configuração de Plugins Brasileiros para WooCommerce
# Baseado em: https://br.wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/

set -e

echo "🇧🇷 Configurando plugins brasileiros para WooCommerce..."

# Verificar se WooCommerce está ativo
if ! wp plugin is-active woocommerce; then
    echo "❌ WooCommerce não está ativo. Execute install-woocommerce.sh primeiro."
    exit 1
fi

# Instalar plugin para campos brasileiros (CPF, CNPJ, etc)
echo "📋 Instalando campos de checkout brasileiros..."
wp plugin install woocommerce-extra-checkout-fields-for-brazil --activate

# Configurar campos obrigatórios brasileiros
echo "⚙️ Configurando campos brasileiros..."

# CPF obrigatório para pessoa física
wp option update wcbcf_person_type 1
wp option update wcbcf_cpf_required 1
wp option update wcbcf_rg_required 0
wp option update wcbcf_cnpj_required 1
wp option update wcbcf_ie_required 0

# Configurar campos de endereço brasileiro
wp option update wcbcf_neighborhood_required 1
wp option update wcbcf_number_required 1
wp option update wcbcf_postcode_format 1

# Configurar validação de CPF/CNPJ
wp option update wcbcf_validate_cpf 1
wp option update wcbcf_validate_cnpj 1

# Configurar campos de telefone
wp option update wcbcf_cell_phone_required 1

echo "📦 Instalando plugin de cálculo de frete dos Correios..."
wp plugin install woocommerce-correios --activate

echo "💳 Instalando plugin oficial do Mercado Pago..."
# Download da versão mais recente do GitHub (oficial)
cd wp-content/plugins/
wget -O woocommerce-mercadopago.zip https://github.com/mercadopago/cart-woocommerce/releases/latest/download/woocommerce-mercadopago.zip
unzip -q woocommerce-mercadopago.zip
rm woocommerce-mercadopago.zip
cd ../../

# Ativar o plugin Mercado Pago
wp plugin activate woocommerce-mercadopago

echo "📊 Configurando zona de entrega para Brasil..."

# Criar zona de entrega para Brasil
wp wc shipping_zone create --name="Brasil" --locations='[{"code":"BR","type":"country"}]'

# Obter ID da zona criada
ZONE_ID=$(wp wc shipping_zone list --fields=id --format=csv | tail -1)

echo "Zona de entrega criada com ID: $ZONE_ID"

echo "🎯 Configurando categorias padrão..."

# Criar categorias de produtos para PMCell
wp wc product_cat create --name="Smartphones" --slug="smartphones" --description="Celulares e smartphones"
wp wc product_cat create --name="Acessórios" --slug="acessorios" --description="Acessórios para celular"
wp wc product_cat create --name="Capinhas e Cases" --slug="capinhas" --description="Proteção para seu celular"
wp wc product_cat create --name="Carregadores" --slug="carregadores" --description="Carregadores e cabos"
wp wc product_cat create --name="Fones de Ouvido" --slug="fones" --description="Fones e acessórios de áudio"
wp wc product_cat create --name="Películas" --slug="peliculas" --description="Películas protetoras"

echo "🏪 Configurando informações da loja..."

# Configurar endereço da loja (São Paulo)
wp option update woocommerce_store_address "Rua das Exemplo, 123"
wp option update woocommerce_store_address_2 "Sala 45"
wp option update woocommerce_store_city "São Paulo"
wp option update woocommerce_store_postcode "01234-567"
wp option update woocommerce_default_country "BR:SP"

echo "📧 Configurando notificações..."

# Configurar emails para português
wp option update woocommerce_email_from_name "PMCell São Paulo"

# Configurar mensagens personalizadas
wp option update woocommerce_checkout_privacy_policy_text "Seus dados pessoais serão utilizados para processar seu pedido e dar suporte à sua experiência em nosso site."

echo "✅ Configuração brasileira concluída com sucesso!"
echo ""
echo "🎯 Plugins instalados e configurados:"
echo "• ✅ Campos de checkout brasileiro (CPF/CNPJ)"
echo "• ✅ Correios Brasil (cálculo de frete)"
echo "• ✅ Mercado Pago (pagamentos)"
echo "• ✅ Categorias PMCell criadas"
echo "• ✅ Zona de entrega Brasil configurada"
echo ""
echo "📋 Próximos passos:"
echo "1. Configure suas credenciais do Mercado Pago em: WP Admin > WooCommerce > Configurações > Pagamentos > Mercado Pago"
echo "2. Configure os Correios em: WP Admin > WooCommerce > Configurações > Entrega"
echo "3. Execute: ./scripts/setup-mercadopago.sh (após obter as credenciais)"
echo ""
echo "🌐 Acesse: http://localhost:8080/wp-admin para continuar"