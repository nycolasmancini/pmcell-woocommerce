#!/bin/bash

# Script de Configuração do Melhor Envio
# Baseado em: https://docs.melhorenvio.com.br/
# Plugin: https://br.wordpress.org/plugins/melhor-envio-cotacao/

set -e

echo "📦 Configurando Melhor Envio para PMCell..."

# Verificar se WooCommerce está ativo
if ! wp plugin is-active woocommerce; then
    echo "❌ WooCommerce não está ativo. Execute install-woocommerce.sh primeiro."
    exit 1
fi

echo "📦 Instalando plugin Melhor Envio..."
wp plugin install melhor-envio-cotacao --activate

echo "⚙️ Configurando Melhor Envio..."

# Configurações básicas do Melhor Envio
wp option update melhor_envio_settings '{
    "enabled": "yes",
    "title": "Melhor Envio",
    "method_title": "Melhor Envio - Cálculo de Frete",
    "method_description": "Cálculo automático de frete com as melhores transportadoras",
    "debug": "no",
    "environment": "production",
    "token": "",
    "user_id": "",
    "company_id": ""
}'

# Configurar dimensões padrão para produtos sem medidas
wp option update melhor_envio_default_height "5"
wp option update melhor_envio_default_width "15"
wp option update melhor_envio_default_length "20"
wp option update melhor_envio_default_weight "0.3"

echo "🚚 Configurando transportadoras disponíveis..."

# Habilitar principais transportadoras
wp option update melhor_envio_correios_sedex "yes"
wp option update melhor_envio_correios_pac "yes"
wp option update melhor_envio_jadlog "yes"
wp option update melhor_envio_loggi "yes"
wp option update melhor_envio_azul_cargo "yes"

echo "📏 Configurando regras de frete..."

# Configurar frete grátis
wp option update melhor_envio_free_shipping_min "199"
wp option update melhor_envio_free_shipping_enabled "yes"

# Configurar prazo adicional
wp option update melhor_envio_additional_days "2"

# Configurar taxa adicional de manuseio
wp option update melhor_envio_handling_fee "0"

echo "🎯 Configurando opções de entrega..."

# Configurar retirada na loja
wp option update melhor_envio_local_pickup "yes"
wp option update melhor_envio_local_pickup_title "Retirar na Loja"
wp option update melhor_envio_local_pickup_cost "0"

echo "📋 Configurando endereço de origem (loja)..."

# Configurar endereço da loja para cálculo
wp option update melhor_envio_origin_postcode "01310-100"
wp option update melhor_envio_origin_address "Avenida Paulista, 1578"
wp option update melhor_envio_origin_city "São Paulo"
wp option update melhor_envio_origin_state "SP"

echo "🔧 Configurando opções avançadas..."

# Configurar timeout para API
wp option update melhor_envio_timeout "30"

# Configurar cache de cotações
wp option update melhor_envio_cache_enabled "yes"
wp option update melhor_envio_cache_time "60"

# Configurar log de erros
wp option update melhor_envio_log_enabled "yes"

echo "📊 Configurando zona de frete para Melhor Envio..."

# Obter ID da zona Brasil (criada anteriormente)
ZONE_ID=$(wp wc shipping_zone list --name="Brasil" --fields=id --format=csv | tail -1)

if [ ! -z "$ZONE_ID" ]; then
    echo "Configurando Melhor Envio na zona ID: $ZONE_ID"
    
    # Adicionar método Melhor Envio à zona Brasil
    wp wc shipping_zone_method create $ZONE_ID --method_id="melhor_envio" --enabled=true --settings='{
        "title": "Melhor Envio",
        "tax_status": "none",
        "cost": "0"
    }'
else
    echo "⚠️ Zona Brasil não encontrada. Criando nova zona..."
    ZONE_ID=$(wp wc shipping_zone create --name="Brasil" --locations='[{"code":"BR","type":"country"}]' --porcelain)
    wp wc shipping_zone_method create $ZONE_ID --method_id="melhor_envio" --enabled=true
fi

echo "🏪 Configurando retirada na loja..."

# Adicionar método de retirada na loja
wp wc shipping_zone_method create $ZONE_ID --method_id="local_pickup" --enabled=true --settings='{
    "title": "Retirar na Loja PMCell",
    "tax_status": "none",
    "cost": "0"
}'

echo "📦 Configurando informações para etiquetas..."

# Configurar informações da empresa para etiquetas
wp option update melhor_envio_company_name "PMCell São Paulo"
wp option update melhor_envio_company_document "00.000.000/0001-00"
wp option update melhor_envio_company_phone "(11) 99999-9999"
wp option update melhor_envio_company_email "contato@pmcell.com.br"

echo "✅ Melhor Envio configurado com sucesso!"
echo ""
echo "🔑 AÇÃO NECESSÁRIA - Configure sua conta Melhor Envio:"
echo ""
echo "1. Crie uma conta em: https://melhorenvio.com.br/cadastre-se"
echo "2. Acesse o painel: https://melhorenvio.com.br/painel"
echo "3. Vá em 'Configurações' > 'Tokens de API'"
echo "4. Gere um novo token para sua loja"
echo ""
echo "5. Configure no WordPress:"
echo "   WP Admin > WooCommerce > Configurações > Entrega > Melhor Envio"
echo "   • Token da API"
echo "   • User ID"
echo "   • Company ID"
echo ""
echo "📋 Recursos configurados:"
echo "• ✅ Múltiplas transportadoras (Correios, JadLog, Loggi, Azul Cargo)"
echo "• ✅ Frete grátis acima de R$ 199,00"
echo "• ✅ Retirada na loja (gratuita)"
echo "• ✅ Cache de cotações (melhor performance)"
echo "• ✅ Prazo adicional de 2 dias (margem de segurança)"
echo ""
echo "🚚 Transportadoras disponíveis:"
echo "• Correios (PAC e Sedex)"
echo "• JadLog"
echo "• Loggi (entrega rápida)"
echo "• Azul Cargo Express"
echo ""
echo "📦 Para usar etiquetas automáticas:"
echo "1. Configure os dados da empresa nas configurações"
echo "2. Tenha saldo na conta Melhor Envio"
echo "3. Use a funcionalidade de 'Gerar Etiqueta' nos pedidos"
echo ""
echo "🌐 Teste o cálculo de frete em: http://localhost:8080/shop"