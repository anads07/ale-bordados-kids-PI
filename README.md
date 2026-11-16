# Alê Bordados Kids

Aplicação web para apresentar e personalizar peças infantis bordadas e apoiar a operação da loja.

## Funcionalidades

- Vitrine com busca, filtros, ordenação e detalhes dos produtos.
- Personalização de peças, favoritos, carrinho com controle de estoque e finalização de pedidos.
- Cadastro e acesso de clientes, além de consulta ao perfil e aos pedidos.
- Área administrativa protegida para gerenciar produtos e acompanhar pedidos, calendário e status.
- Indicadores, avaliações e chat da administração incluem dados simulados. O orçamento de peças próprias está em andamento.

## Tecnologias

Versões verificadas nesta preparação: PHP 8.5.9, Composer 2.11.2, Node.js 24.19.0 e npm 11.17.0. O projeto requer PHP `^8.3`.

Versões resolvidas nos lockfiles: Laravel 13.42.0, Tailwind CSS 4.3.5, Vite 8.3.8 e Laravel Vite Plugin 3.2.1. As views usam Blade; o banco de desenvolvimento é MySQL e os testes estão configurados para SQLite em memória.

## Pré-requisitos

- PHP `^8.3`, Composer, Node.js e npm.
- MySQL e a extensão `pdo_mysql` para a aplicação; habilite `pdo_sqlite` para executar os testes.
- Extensões PHP usadas pelas dependências: Ctype, DOM, Fileinfo, Filter, Hash, Iconv, JSON, Libxml, Mbstring, OpenSSL, PCRE, Phar, Session, Tokenizer, XML e XMLWriter.

## Instalação no Windows

1. Clone o repositório e entre na pasta do projeto:

   ```powershell
   git clone <URL-do-repositorio>
   Set-Location .\ale-bordados-kids
   ```

2. Instale as dependências:

   ```powershell
   composer install
   npm install
   ```

3. Crie o arquivo de ambiente e gere a chave da aplicação:

   ```powershell
   Copy-Item .env.example .env
   php artisan key:generate
   ```

4. Crie um banco MySQL vazio e ajuste no `.env` a conexão (`DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` e `DB_PASSWORD`).

5. Antes de popular o banco, defina `ADMIN_EMAIL` e `ADMIN_PASSWORD` no `.env` com valores próprios e seguros. O seeder não fornece credenciais administrativas padrão.

6. Execute as migrations e os seeders:

   ```powershell
   php artisan migrate
   php artisan db:seed
   ```

## Desenvolvimento

Em terminais PowerShell separados, na pasta do projeto:

```powershell
php artisan serve
```

```powershell
npm run dev
```

## Testes e build

```powershell
composer test
npm run build
```

Os testes usam SQLite em memória; verifique se a extensão `pdo_sqlite` está habilitada no PHP.

Não versione o `.env`, senhas ou outras credenciais. Configure os valores administrativos somente no ambiente local ou no gerenciador de segredos do ambiente de execução.
