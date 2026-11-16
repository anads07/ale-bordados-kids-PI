# Spec 02: Business Rules & Development Guidelines — Alê Bordados Kids

## 1. Permissões e Acessos (Autenticação)

- **Perfil Administrador:**
    - Acesso restrito ao painel administrativo (`/admin`).
    - Único perfil autorizado a criar, alterar e excluir produtos do catálogo.
    - Acesso à gestão de pedidos, calendário e visualização dos dados da dashboard.
- **Perfil Cliente / Usuário Comum:**
    - Pode se registrar na plataforma, fazer login e navegar pelo catálogo público.
    - Pode interagir com o carrinho de compras e adicionar itens aos favoritos.

## 2. Regras de Dados (Real vs. Mockado)

Para a presente versão de testes e desenvolvimento com IA, o comportamento dos dados divide-se estritamente em:

- **Dados Reais (Conectados ao Banco de Dados MySQL):**
    - **Módulo de Produtos (CRUD):** O Administrador pode cadastrar novos produtos (nome, categoria, descrição, preço, estoque, opções de personalização e imagens), alterá-los ou excluí-los. Os clientes visualizam esses produtos dinamicamente na página inicial e na vitrine.
    - **Autenticação:** Registro e login de usuários comuns e do administrador.
- **Dados Mockados (Fictícios / Estáticos na Interface):**
    - Contadores da Dashboard do Admin (ex: _14 Em produção_, _7 Pedidos novos_, _3 Orçamentos pendentes_, _4 Pagamentos pendentes_).
    - Gráficos de faturamento mensal e faturamento por mês.
    - Classificação por estrelas de cada pedido e as avaliações exibidas na home e na página de produtos.
    - Histórico de mensagens do chat administrativo com os clientes.
    - Chat administrativo, avaliações e depoimentos permanecem simulados. Pedidos recentes, calendário e atualização de status já refletem os pedidos reais do banco.

## 3. Funcionalidades em Andamento (Placeholders)

As seguintes funcionalidades estarão visíveis na interface conforme os wireframes do projeto, mas a sua lógica de submissão e processamento completo está marcada como **função em andamento / em breve**:

- **Solicitação de Orçamento de Peça Própria:** O formulário de envio de fotos e especificações de peças do próprio cliente exibirá um alerta amigável de "Função em andamento" ao ser acionado.
- **Ajuda de IA para Descrições:** O botão de geração de descrições automáticas no painel de cadastro de produtos funcionará como um placeholder interativo.

## 4. Carrinho, Estoque e Checkout

- Visitantes podem montar o carrinho; a autenticação é exigida somente para finalizar o pedido.
- Cada personalização diferente cria uma linha própria, mas a quantidade total do mesmo produto não pode ultrapassar o estoque disponível.
- Informar quantidade zero remove a linha. Remover uma linha também apaga a imagem de inspiração associada.
- O checkout grava um pedido por linha personalizada, confirma o estoque dentro de uma transação e só limpa a sessão após a gravação bem-sucedida.
- As imagens de inspiração permanecem disponíveis depois da compra para continuar vinculadas aos pedidos.

---

## 5. Diretrizes de Desenvolvimento e Boas Práticas

Os desenvolvedores devem seguir as seguintes normas de qualidade de código:

1. **Simplicidade em Primeiro Lugar:** Prefira sempre soluções simples, legíveis e diretas em detrimento de abordagens excessivamente complexas ou arquiteturas desnecessárias para o escopo atual.
2. **Reutilização de Código:** Priorize a reutilização de funções, métodos, helpers e componentes Blade globais para evitar repetições.
3. **Zero Duplicação (DRY - Don't Repeat Yourself):** Extraia trechos de UI repetitivos para componentes Blade reutilizáveis (ex: cards de produtos, badges de status, botões padronizados).
4. **Separação de Responsabilidades:** Mantenha a lógica de negócios isolada nos Controllers ou Services, deixando as Views (Blade) limpas e focadas apenas na apresentação.
5. **Padronização de Nomenclatura:** Siga as convenções oficiais do Laravel (Controllers em PascalCase no singular, tabelas no plural snake_case, rotas em kebab-case).
6. **Tratamento de Erros e Validação:** Todos os formulários (especialmente o CRUD de produtos e autenticação) devem conter validações robustas no Request do Laravel (`FormRequest`) e mensagens de erro amigáveis na interface.
7. **Segurança Básica:** Proteção contra CSRF em todos os formulários e uso rigoroso de atribuição em massa protegida (`$fillable` nos Models).
8. **Responsividade Garantida:** Todo o layout gerado deve respeitar os pontos de quebra definidos na identidade visual (Desktop acima de 900px, Tablet/Mobile empilhado e celulares pequenos com colunas otimizadas).
9. **Respeito ao Design System:** Nenhuma cor, espaçamento ou tipografia deve ser inventada fora das variáveis CSS e diretrizes estabelecidas na identidade visual (uso estrito das fontes _Great Vibes_ e _Nunito_ e cores oficiais).
10. **Comentários e Documentação Limpa:** O código gerado deve vir limpo, estruturado e comentado apenas onde a lógica não for autoexplicativa.
