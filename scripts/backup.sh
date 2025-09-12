#!/bin/bash

# Script de Backup para PMCell WooCommerce
# Backup completo do banco de dados e arquivos importantes

set -e

# Configurações
BACKUP_DIR="./data/backup"
DATE=$(date +"%Y%m%d_%H%M%S")
SITE_NAME="pmcell-woocommerce"

echo "💾 Iniciando backup do PMCell WooCommerce..."

# Criar diretório de backup se não existir
mkdir -p $BACKUP_DIR

echo "📊 Fazendo backup do banco de dados..."

# Backup do banco de dados
DB_FILE="${BACKUP_DIR}/database_${SITE_NAME}_${DATE}.sql"
wp db export $DB_FILE

# Comprimir o arquivo SQL
gzip $DB_FILE
echo "✅ Banco de dados salvo em: ${DB_FILE}.gz"

echo "📁 Fazendo backup de arquivos importantes..."

# Backup de uploads
if [ -d "wp-uploads" ]; then
    tar -czf "${BACKUP_DIR}/uploads_${SITE_NAME}_${DATE}.tar.gz" wp-uploads/
    echo "✅ Uploads salvos em: uploads_${SITE_NAME}_${DATE}.tar.gz"
fi

# Backup de plugins personalizados
if [ -d "wp-plugins" ]; then
    tar -czf "${BACKUP_DIR}/plugins_${SITE_NAME}_${DATE}.tar.gz" wp-plugins/
    echo "✅ Plugins salvos em: plugins_${SITE_NAME}_${DATE}.tar.gz"
fi

# Backup de temas personalizados
if [ -d "wp-themes" ]; then
    tar -czf "${BACKUP_DIR}/themes_${SITE_NAME}_${DATE}.tar.gz" wp-themes/
    echo "✅ Temas salvos em: themes_${SITE_NAME}_${DATE}.tar.gz"
fi

# Backup de configurações do WooCommerce
echo "⚙️ Fazendo backup das configurações..."
wp option get woocommerce_admin_notices > "${BACKUP_DIR}/woocommerce_config_${DATE}.json"
wp wc setting list --format=json > "${BACKUP_DIR}/woocommerce_settings_${DATE}.json"

echo "📦 Criando backup completo comprimido..."

# Criar arquivo de backup completo
FULL_BACKUP="${BACKUP_DIR}/full_backup_${SITE_NAME}_${DATE}.tar.gz"
tar -czf $FULL_BACKUP \
    --exclude="${BACKUP_DIR}/full_backup_*.tar.gz" \
    $BACKUP_DIR/ \
    scripts/ \
    config/ \
    .env.example \
    README.md \
    docker-compose.yml 2>/dev/null || true

echo "✅ Backup completo criado: $FULL_BACKUP"

# Limpeza de backups antigos (manter apenas os 10 mais recentes)
echo "🧹 Limpando backups antigos..."
cd $BACKUP_DIR
ls -t database_*.sql.gz 2>/dev/null | tail -n +11 | xargs rm -f || true
ls -t uploads_*.tar.gz 2>/dev/null | tail -n +11 | xargs rm -f || true
ls -t plugins_*.tar.gz 2>/dev/null | tail -n +11 | xargs rm -f || true
ls -t themes_*.tar.gz 2>/dev/null | tail -n +11 | xargs rm -f || true
ls -t full_backup_*.tar.gz 2>/dev/null | tail -n +6 | xargs rm -f || true
cd ..

echo "📊 Gerando relatório do backup..."

# Relatório do backup
REPORT="${BACKUP_DIR}/backup_report_${DATE}.txt"
cat > $REPORT << EOF
PMCell WooCommerce - Relatório de Backup
========================================
Data: $(date)
Versão WordPress: $(wp core version)
Versão WooCommerce: $(wp plugin get woocommerce --field=version 2>/dev/null || echo "N/A")

Arquivos de Backup:
------------------
$(ls -lah ${BACKUP_DIR}/*${DATE}* 2>/dev/null || echo "Nenhum arquivo encontrado")

Estatísticas do Banco:
---------------------
Total de posts: $(wp post list --post_type=any --format=count)
Total de produtos: $(wp post list --post_type=product --format=count 2>/dev/null || echo "0")
Total de pedidos: $(wp post list --post_type=shop_order --format=count 2>/dev/null || echo "0")
Total de usuários: $(wp user list --format=count)

Plugins Ativos:
--------------
$(wp plugin list --status=active --format=table)

Espaço em Disco:
---------------
$(df -h . | tail -1)
EOF

echo "✅ Relatório salvo em: $REPORT"

echo ""
echo "🎉 Backup concluído com sucesso!"
echo "📁 Localização: $BACKUP_DIR"
echo "📊 Relatório: $REPORT"
echo ""
echo "📋 Arquivos criados:"
echo "• database_${SITE_NAME}_${DATE}.sql.gz"
echo "• uploads_${SITE_NAME}_${DATE}.tar.gz"
echo "• plugins_${SITE_NAME}_${DATE}.tar.gz"
echo "• themes_${SITE_NAME}_${DATE}.tar.gz"
echo "• full_backup_${SITE_NAME}_${DATE}.tar.gz"
echo ""
echo "💡 Para restaurar um backup:"
echo "   ./scripts/restore.sh [arquivo_backup.sql.gz]"