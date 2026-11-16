# Spec: UI/UX Specs / Interface e Experiência do Usuário — Alê Bordados Kids

## 1. Diretrizes Gerais e Estilo Visual

- **Estilo:** Baseado nos wireframes iniciais e no guia de identidade visual, o layout adota um padrão claro, minimalista e limpo (fundo principal em branco e cinza claro `--cor-fundo-secundario`), priorizando a usabilidade e a hierarquia visual.
- **Vitrine:** O hero usa `public/images/hero_banner.png` como fundo e exibe somente os textos da chamada e o botão “Ver coleção”; não adiciona formas, laços ou outros símbolos via HTML/CSS. O botão de orçamento permanece somente no bloco de peça própria. Os filtros disponíveis são busca, categoria, faixa de preço e ordenação, sem filtro de popularidade.
- **Responsividade Obrigatória:**
    - **Desktop (Acima de 900px):** Layout completo (largura máxima de 1180px), filtros dispostos ao lado dos produtos na home e painel administrativo estruturado em colunas[cite: 6].
    - **Tablet e Celular (Até 900px):** Adaptação para colunas em formato de pilha única (filtros no topo, formulários empilhados)[cite: 6].
    - **Celular Pequeno (Até 600px):** Redução proporcional de títulos, padding lateral de 16px, grade de produtos da vitrine em 2 colunas e botões principais com largura total (100%)[cite: 6].

## 2. Tipografia (Google Fonts)

- **Fonte Script (`Great Vibes`):** Exclusiva para o logotipo da loja (_Alê Bordados Kids_) no cabeçalho, rodapé e telas de autenticação.
- **Fonte Corpo (`Nunito`):** Utilizada em toda a aplicação:
    - Peso 400: Textos corriqueiros, descrições e placeholders de inputs[cite: 2].
    - Peso 600: Títulos de seções (`h1`, `h2`, `h3`), itens de menu e nomes de produtos[cite: 2].
    - Peso 700: Botões de ação, preços e rótulos (`labels`) de formulários[cite: 2].
    - Amostras de personalização usam apenas Nunito e Great Vibes, variando peso e estilo para representar cada escolha.

## 3. Paleta de Cores Oficial e Variáveis CSS

As definições de cores ficam centralizadas em `resources/css/app.css`; os demais módulos usam somente tokens semânticos. Principais tokens:

- `--cor-rosa` (`#D9779D`): Estrelas de avaliação, botões de favorito, contadores e detalhes decorativos[cite: 3].
- `--cor-rosa-botao` (`#AB5E7C`): Fundo dos botões principais de ação[cite: 3, 4].
- `--cor-rosa-escuro` (`#B5446F`): Efeito hover dos botões principais, preços e textos de destaque[cite: 3].
- `--cor-rosa-claro` (`#FBEFF3`): Fundo decorativo suave e estados selecionados[cite: 3].
- `--cor-fundo` (`#FFFFFF`) e `--cor-fundo-secundario` (`#F8F6F6`): Superfícies principais e secundárias[cite: 3].
- `--cor-texto` (`#2A2629`) e `--cor-texto-suave` (`#756D72`): Textos principais e secundários[cite: 3].
- `--cor-borda` (`#ECE7E9`): Linhas divisórias e bordas finas de componentes[cite: 3].

## 4. Componentes Globais e Estados de UI

- **Botões (`.btn`):** Cantos arredondados (`border-radius: 999px`), padding de `10px 24px`, fonte peso 700, estado de foco visível com outline rosa de 2px[cite: 4].
- **Campos de Formulário (`.campo`):** Largura de 100%, padding de `10px 14px`, borda de 1.5px na cor `--cor-borda` e arredondamento de 12px (mudando para `--cor-rosa` no foco e `--cor-erro` em caso de erro).
- **Badges de Status de Pedidos / Orçamentos:**
    - _Em análise / Novo:_ Fundo `#FDE8EF`, Texto `#A93863`.
    - _Orçamento pendente:_ Fundo `#FFF4D9`, Texto `#9A6A00`.
    - _Pagamento pendente:_ Fundo `--cor-aviso-fundo`, Texto `--cor-aviso-texto`.
    - _Em produção:_ Fundo `#FFF0E3`, Texto `#A65A1C`.
    - _Finalizado / Entregue:_ Fundo `--cor-fundo-status-finalizado`, Texto `--cor-texto-status-finalizado`.
    - _Cancelado:_ Fundo `--cor-fundo-admin`, Texto `--cor-texto-suave`.

## 5. Mapeamento das Telas Baseadas nos Wireframes

1. **Home / Catálogo (`/`):** Vitrine dinâmica de produtos, hero minimalista, bloco explicativo para peças próprias, filtros sem popularidade e seção de avaliações de clientes[cite: 2, 7].
2. **Detalhes do Produto (`/produtos/{id}`):** Galeria de fotos, especificações do item e campos interativos de personalização (texto, cor da linha, fontes, observações e upload de imagem de referência)[cite: 4, 10].
3. **Solicitação de Orçamento (`/orcamento`):** Formulário em etapas para envio de peças do próprio cliente (marcado explicitamente como funcionalidade em andamento/simulação)[cite: 5].
4. **Autenticação (`/login` e `/cadastro`):** Cards centralizados e limpos para acesso e registro de usuários[cite: 6, 7].
5. **Painel Administrativo (`/admin`):** Dashboard analítica contendo KPIs, lista de pendências, pedidos recentes, seção de chats e gráfico de faturamento. A seção de conversas fica acima do gráfico; cada contato abre o modal centralizado do chat, cujo envio permanece simulado. O calendário de entregas e o CRUD de produtos são acessados pela navegação administrativa[cite: 9, 10, 11].
