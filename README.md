# Modo Caçador

Aplicação web de gestão do catálogo de uma loja de artigos de caça, desenvolvida para a unidade curricular de Programação Web.

Permite gerir artigos, as suas variantes (cor, tamanho, material e stock), a galeria de imagens e as categorias. O acesso é protegido por autenticação, e a gestão de utilizadores está reservada a administradores.

## Funcionalidades

- **Artigos:** lista com pesquisa, filtro por categoria, ordenação e paginação; detalhe com galeria; criar, editar e eliminar com validação no servidor.
- **Variantes:** uma por combinação de cor, tamanho e material, cada uma com o seu stock; não é possível criar variantes repetidas.
- **Imagens:** galeria por artigo; uma imagem pode estar associada a uma cor e é usada nas variantes dessa cor.
- **Categorias:** criar, renomear e eliminar (só categorias sem artigos).
- **Autenticação:** login, terminar sessão e troca obrigatória da palavra-passe temporária no primeiro acesso.
- **Utilizadores (admin):** criar, editar, desativar e reativar contas.
- **Perfil:** alterar nome, fotografia e palavra-passe.

## Tecnologias

- PHP 8.2, sem frameworks, com arquitetura MVC própria (router, controllers, models e views)
- MySQL / MariaDB através de `mysqli` com prepared statements
- Apache com `mod_rewrite` (XAMPP)
- HTML5, CSS e JavaScript próprios, sem bibliotecas (não usa Bootstrap)
- Google Fonts: Inter, Space Grotesk e Saira Stencil One

## Instalação

1. Instalar o [XAMPP](https://www.apachefriends.org/) e copiar o projeto para uma pasta local, por exemplo `C:/Projetos/pw/modocacador`.
2. Criar um alias no Apache, em `xampp/apache/conf/extra/httpd-autoindex.conf` (ou noutro ficheiro de configuração incluído):

   ```apache
   Alias /modocacador "C:/Projetos/pw/modocacador"
   <Directory "C:/Projetos/pw/modocacador">
       Options Indexes FollowSymLinks
       AllowOverride All
       Require all granted
   </Directory>
   ```

3. Arrancar o Apache e o MySQL no XAMPP Control Panel.
4. Importar a base de dados `huntingdb`, por esta ordem (no phpMyAdmin ou na linha de comandos):
   1. `db.sql` (estrutura das tabelas e utilizador inicial)
   2. `categorias_huntingdb.sql`
   3. `artigos_huntingdb.sql` (artigos, variantes e imagens)
5. Abrir <http://localhost/modocacador/>.

Se o projeto ficar noutro caminho, alterar a constante `URL_BASE` em `index.php`. Os dados de acesso à base de dados estão em `app/core/Db.php`.

### Primeiro acesso

| Utilizador | Palavra-passe |
|---|---|
| `admin` | `admin123` |

A aplicação obriga a definir uma nova palavra-passe no primeiro login.

## Estrutura

```
modocacador/
├── index.php            # ponto de entrada: sessão, autoload e router
├── .htaccess            # reencaminha os pedidos para index.php
├── db.sql, *_huntingdb.sql
├── app/
│   ├── core/            # App (router e controlo de acesso), Controller, Db, Sessao
│   ├── controllers/     # Artigo, Variante, Categoria, Auth, Perfil, Utilizador
│   ├── models/          # acesso à base de dados
│   ├── views/           # páginas, layout partilhado e partials
│   ├── helpers.php      # e(), euros(), iniciais(), icone()
│   └── load.php         # autoload das classes
└── assets/              # css, js e imagens
```

## Segurança

- Palavras-passe guardadas com `password_hash()` (bcrypt)
- Prepared statements em todas as consultas
- Token CSRF em todos os formulários POST
- Escape de todo o conteúdo mostrado (`htmlspecialchars`)
- Renovação do ID de sessão no login e cookie de sessão `HttpOnly` / `SameSite=Lax`
- Controlo de acesso centralizado antes de qualquer página

## Créditos das imagens

As imagens dos artigos vêm do Wikimedia Commons (licenças livres) e da loja online FRANKONIA (uso académico, não comercial). A lista completa, com autor, licença e fonte, está em [CREDITOS_IMAGENS.md](CREDITOS_IMAGENS.md).

Projeto académico, sem fins comerciais.
