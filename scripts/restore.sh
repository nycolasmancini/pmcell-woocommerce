#!/bin/bash

# Script de Restauração para PMCell WooCommerce
# Restaura backup do banco de dados

set -e

# Verificar se foi fornecido um arquivo de backup
if [ -z "$1" ]; then
    echo "❌ Uso: $0 <arquivo_backup.sql.gz>"
    echo ""
    echo "📁 Backups disponíveis:"
    ls -la ./data/backup/database_*.sql.gz 2>/dev/null || echo "Nenhum backup encontrado"
    exit 1
fi

BACKUP_FILE=$1

# Verificar se o arquivo existe
if [ ! -f "$BACKUP_FILE" ]; then
    echo "❌ Arquivo de backup não encontrado: $BACKUP_FILE"
    exit 1
fi

echo "🔄 Iniciando restauração do backup..."
echo "📂 Arquivo: $BACKUP_FILE"

# Confirmação de segurança
read -p "⚠️ Isso irá SOBRESCREVER o banco atual. Continuar? (y/N): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "❌ Restauração cancelada pelo usuário"
    exit 1
fi

# Fazer backup do estado atual antes da restauração
echo "💾 Fazendo backup preventivo do estado atual..."
PREVENTIVE_BACKUP="./data/backup/preventive_$(date +"%Y%m%d_%H%M%S").sql.gz"
wp db export - | gzip > $PREVENTIVE_BACKUP
echo "✅ Backup preventivo salvo em: $PREVENTIVE_BACKUP"

echo "🗄️ Restaurando banco de dados..."

# Descomprimir e restaurar
if [[ $BACKUP_FILE == *.gz ]]; then
    gunzip -c $BACKUP_FILE | wp db import -
else
    wp db import $BACKUP_FILE
fi

echo "🔧 Atualizando URLs e configurações..."

# Atualizar URLs (caso necessário)
CURRENT_URL=$(wp option get home)
wp search-replace "http://localhost:8080" "$CURRENT_URL" --dry-run

# Flush rewrite rules
wp rewrite flush

# Atualizar permalinks
wp rewrite structure '/%postname%/'

echo "🔍 Verificando integridade dos dados..."

# Verificar se o WordPress está funcionando
if wp core is-installed; then
    echo "✅ WordPress: OK"
else
    echo "❌ WordPress: Problema detectado"
fi

# Verificar plugins essenciais
echo "🔌 Verificando plugins essenciais..."
if wp plugin is-active woocommerce; then
    echo "✅ WooCommerce: Ativo"
    
    # Verificar produtos
    PRODUCT_COUNT=$(wp post list --post_type=product --format=count)
    echo "📦 Produtos encontrados: $PRODUCT_COUNT"
    
    # Verificar pedidos
    ORDER_COUNT=$(wp post list --post_type=shop_order --format=count)
    echo "🛒 Pedidos encontrados: $ORDER_COUNT"
else
    echo "⚠️ WooCommerce: Não ativo"
fi

if wp plugin is-active woocommerce-mercadopago; then
    echo "✅ Mercado Pago: Ativo"
else
    echo "⚠️ Mercado Pago: Não ativo"
fi

echo "🧹 Limpando cache..."

# Limpar cache do WordPress
wp cache flush

# Limpar cache do objeto
wp cache flush

# Regenerar thumbnails se houver muitas imagens
ATTACHMENT_COUNT=$(wp post list --post_type=attachment --format=count)
if [ $ATTACHMENT_COUNT -gt 50 ]; then
    echo "🖼️ Regenerando miniaturas de imagens ($ATTACHMENT_COUNT encontradas)..."
    wp media regenerate --yes
fi

echo "📊 Gerando relatório da restauração..."

# Relatório da restauração
REPORT="./data/backup/restore_report_$(date +"%Y%m%d_%H%M%S").txt"
cat > $REPORT << EOF
PMCell WooCommerce - Relatório de Restauração
=============================================
Data: $(date)
Arquivo Restaurado: $BACKUP_FILE
Backup Preventivo: $PREVENTIVE_BACKUP

Estado Pós-Restauração:
----------------------
WordPress: $(wp core version)
WooCommerce: $(wp plugin get woocommerce --field=version 2>/dev/null || echo "N/A")
URL do Site: $(wp option get home)
Total de Posts: $(wp post list --post_type=any --format=count)
Total de Produtos: $(wp post list --post_type=product --format=count)
Total de Pedidos: $(wp post list --post_type=shop_order --format=count)
Total de Usuários: $(wp user list --format=count)

Plugins Ativos:
--------------
$(wp plugin list --status=active --format=table)
EOF

echo "✅ Relatório salvo em: $REPORT"

echo ""
echo "🎉 Restauração concluída com sucesso!"
echo "📂 Backup preventivo: $PREVENTIVE_BACKUP"
echo "📊 Relatório: $REPORT"
echo ""
echo "🌐 Acesse: http://localhost:8080 para verificar o site"
echo "🔧 Admin: http://localhost:8080/wp-admin"
echo ""
echo "⚠️ Lembre-se de:"
echo "• Verificar as configurações do Mercado Pago"
echo "• Testar o processo de checkout"
echo "• Verificar as configurações de frete"