<?php
$p = $data['perfil'];
$erros = $data['erros'];
$invalido = function ($campo, $ajuda = null) use ($erros) {
  if (isset($erros[$campo])) {
    return 'aria-invalid="true" aria-describedby="' . $campo . '-erro"';
  }
  return $ajuda ? 'aria-describedby="' . $ajuda . '"' : '';
};
$erro = function ($campo) use ($erros) {
  return isset($erros[$campo]) ? '<span class="campo-erro" id="' . $campo . '-erro">' . icone('erro', 14) . ' ' . e($erros[$campo]) . '</span>' : '';
};
?>
<header class="cabecalho">
  <div>
    <h1>O meu perfil</h1>
    <p>Os seus dados e a palavra-passe de acesso.</p>
  </div>
</header>

<?php if ($data['obrigatorio']) { ?>
  <div class="alerta alerta-erro" role="alert">
    <?php echo icone('erro', 20); ?>
    <span><strong>Defina uma nova palavra-passe para continuar.</strong> A palavra-passe atual é temporária.</span>
  </div>
<?php } ?>

<?php if (!$data['obrigatorio']) { ?>
<form class="cartao cartao-corpo" action="<?php echo $url_alias; ?>/perfil/dados" method="POST" enctype="multipart/form-data" aria-labelledby="dados-titulo">
  <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
  <h2 id="dados-titulo">Dados pessoais</h2>

  <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 20px">
    <span class="avatar avatar-lg" aria-hidden="true">
      <?php if (!empty($p['fotografia'])) { ?><img src="<?php echo e($p['fotografia']); ?>" alt=""><?php } else { echo e(iniciais($p['nome'])); } ?>
    </span>
    <div class="campo" style="flex: 1 1 260px">
      <label for="fotografia">Fotografia <span class="opcional">(opcional)</span></label>
      <input class="input" id="fotografia" name="fotografia" type="file" accept="image/jpeg,image/png,image/webp" style="padding-top: 9px" <?php echo $invalido('fotografia', 'foto-ajuda'); ?>>
      <?php echo $erro('fotografia') ?: '<span class="campo-ajuda" id="foto-ajuda">JPG, PNG ou WebP, até 2 MB. Sem fotografia, mostramos as suas iniciais.</span>'; ?>
      <?php if (!empty($p['fotografia'])) { ?>
        <label class="checkbox"><input type="checkbox" name="remover_fotografia" value="1"> Remover a fotografia atual</label>
      <?php } ?>
    </div>
  </div>

  <div class="grelha-campos">
    <div class="campo">
      <label for="nome">Nome</label>
      <input class="input" id="nome" name="nome" type="text" required maxlength="400" autocomplete="name" value="<?php echo e($p['nome']); ?>" <?php echo $invalido('nome'); ?>>
      <?php echo $erro('nome'); ?>
    </div>
    <div class="campo">
      <label for="id">Utilizador</label>
      <input class="input" id="id" type="text" value="<?php echo e($p['id_utilizador']); ?>" readonly aria-describedby="id-ajuda">
      <span class="campo-ajuda" id="id-ajuda">Não pode ser alterado.</span>
    </div>
  </div>

  <div class="barra-acoes">
    <button type="submit" class="btn btn-primario">Guardar dados</button>
  </div>
</form>
<?php } ?>

<form class="cartao cartao-corpo" action="<?php echo $url_alias; ?>/perfil/palavraPasse" method="POST" aria-labelledby="pp-titulo" novalidate>
  <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
  <input type="text" name="utilizador" value="<?php echo e($p['id_utilizador']); ?>" autocomplete="username" hidden>
  <div>
    <h2 id="pp-titulo">Alterar palavra-passe</h2>
    <p class="secundario">Por segurança, confirme primeiro a palavra-passe atual.</p>
  </div>

  <div class="campo" style="max-width: 420px">
    <label for="atual">Palavra-passe atual</label>
    <input class="input" id="atual" name="atual" type="password" required autocomplete="current-password" <?php echo $data['obrigatorio'] && empty($erros) ? 'autofocus' : ''; ?> <?php echo $invalido('atual'); ?>>
    <?php echo $erro('atual'); ?>
  </div>

  <div class="grelha-campos">
    <div class="campo">
      <label for="nova">Nova palavra-passe</label>
      <input class="input" id="nova" name="nova" type="password" required minlength="8" autocomplete="new-password" <?php echo $invalido('nova', 'nova-ajuda'); ?>>
      <?php echo $erro('nova') ?: '<span class="campo-ajuda" id="nova-ajuda">Pelo menos 8 caracteres, com letras e números, diferente da atual.</span>'; ?>
    </div>
    <div class="campo">
      <label for="confirmar">Confirmar nova palavra-passe</label>
      <input class="input" id="confirmar" name="confirmar" type="password" required autocomplete="new-password" <?php echo $invalido('confirmar'); ?>>
      <?php echo $erro('confirmar'); ?>
    </div>
  </div>

  <div class="barra-acoes">
    <button type="submit" class="btn btn-primario">Alterar palavra-passe</button>
  </div>
</form>
