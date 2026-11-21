V O L T A U T O — SISTEMA DE GESTÃO DE AUTOELÉTRICA

TECNOLOGIAS
HTML5, CSS3, JavaScript, PHP 8+, MySQL/MariaDB e PDO.

INSTALAÇÃO LOCAL (XAMPP)
1. Instale e abra o XAMPP.
2. Inicie Apache e MySQL.
3. Copie a pasta voltauto para C:\xampp\htdocs\ (Windows) ou para a pasta htdocs equivalente.
4. Acesse http://localhost/phpmyadmin.
5. Importe o arquivo database.sql na aba Importar. Ele cria o banco voltauto, as quatro tabelas, os relacionamentos e registros de demonstração.
6. Confira config.php. A configuração inicial usa host localhost, banco voltauto, usuário root e senha vazia, padrão comum do XAMPP. Ajuste caso seu MySQL use outras credenciais.
7. Acesse http://localhost/voltauto/.
8. Clique em Criar uma conta, cadastre seu usuário e entre.

PRIMEIRO ACESSO
Não existe senha fixa ou senha administrativa gravada no projeto. O primeiro usuário deve ser criado pela tela de cadastro. A senha é armazenada com password_hash() e validada com password_verify().

FUNCIONALIDADES
- Cadastro, login, validação de sessão e logout.
- Páginas internas protegidas por autenticação.
- CRUD de clientes.
- CRUD de veículos vinculados a clientes.
- CRUD de serviços vinculados a veículos.
- Pesquisa rápida na tabela, validações de formulário e confirmação de exclusão.
- Consultas preparadas via PDO, proteção CSRF e escape de saída HTML.

ESTRUTURA DO BANCO
usuarios (id PK)
clientes (id PK)
veiculos (id PK, cliente_id FK -> clientes.id)
servicos (id PK, veiculo_id FK -> veiculos.id)
Relacionamentos 1:N: clientes -> veiculos -> servicos.

TESTE SUGERIDO
1. Cadastre um usuário novo e entre.
2. Abra Clientes, cadastre, edite e exclua um registro de teste.
3. Abra Veículos e vincule um veículo a um cliente.
4. Abra Serviços e vincule um serviço ao veículo.
5. Edite o status e o valor do serviço.
6. Saia pelo botão Sair e tente abrir http://localhost/voltauto/clientes.php. O sistema deve redirecionar ao login.
7. Teste um e-mail/senha incorretos.

ROTEIRO SUGERIDO PARA O VÍDEO (ATÉ 10 MIN)
- 0:00–0:30: apresente-se dizendo seu nome e o objetivo do sistema.
- 0:30–1:20: abra http://localhost/voltauto/ e explique o domínio: gestão de uma autoelétrica.
- 1:20–2:40: mostre o phpMyAdmin, o banco voltauto, as tabelas e o Desenhador. Explique PK, FK e os relacionamentos clientes 1:N veículos e veículos 1:N serviços. Execute SELECT * FROM clientes;, SELECT * FROM veiculos; e SELECT * FROM servicos;.
- 2:40–3:20: mostre usuarios no phpMyAdmin após cadastro. Explique que a coluna senha contém hash gerado por password_hash(), não a senha original.
- 3:20–4:40: demonstre login válido, login inválido, cadastro de usuário e logout. Tente abrir uma página interna sem sessão.
- 4:40–5:30: apresente a interface, menu, responsividade e validações.
- 5:30–8:50: demonstre SELECT, INSERT, UPDATE e DELETE em clientes, veículos e serviços, mostrando a alteração no navegador e no phpMyAdmin.
- 8:50–9:30: encerre resumindo as tecnologias utilizadas.

OBSERVAÇÕES
- O arquivo inicial é index.php, pois o processamento de sessão e a consulta ao banco exigem PHP. Ele é acessado pelo servidor HTTP em localhost, conforme o roteiro da avaliação.
- A logo SVG fornecida no projeto anterior foi preservada em assets/logo-autoeletrica.svg.
- O projeto foi preparado para ambiente local de desenvolvimento. Para publicação na internet, configure HTTPS, credenciais próprias, política de backup e controles adicionais de produção.
