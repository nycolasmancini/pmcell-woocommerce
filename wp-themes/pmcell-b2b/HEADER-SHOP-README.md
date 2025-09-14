# 🏪 Header Customizado PMCell - Seção Shop

## 📋 Resumo da Implementação

Este documento descreve a implementação completa do header personalizado para a seção `/shop/` do WooCommerce, seguindo as especificações solicitadas:

- **Header branco** com identidade visual PMCell
- **Logo da empresa** em proporção 1:1 à esquerda
- **Nome "PMCELL"** em negrito com "SÃO PAULO" abaixo
- **Barra de pesquisa centralizada** com funcionalidade AJAX avançada
- **Layout totalmente responsivo**

## 🎯 Funcionalidades Implementadas

### ✅ Design e Layout
- [x] Header com fundo branco (#FFFFFF)
- [x] Layout em grid responsivo (logo + pesquisa + usuário)
- [x] Logo 1:1 com placeholder inteligente
- [x] Typography PMCell com fonte Inter
- [x] Paleta de cores oficial PMCell

### ✅ Barra de Pesquisa Avançada
- [x] Pesquisa AJAX em tempo real
- [x] Busca por nome do produto
- [x] Busca por categoria
- [x] Busca por marca/SKU
- [x] Autocomplete com dropdown
- [x] Cache de resultados (5 minutos)
- [x] Navegação por teclado (arrows, enter, escape)
- [x] Mobile-friendly

### ✅ Integração WooCommerce
- [x] Override completo do template archive-product.php
- [x] Header específico (header-shop.php)
- [x] Compatibilidade com produtos, categorias e tags
- [x] Segurança com nonce verification
- [x] Otimização de performance

### ✅ Responsividade
- [x] Desktop (1200px+)
- [x] Tablet (768px - 1199px)
- [x] Mobile (até 767px)
- [x] Acessibilidade (prefers-reduced-motion, high-contrast)

## 📁 Arquivos Criados/Modificados

### Novos Arquivos:
```
wp-themes/pmcell-b2b/
├── header-shop.php           # Header específico para shop
├── css/shop-header.css       # Estilos do header
├── js/shop-search.js         # JavaScript da pesquisa AJAX
├── woocommerce/
│   └── archive-product.php   # Template override do WooCommerce
└── test-header.php          # Arquivo de teste (pode ser removido)
```

### Arquivos Modificados:
```
wp-themes/pmcell-b2b/functions.php  # Adicionadas funções AJAX e enqueue
```

## 🚀 Como Ativar o Logo

O tema já está configurado para suportar logos customizados. Para adicionar o logo da PMCell:

1. **Acesse o WordPress Admin**
   - Vá para `Aparência → Personalizar → Identidade do Site`

2. **Faça Upload do Logo**
   - Clique em "Selecionar logo"
   - Faça upload da imagem em proporção 1:1 (recomendado: 120x120px ou maior)
   - Salve as alterações

3. **Verificação**
   - O logo aparecerá automaticamente no header da shop
   - Se não houver logo, um placeholder será exibido

## 🔧 Configurações Técnicas

### CSS Loading
```php
// Carregado automaticamente apenas nas páginas de shop
if (is_shop() || is_product_category() || is_product_tag()) {
    wp_enqueue_style('pmcell-shop-header', '...', '1.0.0');
}
```

### JavaScript Loading
```php
// JavaScript carregado condicionalmente com localização AJAX
wp_enqueue_script('pmcell-shop-search', '...', ['jquery'], '1.0.0', true);
wp_localize_script('pmcell-shop-search', 'pmcell_ajax', [...]);
```

### AJAX Endpoints
```php
// Registrados para usuários logados e não-logados
add_action('wp_ajax_pmcell_shop_search', 'pmcell_handle_shop_search');
add_action('wp_ajax_nopriv_pmcell_shop_search', 'pmcell_handle_shop_search');
```

## 🧪 Como Testar

### 1. Teste Visual Básico
```bash
# Abrir no navegador
http://seusite.com/wp-content/themes/pmcell-b2b/test-header.php
```

### 2. Teste em Produção
1. Vá para `/shop/` do seu site
2. Verifique se o header aparece corretamente
3. Teste a pesquisa digitando "iPhone" ou "Samsung"
4. Redimensione a janela para testar responsividade

### 3. Checklist de Validação

#### ✅ Layout Desktop
- [ ] Header com fundo branco
- [ ] Logo PMCell à esquerda (60x60px)
- [ ] Nome "PMCELL" em negrito, Inter font
- [ ] "SÃO PAULO" alinhado à direita, fonte menor
- [ ] Barra de pesquisa centralizada
- [ ] Botão "Minha Conta" e "Carrinho" à direita

#### ✅ Funcionalidades
- [ ] Pesquisa AJAX funcionando
- [ ] Dropdown com resultados aparecem
- [ ] Navegação por teclado (arrows, enter)
- [ ] Click em resultado redireciona corretamente
- [ ] Cache de pesquisa ativo

#### ✅ Mobile
- [ ] Layout empilhado verticalmente
- [ ] Todos os elementos visíveis
- [ ] Pesquisa funcional em telas pequenas
- [ ] Botões acessíveis

## 🎨 Customizações de Cores

As cores estão definidas no arquivo `style.css` principal:

```css
:root {
  --pmcell-primary: #FF6B5A;      /* Laranja PMCell */
  --pmcell-primary-dark: #E5533D;  /* Laranja escuro */
  --pmcell-primary-light: #FFE8E5; /* Laranja claro */
}
```

Para modificar cores, edite essas variáveis CSS.

## 🔍 Configuração da Pesquisa

### Parâmetros Configuráveis (js/shop-search.js):
```javascript
const SEARCH_CONFIG = {
    minChars: 2,        // Mínimo de caracteres para pesquisar
    delay: 300,         // Delay em ms antes de pesquisar
    maxResults: 8,      // Máximo de resultados
    cache: true,        // Habilitar cache
    cacheExpiry: 300000 // Tempo de cache (5 min)
};
```

### Tipos de Busca Suportados:
1. **Título do produto** - Busca no nome
2. **SKU** - Busca no código do produto
3. **Categoria** - Busca nas categorias
4. **Tags** - Busca nas tags do produto

## 📱 Suporte Mobile

### Breakpoints:
- **Desktop**: 1200px+
- **Tablet**: 768px - 1199px
- **Mobile**: até 767px
- **Small Mobile**: até 480px

### Funcionalidades Mobile:
- Layout vertical otimizado
- Pesquisa full-width
- Botões com touch targets adequados
- Menu mobile toggle (se necessário)

## 🔒 Segurança

### Medidas Implementadas:
- **Nonce verification** em todas as requisições AJAX
- **Sanitização** de inputs com `sanitize_text_field()`
- **Escape** de outputs com funções WordPress
- **Limitação** de resultados por query
- **Timeout** em requisições AJAX (10s)

## 📈 Performance

### Otimizações:
- **Conditional loading** - CSS/JS só carrega na shop
- **Cache de pesquisa** - Reduz requisições repetidas
- **Lazy loading** - Imagens carregam sob demanda
- **Minified queries** - Busca otimizada no banco
- **Pagination** - Limitação de resultados

## 🐛 Troubleshooting

### ❌ Header não aparece:
1. Verificar se o arquivo `header-shop.php` existe
2. Confirmar que o WooCommerce está ativo
3. Checar se há erros no console do navegador

### ❌ Pesquisa não funciona:
1. Verificar se jQuery está carregado
2. Conferir se o AJAX URL está correto no console
3. Testar com produtos existentes no banco

### ❌ Logo não aparece:
1. Ir em `Aparência → Personalizar → Identidade do Site`
2. Fazer upload de uma imagem
3. Verificar se a imagem está no formato correto

### ❌ Layout quebrado em mobile:
1. Verificar se o CSS está carregando
2. Testar com diferentes resoluções
3. Conferir se não há conflitos CSS

## 📞 Contato e Suporte

Para dúvidas sobre esta implementação:

- **Documentação WooCommerce**: [https://woocommerce.com/document/template-structure/](https://woocommerce.com/document/template-structure/)
- **WordPress Codex**: [https://codex.wordpress.org/Theme_Development](https://codex.wordpress.org/Theme_Development)

---

## 🎉 Conclusão

O header customizado foi implementado com sucesso seguindo todas as especificações técnicas e de design. A solução é:

- ✅ **Profissional** - Código limpo e bem documentado
- ✅ **Responsiva** - Funciona em todos os dispositivos
- ✅ **Performática** - Otimizada para velocidade
- ✅ **Segura** - Seguindo boas práticas WordPress
- ✅ **Manutenível** - Fácil de modificar e expandir

O sistema está pronto para produção! 🚀