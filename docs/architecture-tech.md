# Spec: Architecture & Technical Specs — Alê Bordados Kids

## 1. Stack Tecnológica

- **Backend:** PHP 8.5 / Laravel.
- **Frontend / Views:** Blade Templates.
- **Estilização:** CSS puro com tokens da identidade visual, integrado ao Tailwind CSS 4.
- **Build:** Vite 8, com entradas separadas para a área pública e a administrativa.
- **Banco de Dados:** MySQL em desenvolvimento e SQLite em memória nos testes automatizados.

## 2. Estrutura de Pastas e Organização do Projeto

O projeto segue a estrutura padrão do Laravel, com separação explícita entre as áreas pública e administrativa:

- `app/`
    - `Http/Controllers/Admin/` — dashboard, pedidos e CRUD de produtos.
    - `Http/Controllers/User/` — vitrine, autenticação, carrinho, checkout, favoritos, perfil e Sobre Nós.
    - `Http/Middleware/Admin/` — autorização da área administrativa.
    - `Http/Requests/` — validação de produtos, login, cadastro e carrinho.
    - `Models/` — `Usuario`, `Produto` e `Pedido`.
    - `Services/User/CartService.php` — altera as linhas do carrinho na sessão e aplica os limites de estoque.
    - `Services/User/CartLineNormalizer.php` — normaliza os dados das linhas e mantém compatibilidade com sessões antigas.
    - `Services/User/CartPresenter.php` — busca produtos e prepara valores e personalizações para as views.
    - `Services/User/FavoriteService.php` — gerencia os identificadores de produtos favoritos na sessão.
    - `Providers/` — compositores de view e inicialização da aplicação.
- `resources/`
    - `views/admin/` — páginas e componentes da área administrativa, em `admin/components/`.
    - `views/user/` — páginas e componentes da vitrine e da conta do cliente, em `user/components/`.
    - `views/shared/components/` — componentes usados pelas duas áreas.
    - `css/` — design system centralizado e módulos por contexto (`user` e `admin`).
    - `js/` — módulos JavaScript separados por contexto.
- `database/`
    - `migrations/` — tabelas oficiais e estruturas padrão do Laravel.
    - `seeders/Admin/` — criação do administrador.
    - `seeders/User/` — catálogo inicial exibido na vitrine.
    - `seeders/DatabaseSeeder.php` — ponto global que executa os seeders por contexto.
- `routes/`
    - `web.php` — carrega as rotas.
    - `user.php` — rotas públicas e do cliente.
    - `admin.php` — rotas protegidas da administração.
- `docs/` — especificações do projeto, capturas de tela e este documento técnico.

## 3. Arquitetura e Padrões de Código

- **Separação de Responsabilidades:** Controllers orquestram o fluxo; Requests validam; `CartService` gerencia a sessão e o estoque, `CartLineNormalizer` trata o formato dos dados e `CartPresenter` prepara os itens para exibição. `FavoriteService` gerencia os favoritos; Models cuidam de persistência e relacionamentos.
- **Reutilização de Componentes:** Componentes Blade anônimos para cartões, badges, gráficos, calendário, modais e títulos.
- **Nomenclatura em pt-BR:** Variáveis, métodos e comentários usam português, preservando termos técnicos universais e contratos do framework.
- **Simplicidade Técnica:** Soluções diretas e legíveis, sem abstrações desnecessárias para o escopo.

## 4. Banco de Dados e Relacionamentos

- **Tabelas reais:** `usuarios`, `produtos` e `pedidos`.
- **Autenticação:** o model `Usuario` usa a tabela `usuarios`, chave `id_us` e coluna de senha `senha`.
- **Produtos:** o CRUD administrativo e a vitrine consomem a tabela `produtos`.
- **Pedidos:** cada linha personalizada do carrinho gera um registro em `pedidos`, com vínculo ao usuário, ao produto, à personalização, à data e ao status.
- **Dados simulados:** indicadores, gráficos, chat administrativo, avaliações e depoimentos permanecem em `config/mock.php`; pedidos recentes, calendário e status usam dados reais.
- **Sessões:** armazenam carrinho, favoritos e autenticação; por isso a migration padrão de `sessions` é mantida.

## 5. Segurança e Autorização

- **Área administrativa:** exige autenticação e o middleware `admin`.
- **Middleware `EnsureUserIsAdmin`:** valida `is_admin` e o e-mail definido em `config/admin.php`.
- **Formulário de produto:** `ProdutoRequest::authorize()` reforça a permissão no nível da requisição.
- **CSRF:** presente em todos os formulários.
- **Senhas:** armazenadas com cast `hashed` na coluna `senha`.
- **Atribuição em massa:** protegida por `$fillable`.
- **Rate limiting:** login e cadastro usam `throttle:6,1`.
- **Seeder administrativo:** exige `ADMIN_EMAIL` e `ADMIN_PASSWORD` configurados no ambiente; não há credenciais padrão.

## 6. Padrões de Nomenclatura e Convenções

- **Controllers:** PascalCase no singular, separados por contexto (`Admin` e `User`).
- **Models:** PascalCase no singular (`Usuario`, `Produto`, `Pedido`).
- **Tabelas do Banco:** snake_case no plural, em português (`usuarios`, `produtos`, `pedidos`).
- **Rotas:** kebab-case e nomes de rota com ponto (`admin.produtos.index`, `carrinho.store`).
- **Views:** organizadas por contexto (`admin/...`, `user/...`); componentes administrativos e do cliente ficam em `admin/components/` e `user/components/`, e os compartilhados em `shared/components/`.
- **CSS:** tokens semânticos em pt-BR (`--cor-fundo`, `--cor-texto`, `--cor-borda` e demais estados) definidos exclusivamente em `resources/css/app.css`; os módulos CSS consomem os tokens sem cores literais locais. A tipografia da interface usa `Great Vibes` e `Nunito`; as amostras de personalização usam variações de estilo dessas famílias.
- **JavaScript:** variáveis locais em pt-BR; contratos JSON da API preservam os nomes esperados pelo backend.

## 7. Documentação do Projeto

As especificações funcionais, de negócio, de interface e de arquitetura são mantidas em `docs/`. Os arquivos `AGENTS.md` e `CLAUDE.md` e as pastas `.agents/` e `.claude/` contêm instruções e habilidades de ferramentas de desenvolvimento, não especificações do produto. O `README.md` atual é o modelo padrão do Laravel e não contém regras próprias do projeto.
