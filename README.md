# Sistema de Gerenciamento de Contatos com PHP e MySQL

Este projeto é um sistema completo para gerenciamento de contatos, desenvolvido com PHP, MySQL, HTML, CSS e JavaScript. Ele oferece funcionalidades de cadastro, listagem, edição e exclusão de contatos, com foco em segurança, validação de dados e uma interface de usuário intuitiva e moderna.

## Funcionalidades

*   **Cadastro de Contatos**: Formulário para adicionar novos contatos com validação robusta.
*   **Listagem de Contatos**: Visualização de todos os contatos cadastrados em uma tabela organizada.
*   **Edição de Contatos**: Funcionalidade para atualizar informações de contatos existentes, incluindo a opção de manter a senha ou definir uma nova.
*   **Exclusão de Contatos**: Opção para remover contatos da base de dados, com modal de confirmação para evitar exclusões acidentais.
*   **Validação de Dados**: Validação abrangente no frontend (JavaScript) para feedback imediato e no backend (PHP) para garantir a integridade e segurança dos dados.
*   **Segurança Robusta**:
    *   **Prevenção de SQL Injection**: Utiliza `PDO` (PHP Data Objects) com *prepared statements* para todas as interações com o banco de dados.
    *   **Proteção CSRF**: Implementa um token *Cross-Site Request Forgery* para proteger contra requisições maliciosas.
    *   **Armazenamento Seguro de Senhas**: As senhas são armazenadas como *hashes* utilizando `password_hash()` com `PASSWORD_BCRYPT`.
    *   **Escape de Saída**: Utiliza `htmlspecialchars()` para prevenir ataques *Cross-Site Scripting* (XSS) ao exibir dados.
    *   **Tratamento de Duplicidade**: Verifica a existência de e-mails já cadastrados para evitar registros duplicados.
*   **Feedback ao Usuário**: Mensagens de sucesso e erro claras e específicas são exibidas para orientar o usuário.
*   **Interface Moderna**: Design responsivo com um tema escuro, navegação por abas, barra de força de senha, contador de caracteres e efeitos visuais para uma experiência de usuário aprimorada.

## Estrutura do Projeto

A estrutura de diretórios do projeto é a seguinte:

```text
contatos_project/
├── app.js
├── assets/
│   └── style.css
├── config.php
├── database.sql
├── editar.php
├── index.php
└── lista.php
```

### Descrição dos Arquivos:

*   `index.php`: Página principal que exibe o formulário de cadastro de novos contatos. Processa o envio do formulário, realiza validações e interage com o banco de dados para salvar os dados.
*   `lista.php`: Página responsável por exibir a lista de todos os contatos cadastrados e oferece funcionalidades de exclusão.
*   `editar.php`: Página dedicada à edição de contatos existentes. Carrega os dados do contato selecionado, permite a atualização e trata a senha como um campo opcional para edição.
*   `config.php`: Contém as configurações de conexão com o banco de dados (host, nome, usuário, senha) e funções auxiliares essenciais, como `getDB()` para a conexão PDO, `gerarCSRF()` e `validarCSRF()` para proteção CSRF, e `limpar()` para higienização de dados.
*   `database.sql`: Script SQL para a criação do banco de dados `cadastro_contatos` e da tabela `contatos`.
*   `app.js`: Script JavaScript que implementa a lógica do frontend, incluindo validações em tempo real, contador de caracteres, alternância de visibilidade da senha, barra de força da senha e o relógio na barra de status.
*   `assets/style.css`: Folha de estilos CSS que define a aparência visual de toda a aplicação, incluindo o tema escuro, responsividade e estilos para os componentes da interface.

## Requisitos

Para executar este projeto, você precisará de um ambiente de servidor web com suporte a PHP e MySQL. As seguintes ferramentas são recomendadas:

*   **Servidor Web**: Apache ou Nginx.
*   **PHP**: Versão 7.4 ou superior, com a extensão `PDO MySQL` habilitada.
*   **Banco de Dados**: MySQL.
*   **Gerenciador de Banco de Dados**: MySQL Workbench ou uma ferramenta equivalente para importar o script SQL.
*   **XAMPP (Recomendado para Windows)**: Um pacote que inclui Apache, MySQL e PHP, facilitando a configuração do ambiente em sistemas Windows.

## Configuração do Banco de Dados

O projeto utiliza o banco de dados `cadastro_contatos` e a tabela `contatos`.

### 1. Criar e Importar o Banco de Dados

1.  Abra seu gerenciador de banco de dados (MySQL Workbench).
2.  Conecte-se ao seu servidor MySQL.
3.  Execute o script `database.sql` localizado na raiz do projeto (`contatos_project/database.sql`). Este script criará o banco de dados `cadastro_contatos` (se ainda não existir) e a tabela `contatos` com a seguinte estrutura:

    | Campo           | Tipo                  | Descrição                                         |
    | :-------------- | :-------------------- | :------------------------------------------------ |
    | `id`            | `INT`                 | Chave primária auto-incrementável.                |
    | `nome`          | `VARCHAR(100)`        | Nome completo do contato.                         |
    | `email`         | `VARCHAR(150)`        | Endereço de e-mail (único).                       |
    | `senha`         | `VARCHAR(255)`        | Hash da senha do contato.                         |
    | `mensagem`      | `TEXT`                | Mensagem enviada pelo contato.                    |
    | `criado_em`     | `DATETIME`            | Data e hora de criação do registro.               |
    | `atualizado_em` | `DATETIME`            | Data e hora da última atualização do registro.    |

    **Observações Importantes:**
    *   A senha é armazenada como um hash seguro, não em texto puro.
    *   O e-mail possui uma restrição de unicidade, garantindo que cada contato tenha um e-mail exclusivo.

### 2. Configurar Credenciais do Banco de Dados

Abra o arquivo `config.php` e verifique as seguintes constantes de conexão com o banco de dados:

```php
define("DB_HOST", "localhost");
define("DB_NAME", "cadastro_contatos");
define("DB_USER", "root");       // Altere para o seu usuário MySQL
define("DB_PASS", "");           // Altere para a sua senha MySQL
define("DB_CHARSET", "utf8mb4");
```

*   Se o seu servidor MySQL exigir uma senha, atualize o valor de `DB_PASS` de acordo.
*   Se o seu banco de dados ou usuário tiverem nomes diferentes, ajuste `DB_NAME` e `DB_USER`.

## Como Executar o Projeto

### 1. Preparar o Ambiente

*   **Com XAMPP (Windows)**:
    1.  Certifique-se de que o Apache e o MySQL estão iniciados no XAMPP Control Panel.
    2.  Copie a pasta `contatos_project` (o diretório raiz deste projeto) para dentro do diretório `htdocs` do XAMPP. Por exemplo: `C:\xampp\htdocs\contatos_project`.

*   **Em Outros Ambientes (Linux/macOS/Servidor)**:
    1.  Configure seu servidor web (Apache/Nginx) para servir o diretório `contatos_project`.
    2.  Certifique-se de que o PHP e o MySQL estão configurados e em execução.

### 2. Acessar a Aplicação

Após configurar o ambiente e o banco de dados, abra seu navegador e acesse a URL correspondente ao diretório do projeto:

*   **Com XAMPP (Windows)**:
    ```text
    http://localhost/contatos_project/
    ```
*   **Em Outros Ambientes**: A URL dependerá da sua configuração de servidor web (ex: `http://localhost/` se o projeto estiver na raiz do seu servidor, ou `http://seuservidor.com/contatos_project/`).

## Regras de Validação

As seguintes regras de validação são aplicadas aos campos do formulário:

### Nome

*   **Obrigatório**.
*   Deve ter entre **3 e 100 caracteres**.

### E-mail

*   **Obrigatório**.
*   Deve ter um **formato de e-mail válido**.
*   Máximo de **150 caracteres**.
*   Deve ser **único** (não pode haver outro registro com o mesmo e-mail no banco de dados).

### Senha

*   **Obrigatória** (no cadastro).
*   Mínimo de **8 caracteres**.
*   Deve conter:
    *   Pelo menos uma **letra maiúscula**.
    *   Pelo menos um **número**.
*   Na edição, a senha é **opcional**: se deixada em branco, a senha existente é mantida.

### Mensagem

*   **Opcional** (no cadastro, mas com limite de caracteres).
*   Máximo de **250 caracteres**.

## Fluxo da Aplicação

1.  **Cadastro (`index.php`)**:
    *   O usuário preenche o formulário. O JavaScript (`app.js`) valida os campos em tempo real, fornecendo feedback imediato.
    *   Ao enviar o formulário, os dados são enviados para `index.php` via método POST.
    *   O PHP em `index.php` realiza validações de segurança (CSRF) e de dados (nome, e-mail, senha, mensagem).
    *   Verifica a duplicidade de e-mail no banco de dados.
    *   Se houver erros, mensagens são exibidas e os dados preenchidos são mantidos no formulário.
    *   Se todos os dados forem válidos, a senha é *hashed* e os dados são salvos na tabela `contatos`. Uma mensagem de sucesso é exibida.

2.  **Listagem (`lista.php`)**:
    *   Exibe todos os contatos cadastrados, com opções para editar ou excluir cada um.
    *   A exclusão de um contato é feita via POST, com validação CSRF e um modal de confirmação.

3.  **Edição (`editar.php`)**:
    *   Carrega os dados de um contato específico com base no `id` fornecido na URL.
    *   Permite ao usuário atualizar o nome, e-mail e mensagem. A senha pode ser alterada ou mantida.
    *   Realiza validações semelhantes às do cadastro, incluindo a verificação de e-mail único (excluindo o próprio contato).
    *   Atualiza os dados no banco de dados e redireciona para a lista com uma mensagem de sucesso.

## Solução de Problemas Comuns

### Erro de Conexão com o Banco de Dados

Se você encontrar erros relacionados à conexão com o banco de dados, verifique os seguintes pontos:

*   Certifique-se de que o servidor MySQL está em execução.
*   Confira se as constantes `DB_HOST`, `DB_NAME`, `DB_USER` e `DB_PASS` em `config.php` estão corretas e correspondem às suas configurações de banco de dados.
*   Verifique se o banco de dados `cadastro_contatos` e a tabela `contatos` foram criados corretamente executando o script `database.sql`.

### Tabela `contatos` não Encontrada

Este erro geralmente indica que o script `database.sql` não foi executado ou foi executado em um banco de dados incorreto. Certifique-se de que o script foi importado para o banco de dados `cadastro_contatos`.

### E-mail Já Cadastrado

Esta é uma validação esperada. Significa que o e-mail que você tentou cadastrar já existe na base de dados. Tente usar um e-mail diferente ou edite o contato existente.

## Observação Final

Este projeto demonstra um sistema de gerenciamento de contatos funcional e seguro, aplicando boas práticas de desenvolvimento web, como validação de dados, proteção contra vulnerabilidades comuns e uma arquitetura organizada. É uma excelente base para aprendizado e para ser expandido com novas funcionalidades.
