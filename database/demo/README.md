# Pacote de demonstração local

## Arquivos

- `culturapg_demo_snapshot_2026-11-22.sql`: cópia isolada usada nos testes autenticados, com usuários e eventos de demonstração.
- `../exports/culturapg_snapshot_2026-11-22.sql`: exportação 1:1 do banco `culturapg` antes dos testes, com 15 tabelas e 86 registros.
- `../../public/img/demo-evento.png` e `../../public/img/demo-perfil.png`: imagens sintéticas seguras usadas pela demonstração.

O snapshot original inclui os registros de usuário e seus hashes de senha, conforme solicitado; não contém senhas em texto puro. As contas do banco foram informadas pelo proprietário como contas de teste.

## Importação

Use uma conta MySQL administrativa para importar o snapshot demo em `culturapg_demo`. Ele cria as tabelas e usa `DROP TABLE IF EXISTS`; importe-o somente no schema de demonstração, nunca em `culturapg`.

O snapshot 1:1 cria o schema separado `culturapg_snapshot_20261122`, para manter o nome `culturapg` protegido durante a restauração.

Para executar o site local apontando ao demo, configure `config/database.local.php` com `dbname` igual a `culturapg_demo`. Não publique esse arquivo de configuração nem o arquivo local `config/demo-accounts.local.txt`, que guarda as senhas de teste desta máquina.

O snapshot 1:1 do banco principal preserva o caminho da imagem cadastrada no banco, mas não inclui o arquivo binário correspondente: ele contém resultados de um inventário de personalidade e foi mantido fora do repositório. O pacote demo usa as imagens sintéticas acima.
