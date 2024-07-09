# AniBook: Compartilhamento de Informações sobre Animes

![anibook_print_desktop](https://github.com/diogomasc/Anibook/assets/164716668/c9655c88-05cc-4d2f-b562-c907e25bdf3e)

AniBook é uma plataforma PHP que permite aos fãs de animes descobrir, avaliar e discutir seus favoritos, além de compartilhar suas próprias críticas e avaliações.. Desenvolvido como parte da disciplina de Desenvolvimento Web, o site utiliza Bootstrap e JavaScript para uma interface interativa. O banco de dados MySQL é usado para armazenar informações essenciais, como trailers, sinopses e avaliações.

## Tecnologias Utilizadas

- Front-end: Bootstrap, JavaScript
- Back-end: PHP
- Banco de Dados: MySQL
- Ferramentas: XAMPP para ambiente de desenvolvimento local, PHPMyAdmin para gerenciamento do banco de dados

## Recursos Adicionais

O site é responsivo, adaptando-se a diferentes dispositivos para uma experiência de usuário consistente em qualquer tela. Implementa também o envio de e-mails utilizando PHPMailer, oferecendo notificações e confirmações automatizadas aos usuários.

![anibook_print_mobile](https://github.com/diogomasc/Anibook/assets/164716668/368bb051-2968-4bae-a87a-466992fd98bb)

## Arquitetura MVC (Model-View-Controller)

O AniBook adota o padrão MVC para estruturar sua aplicação web de forma organizada e escalável. Este padrão separa o código em três componentes principais:

- **Model**: Responsável pela manipulação dos dados, interagindo diretamente com o banco de dados MySQL através das classes como `MovieDAO.php`, `ReviewDAO.php` e `UserDAO.php`, que representam os modelos de dados para Movie, Review e User, respectivamente.
- **View**: Camada de apresentação que exibe os dados ao usuário final de forma dinâmica e responsiva utilizando HTML, CSS com Bootstrap, e JavaScript para interatividade na interface.
- **Controller**: Controla o fluxo da aplicação, recebendo requisições do usuário, processando as entradas, consultando o modelo apropriado e selecionando a visão correta para retornar ao usuário.

Esta abordagem facilita a manutenção do código, separando preocupações e permitindo o desenvolvimento paralelo entre equipes front-end e back-end. O uso do padrão MVC no AniBook promove uma estrutura clara e modular, facilitando a expansão e a adição de novos recursos ao longo do desenvolvimento.

## Pré-requisitos

1. **Instalação do XAMPP**: Certifique-se de ter o XAMPP instalado no seu sistema. Você pode baixá-lo em [https://www.apachefriends.org/index.html](https://www.apachefriends.org/index.html).
   
2. **Configuração do PHPMyAdmin**: O PHPMyAdmin geralmente está incluído no XAMPP e pode ser acessado pelo navegador após iniciar o Apache e o MySQL no painel de controle do XAMPP.

## Passos para Criar o Banco de Dados

1. **Inicie o XAMPP**:
   - Abra o painel de controle do XAMPP.
   - Inicie os módulos do Apache e MySQL.

2. **Acesse o PHPMyAdmin**:
   - Abra o seu navegador e digite `http://localhost/phpmyadmin`.
   - Isso abrirá a interface do PHPMyAdmin onde você poderá gerenciar seu banco de dados.

3. **Criação do Banco de Dados**:

- Na interface do PHPMyAdmin, clique em "Novo" no menu à esquerda para criar um novo banco de dados.
- Digite o nome "Anibook" na caixa de texto e clique em "Criar" para criar o banco de dados.
- Após criar o banco de dados "Anibook", clique na aba "SQL" no topo.
- Cole o seguinte código SQL na caixa de consulta e clique em "Executar" para criar as tabelas users, movies e reviews dentro do banco de dados "Anibook":

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50),
    last_name VARCHAR(50),
    email VARCHAR(200),
    password VARCHAR(200),
    image VARCHAR(200),
    token VARCHAR(200),
    bio TEXT
);

CREATE TABLE movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100),
    description TEXT,
    image VARCHAR(200),
    trailer VARCHAR(150),
    category VARCHAR(50),
    length VARCHAR(50),
    users_id INT,
    ano INT,
    FOREIGN KEY (users_id) REFERENCES users(id)
);

CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rating INT,
    review TEXT,
    users_id INT,
    movies_id INT,
    FOREIGN KEY (users_id) REFERENCES users(id),
    FOREIGN KEY (movies_id) REFERENCES movies(id)
);
```

## Configuração Adicional para Manipulação de Imagens

Para permitir a manipulação de imagens (como redimensionamento) no PHP, é necessário garantir que a extensão GD esteja habilitada no PHP. No XAMPP, essa extensão geralmente vem descomentada por padrão, mas se não estiver, você pode ativá-la seguindo estes passos:

1. **Localize o arquivo `php.ini`**:
   - No XAMPP, vá para o diretório de instalação do PHP. Por exemplo, `C:\xampp\php`.
   - Procure pelo arquivo `php.ini`.

2. **Descomente a Extensão GD**:
   - Abra o arquivo `php.ini` em um editor de texto.
   - Procure pela linha `;extension=gd` (pode variar dependendo da versão).
   - Remova o ponto e vírgula (`;`) no início da linha para descomentar a extensão.
   - Salve o arquivo `php.ini` e reinicie o Apache pelo painel de controle do XAMPP para aplicar as alterações.

## Movendo ou Copiando o Repositório para a Pasta `htdocs`

1. **Clone o Repositório**:
   - Navegue até a pasta `htdocs` do XAMPP. Geralmente está localizada em `C:\xampp\htdocs` no Windows.
   - Clone o repositório AniBook na pasta `htdocs` usando o comando:

```sh
git clone https://github.com/diogomasc/Anibook.git
```

2. **Acesse o Projeto no Navegador**:
   - Abra o seu navegador e digite `http://localhost/Anibook`.
   - Isso abrirá a interface do AniBook.

## Testando o Banco de Dados

Após criar as tabelas no PHPMyAdmin, você pode começar a testar o seu projeto integrado com essas tabelas. Certifique-se de configurar corretamente a conexão com o banco de dados no seu código PHP para que ele possa interagir com as tabelas `users`, `movies` e `reviews` conforme necessário.
