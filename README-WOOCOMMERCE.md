# PMCell WooCommerce 🛒

> E-commerce completo para PMCell São Paulo com WooCommerce, Mercado Pago e Melhor Envio

## 🚀 Recursos Implementados

### 💳 **Pagamentos**
- **Mercado Pago** - PIX, Cartão de Crédito, Boleto
- **Antifraude** integrado
- **Webhook** para notificações instantâneas
- **Ambiente de testes** configurado

### 📦 **Frete e Entrega**
- **Melhor Envio** - Múltiplas transportadoras
- **Correios** (PAC, Sedex)
- **JadLog, Loggi, Azul Cargo**
- **Frete grátis** acima de R$ 199,00
- **Retirada na loja** (gratuita)

### 🇧🇷 **Brasil Compliance**
- **CPF/CNPJ** obrigatório
- **Endereços brasileiros** completos
- **Validação de documentos**
- **Impostos** configuráveis
- **Moeda BRL** com formatação

### 🛡️ **Segurança**
- **SSL** obrigatório
- **Logs de transação**
- **Backup** automatizado
- **Validação de dados**

## 📋 **Instalação Rápida**

### 1. **Preparar Ambiente**
```bash
# Clonar o repositório
git clone https://github.com/seu-usuario/woocommerce-pmcell.git
cd woocommerce-pmcell

# Configurar variáveis de ambiente
cp .env.woocommerce .env
# Edite o arquivo .env com suas credenciais
```

### 2. **Instalar WooCommerce**
```bash
# Instalar WooCommerce base
./scripts/install-woocommerce.sh

# Configurar plugins brasileiros
./scripts/configure-brazil.sh

# Configurar Mercado Pago
./scripts/setup-mercadopago.sh

# Configurar Melhor Envio
./scripts/setup-melhor-envio.sh
```

### 3. **Verificar Instalação**
```bash
# Verificar plugins ativos
wp plugin list --status=active

# Testar configurações WooCommerce
wp wc product list
wp wc customer list
```

## 🔧 **Scripts Disponíveis**

| Script | Descrição |
|--------|-----------|
| `install-woocommerce.sh` | Instala e configura WooCommerce base |
| `configure-brazil.sh` | Configura plugins brasileiros (CPF/CNPJ) |
| `setup-mercadopago.sh` | Configura Mercado Pago (PIX, Cartão, Boleto) |
| `setup-melhor-envio.sh` | Configura cálculo de frete |
| `backup.sh` | Backup completo do banco e arquivos |
| `restore.sh` | Restaura backup do banco de dados |

## 🏪 **Configuração da Loja**

### **Informações Básicas**
- **Nome**: PMCell São Paulo
- **Endereço**: Avenida Paulista, 1578 - São Paulo, SP
- **CEP**: 01310-100
- **Telefone**: (11) 99999-9999
- **Email**: contato@pmcell.com.br

### **Categorias de Produtos**
- 📱 Smartphones
- 🔌 Acessórios
- 🛡️ Capinhas e Cases
- ⚡ Carregadores
- 🎧 Fones de Ouvido
- 📱 Películas

## 💳 **Configuração do Mercado Pago**

### 1. **Obter Credenciais**
1. Acesse: https://www.mercadopago.com.br/developers/panel/app
2. Crie uma aplicação
3. Copie suas credenciais:
   - Public Key
   - Access Token

### 2. **Configurar no WordPress**
1. WP Admin > WooCommerce > Configurações > Pagamentos
2. Configure cada método:
   - PIX - Mercado Pago
   - Cartão de Crédito - Mercado Pago
   - Boleto Bancário - Mercado Pago

### 3. **Configurar Webhook**
- URL: `https://seu-site.com/wc-api/WC_WooMercadoPago_Webhook/`
- Eventos: `payments`, `merchant_orders`

## 📦 **Configuração do Melhor Envio**

### 1. **Criar Conta**
1. Cadastre-se em: https://melhorenvio.com.br/cadastre-se
2. Acesse: https://melhorenvio.com.br/painel
3. Gere um Token de API

### 2. **Configurar Plugin**
1. WP Admin > WooCommerce > Configurações > Entrega
2. Configure o Melhor Envio com:
   - Token da API
   - User ID
   - Company ID

## 🚚 **Transportadoras Disponíveis**

| Transportadora | Tipo de Entrega | Prazo Médio |
|---------------|-----------------|-------------|
| **Correios PAC** | Econômica | 8-12 dias úteis |
| **Correios Sedex** | Expressa | 3-5 dias úteis |
| **JadLog** | Expressa | 2-4 dias úteis |
| **Loggi** | Mesma cidade | 4-24 horas |
| **Azul Cargo** | Expressa | 2-5 dias úteis |
| **Retirada na Loja** | Presencial | Imediata |

## 💰 **Política de Frete**

- **Frete Grátis**: Compras acima de R$ 199,00
- **Retirada na Loja**: Sempre gratuita
- **Prazo Adicional**: +2 dias úteis (margem de segurança)
- **Taxa de Manuseio**: R$ 0,00

## 🔒 **Segurança e Compliance**

### **Dados Obrigatórios**
- ✅ CPF para pessoa física
- ✅ CNPJ para pessoa jurídica
- ✅ Telefone celular
- ✅ Endereço completo com número
- ✅ Validação de documentos

### **Proteção Antifraude**
- ✅ Análise automática do Mercado Pago
- ✅ Validação de CPF/CNPJ
- ✅ Verificação de endereço
- ✅ Logs de transações suspeitas

## 📊 **Backup e Manutenção**

### **Backup Automático**
```bash
# Backup completo
./scripts/backup.sh

# Verificar backups
ls -la ./data/backup/
```

### **Restauração**
```bash
# Restaurar do backup
./scripts/restore.sh ./data/backup/database_pmcell-woocommerce_20240312_143022.sql.gz
```

### **Manutenção Regular**
- ✅ Backup semanal automático
- ✅ Limpeza de logs antigos
- ✅ Verificação de plugins
- ✅ Atualização de segurança

## 🌐 **URLs Importantes**

| Página | URL |
|--------|-----|
| **Loja** | http://localhost:8080/shop |
| **Carrinho** | http://localhost:8080/cart |
| **Checkout** | http://localhost:8080/checkout |
| **Minha Conta** | http://localhost:8080/my-account |
| **Admin** | http://localhost:8080/wp-admin |

## 📞 **Suporte e Documentação**

### **Documentação Oficial**
- [WooCommerce](https://woocommerce.com/documentation/)
- [Mercado Pago](https://www.mercadopago.com.br/developers/pt/docs/woocommerce)
- [Melhor Envio](https://docs.melhorenvio.com.br/)

### **Plugins Utilizados**
- WooCommerce (oficial)
- WooCommerce Extra Checkout Fields for Brazil
- WooCommerce Mercado Pago (oficial)
- Melhor Envio Cotação

## 🤝 **Contribuição**

1. Fork o projeto
2. Crie sua feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 **Licença**

Este projeto está licenciado sob a MIT License - veja o arquivo [LICENSE](LICENSE) para detalhes.

---

**PMCell São Paulo** - Seu celular, nossa especialidade! 📱✨