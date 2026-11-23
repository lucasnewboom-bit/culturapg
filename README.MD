# CulturaPG

Plataforma de divulgação de eventos culturais de Ponta Grossa - PR, desenvolvida em PHP, MySQL 8.0, JavaScript, Bootstrap 5 e Leaflet.

## Requisitos

- XAMPP com Apache e PHP 7.4 ou superior, com as extensões PDO MySQL, Fileinfo e mbstring habilitadas.
- MySQL Server 8.0. O projeto pode usar o servidor MySQL separado do XAMPP.
- Git, caso queira baixar atualizações do repositório.

## Instalação local

1. Abra o PowerShell e baixe o repositório dentro do XAMPP:

   ```powershell
   cd C:\xampp\htdocs
   git clone https://github.com/lucasnewboom-bit/culturapg.git
   ```

2. No MySQL Workbench, abra e execute `database/setup.sql`. O arquivo cria a estrutura e os registros essenciais de cidade e categorias, sem incluir contas ou dados pessoais.
3. Copie `config/database.example.php` para `config/database.local.php`.
4. Edite `config/database.local.php` e informe o endereço, a porta, o usuário e a senha do seu MySQL Server.
5. Inicie o Apache no XAMPP e o MySQL Server 8.0.
6. Abra `http://localhost/culturapg/public/`.

Se você restaurou um banco salvo antes da atualização de categorias, execute uma vez o arquivo `database/migrations/20261123_add_categoria_owner.sql` no schema configurado em `config/database.local.php`.

`config/database.local.php` e as fotos de perfil ficam fora do Git para proteger credenciais e arquivos pessoais. A pasta de fotos é criada para receber uploads quando o sistema for usado.

## Atualizações do código

Na pasta do projeto, use `git pull` para trazer as atualizações publicadas. Não substitua sua configuração local do banco.

## Banco de dados

O arquivo `database/setup.sql` contém somente a estrutura das tabelas e os dados fixos necessários para iniciar o sistema. Ele não contém usuários, senhas, favoritos, histórico, notificações, organizadores ou eventos cadastrados no computador de desenvolvimento.
