<!doctype html>
<html lang="pt-PT">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php require 'app/views/partials/transicao.php'; ?>
  <title>Iniciar sessão — Modo Caçador</title>
  <link rel="icon" href="<?php echo $url_alias; ?>/assets/img/logo/logo_emblema.png">
  <?php require 'app/views/partials/fontes.php'; ?>
  <link rel="stylesheet" href="<?php echo $url_alias; ?>/assets/css/main.css?v=<?php echo filemtime('assets/css/main.css'); ?>">
</head>
<body>
<div class="login">

  <section class="login-marca" aria-label="Modo Caçador">
    <img class="login-foto" src="<?php echo $url_alias; ?>/assets/img/login/floresta.jpg" alt="">
    <div class="login-sombra" aria-hidden="true"></div>
    <div class="login-assinatura">
      <img src="<?php echo $url_alias; ?>/assets/img/logo/logo_emblema.png" alt="">
      <div>
        <h1><?php $wm_classe = 'wm-xl wm-animar'; include 'app/views/partials/nome.php'; ?></h1>
        <p class="login-subtitulo">Gestão do catálogo</p>
      </div>
    </div>
  </section>

  <main class="login-conteudo">
    <form class="login-form" action="<?php echo $url_alias; ?>/auth/login" method="POST" novalidate>
      <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
      <div>
        <h2>Iniciar sessão</h2>
        <p class="descricao">Introduza as suas credenciais para continuar.</p>
      </div>

      <?php if (!empty($data['erro'])) { ?>
        <div class="alerta alerta-erro" role="alert">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M12 7v6M12 16.5v.5"></path></svg>
          <span><?php echo htmlspecialchars($data['erro']); ?></span>
        </div>
      <?php } ?>

      <div class="campo">
        <label for="utilizador">Utilizador</label>
        <input class="input input-lg" id="utilizador" name="id_utilizador" type="text" autocomplete="username" required autofocus placeholder="ex.: admin" value="<?php echo htmlspecialchars($data['id_utilizador'] ?? ''); ?>">
      </div>

      <div class="campo">
        <label for="palavra-passe">Palavra-passe</label>
        <div class="campo-palavra-passe">
          <input class="input input-lg" id="palavra-passe" name="palavra_passe" type="password" autocomplete="current-password" required>
          <button type="button" class="mostrar-pp" data-mostrar="palavra-passe" aria-pressed="false" aria-label="Mostrar palavra-passe">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            <span>Mostrar</span>
          </button>
        </div>
      </div>

      <button type="submit" class="btn btn-primario btn-bloco btn-lg">Entrar</button>

      <p class="nota">Esqueceu-se da palavra-passe? Peça a um administrador para a redefinir.</p>
    </form>
  </main>
</div>
<script src="<?php echo $url_alias; ?>/assets/js/main.js?v=<?php echo filemtime('assets/js/main.js'); ?>"></script>
</body>
</html>
