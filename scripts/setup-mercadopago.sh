#!/bin/bash

# Script de Configuração do Mercado Pago
# Baseado em: https://github.com/mercadopago/cart-woocommerce
# Documentação: https://www.mercadopago.com.br/developers/pt/docs/woocommerce/introduction

set -e

echo "💳 Configurando Mercado Pago para PMCell..."

# Verificar se o plugin está ativo
if ! wp plugin is-active woocommerce-mercadopago; then
    echo "❌ Plugin Mercado Pago não está ativo. Execute configure-brazil.sh primeiro."
    exit 1
fi

# Verificar se as variáveis de ambiente existem
if [[ -f .env ]]; then
    source .env
    echo "✅ Arquivo .env carregado"
else
    echo "⚠️ Arquivo .env não encontrado. Usando configuração manual."
fi

echo "⚙️ Configurando métodos de pagamento do Mercado Pago..."

# Configurações gerais do Mercado Pago
wp option update woocommerce_mercadopago_settings '{
    "enabled": "yes",
    "title": "Mercado Pago",
    "description": "Pague com PIX, cartão de crédito ou boleto através do Mercado Pago",
    "mp_mode": "production",
    "mp_category": "others",
    "mp_store_identificator": "PMCell São Paulo",
    "mp_integrator_id": "",
    "mp_debug_mode": "no",
    "mp_custom_domain": ""
}'

# Configurar PIX (método mais seguro e rápido)
echo "🔄 Configurando PIX..."
wp option update woocommerce_woo-mercado-pago-pix_settings '{
    "enabled": "yes",
    "title": "PIX - Mercado Pago",
    "description": "Pagamento instantâneo via PIX - Aprovação em até 5 minutos",
    "method_title": "PIX",
    "method_description": "Pagamento instantâneo via PIX",
    "mp_pix_expiration": "30",
    "mp_pix_discount": "0",
    "checkout_credential_production": "",
    "checkout_credential_test": ""
}'

# Configurar Cartão de Crédito
echo "💳 Configurando Cartão de Crédito..."
wp option update woocommerce_woo-mercado-pago-custom_settings '{
    "enabled": "yes",
    "title": "Cartão de Crédito - Mercado Pago",
    "description": "Pague com cartão de crédito de forma segura",
    "method_title": "Cartão de Crédito",
    "method_description": "Cartão de crédito via Mercado Pago",
    "mp_discount_credit": "0",
    "mp_commission_credit": "0",
    "mp_installments": "12",
    "mp_min_installment": "5",
    "checkout_credential_production": "",
    "checkout_credential_test": ""
}'

# Configurar Boleto Bancário
echo "🏦 Configurando Boleto Bancário..."
wp option update woocommerce_woo-mercado-pago-ticket_settings '{
    "enabled": "yes",
    "title": "Boleto Bancário - Mercado Pago",
    "description": "Boleto bancário com vencimento configurável",
    "method_title": "Boleto Bancário",
    "method_description": "Pagamento via boleto bancário",
    "mp_discount_ticket": "0",
    "mp_commission_ticket": "0",
    "mp_due_date": "3",
    "checkout_credential_production": "",
    "checkout_credential_test": ""
}'

# Configurar webhook do Mercado Pago para notificações instantâneas
WEBHOOK_URL="$(wp option get home)/wc-api/WC_WooMercadoPago_Webhook/"
echo "🔔 Configurando webhook: $WEBHOOK_URL"

wp option update woocommerce_mercadopago_webhook_url "$WEBHOOK_URL"

echo "🛡️ Configurando opções de segurança..."

# Configurar logs para debug (desabilitado em produção)
wp option update woocommerce_mercadopago_debug_mode "no"

# Configurar modo de produção
wp option update woocommerce_mercadopago_sandbox_mode "no"

echo "📊 Configurando análise de risco..."

# Habilitar análise automática de risco (antifraude)
wp option update woocommerce_mercadopago_auto_return "yes"
wp option update woocommerce_mercadopago_success_url "$(wp option get home)/checkout/order-received/"
wp option update woocommerce_mercadopago_failure_url "$(wp option get home)/checkout/"
wp option update woocommerce_mercadopago_pending_url "$(wp option get home)/checkout/order-received/"

echo "🎨 Configurando aparência do checkout..."

# Configurar cores e tema
wp option update woocommerce_mercadopago_checkout_theme "blue"
wp option update woocommerce_mercadopago_checkout_header_color "#009EE3"

echo "✅ Mercado Pago configurado com sucesso!"
echo ""
echo "🔑 AÇÃO NECESSÁRIA - Configure suas credenciais:"
echo ""
echo "1. Acesse: https://www.mercadopago.com.br/developers/panel/app"
echo "2. Crie uma aplicação ou use uma existente"
echo "3. Copie suas credenciais:"
echo "   • Public Key (Chave Pública)"
echo "   • Access Token (Token de Acesso)"
echo ""
echo "4. Configure em: WP Admin > WooCommerce > Configurações > Pagamentos"
echo "   • PIX - Mercado Pago"
echo "   • Cartão de Crédito - Mercado Pago"
echo "   • Boleto Bancário - Mercado Pago"
echo ""
echo "5. Configure o Webhook em: https://www.mercadopago.com.br/developers/panel/app/webhooks"
echo "   URL: $WEBHOOK_URL"
echo "   Eventos: payments, merchant_orders"
echo ""
echo "📋 Para testar pagamentos:"
echo "• Use o modo sandbox primeiro"
echo "• Cartões de teste: https://www.mercadopago.com.br/developers/pt/docs/testing/test-cards"
echo "• PIX de teste disponível no ambiente sandbox"
echo ""
echo "🌐 Acesse: http://localhost:8080/shop para testar sua loja"