# Spec 01: Project Overview / Visão Geral — Alê Bordados Kids

## 1. Propósito do Projeto e Contexto

O sistema **Alê Bordados Kids** é uma plataforma web desenvolvida em Laravel para um negócio real de pequeno porte focado em **vestuário e enxovais infantis com bordados personalizados**.

O principal objetivo do sistema é centralizar a gestão de pedidos, organizar o fluxo de produção, reduzir erros operacionais (como trocas de nomes ou cores) e proporcionar uma vitrine elegante e intuitiva para os clientes.

## 2. Problema que Resolve

- **Desorganização e Fragmentação:** Atualmente, os pedidos e informações ficam espalhados entre cadernos físicos e arquivos no computador. O sistema unifica essas informações em um único ambiente.
- **Erros de Comunicação na Personalização:** O cliente preenche diretamente os dados exatos do bordado (texto, cor da linha, fonte e referências), evitando falhas manuais.
- **Gestão de Prazos:** Ajuda a controlar a fila de produção e os prazos de entrega para evitar atrasos.

## 3. Público-Alvo

- **Clientes (Pais e Familiares):** Usuários que buscam peças infantis exclusivas ou que desejam enviar peças próprias para orçamento e customização.
- **Administradores (Loja / Produção):** Os responsáveis pelo negócio que precisam gerenciar o catálogo de produtos, acompanhar pedidos por status, visualizar o painel financeiro e interagir com os clientes via chat.

## 4. Escopo e Funcionalidades Principais

### Área Pública (Cliente)

- **Home / Catálogo:** Vitrine de produtos dinâmicos vindos do banco de dados, com busca, filtros por categoria e faixa de preço e ordenação por relevância, preço ou nome.
- **Página do Produto:** Detalhes da peça e campos interativos de personalização (texto, desenho, cores da linha, fontes, observações e upload de imagem de inspiração).
- **Orçamento de Peça Própria:** Formulário para envio de fotos e especificações de roupas/toalhas do próprio cliente (marcada explicitamente como **função em andamento / em breve**).
- **Autenticação:** Páginas de Login e Cadastro de novos usuários.

### Área Administrativa (Admin)

- **Acesso Restrito:** Apenas o administrador possui privilégios de gestão.
- **CRUD de Produtos:** Capacidade exclusiva de **Criar, Alterar e Excluir** produtos do catálogo.
- **Dashboard e KPIs:** Indicadores de pedidos em produção, novos, pagamentos pendentes e gráficos de faturamento (dados mockados/fictícios).
- **Gerenciamento de Pedidos & Calendário:** Visualização de status e prazos de entrega.
- **Chat Simulado:** Área de mensagens entre o administrador e os clientes.

## 5. Tecnologias Utilizadas

- **Backend:** PHP / Laravel
- **Frontend:** Blade Templates
- **Estilização:** CSS e uso de tailwind baseado estritamente no Guia de Identidade Visual (Variáveis CSS, paleta de cores rosa/tinta/suave, fontes _Great Vibes_ e _Nunito_).
- **Banco de Dados:** MySQL para produtos, usuários e pedidos reais; SQLite em memória nos testes automatizados.
