<?php
use app\core\Sessao;
$secao = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'))[1] ?? '';
$flash = Sessao::obterFlash();
$wm_animar = Sessao::animarLogo() ? 'wm-animar' : '';
?>
<!doctype html>
<html lang="pt-PT">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo e($titulo ?? 'Modo Caçador'); ?> — Modo Caçador</title>
  <link rel="icon" href="<?php echo $url_alias; ?>/assets/img/logo/logo_emblema.png">
  <?php require 'app/views/partials/fontes.php'; ?>
  <link rel="stylesheet" href="<?php echo $url_alias; ?>/assets/css/main.css?v=<?php echo filemtime('assets/css/main.css'); ?>">
</head>
<body>
<div class="app">
  <!-- barra do topo (só em ecrãs estreitos): botão do menu lateral + logo -->
  <header class="barra-movel">
    <button type="button" class="menu-botao" id="menu-abrir" aria-controls="menu" aria-expanded="false" aria-label="Abrir menu"><?php echo icone('menu', 24); ?></button>
    <a href="<?php echo $url_alias; ?>/artigo" class="brand" aria-label="Modo Caçador — página inicial">
      <img src="<?php echo $url_alias; ?>/assets/img/logo/logo_emblema.png" alt="">
      <?php $wm_classe = $wm_animar; include 'app/views/partials/nome.php'; ?>
    </a>
  </header>
  <div class="menu-fundo" id="menu-fundo" hidden></div>

  <nav class="menu" id="menu" aria-label="Navegação principal">
    <div class="menu-topo">
      <a href="<?php echo $url_alias; ?>/artigo" class="brand" aria-label="Modo Caçador — página inicial">
        <img src="<?php echo $url_alias; ?>/assets/img/logo/logo_emblema.png" alt="">
        <?php $wm_classe = $wm_animar; include 'app/views/partials/nome.php'; ?>
      </a>
      <button type="button" class="menu-botao menu-fechar" id="menu-fechar" aria-label="Fechar menu"><?php echo icone('fechar', 24); ?></button>
    </div>
    <ul class="menu-lista">
      <li><a class="menu-item" href="<?php echo $url_alias; ?>/artigo" <?php echo in_array($secao, ['artigo', 'variante']) ? 'aria-current="page"' : ''; ?>><?php echo icone('artigos', 20); ?> Artigos</a></li>
      <li><a class="menu-item" href="<?php echo $url_alias; ?>/categoria" <?php echo $secao === 'categoria' ? 'aria-current="page"' : ''; ?>><?php echo icone('categorias', 20); ?> Categorias</a></li>
      <?php if (Sessao::eAdmin()) { ?>
        <li><a class="menu-item" href="<?php echo $url_alias; ?>/utilizador" <?php echo $secao === 'utilizador' ? 'aria-current="page"' : ''; ?>><?php echo icone('utilizadores', 20); ?> Utilizadores</a></li>
      <?php } ?>
    </ul>
    <div class="menu-rodape">
      <a class="menu-item menu-perfil" href="<?php echo $url_alias; ?>/perfil" <?php echo $secao === 'perfil' ? 'aria-current="page"' : ''; ?>>
        <span class="avatar" aria-hidden="true">
          <?php if (!empty($utilizador['fotografia'])) { ?><img src="<?php echo e($utilizador['fotografia']); ?>" alt=""><?php } else { echo e(iniciais($utilizador['nome'])); } ?>
        </span>
        <span><strong><?php echo e($utilizador['nome']); ?></strong><small>O meu perfil</small></span>
      </a>
      <form action="<?php echo $url_alias; ?>/auth/logout" method="POST">
        <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
        <button type="submit" class="menu-item"><?php echo icone('sair', 20); ?> Terminar sessão</button>
      </form>
    </div>
  </nav>

  <main class="conteudo <?php echo $classe_conteudo ?? ''; ?>">
    <?php if ($flash) { ?>
      <div class="alerta alerta-<?php echo $flash['tipo'] === 'erro' ? 'erro' : 'sucesso'; ?>" role="<?php echo $flash['tipo'] === 'erro' ? 'alert' : 'status'; ?>">
        <?php echo icone($flash['tipo'] === 'erro' ? 'erro' : 'sucesso', 20); ?>
        <span><?php echo $flash['mensagem']; ?></span>
      </div>
    <?php } ?>
