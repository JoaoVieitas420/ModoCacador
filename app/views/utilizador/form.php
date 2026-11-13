<?php
$u = $data['alvo'];
$erros = $data['erros'];
$erro = function ($campo) use ($erros) {
  return isset($erros[$campo]) ? '<span class="campo-erro" id="' . $campo . '-erro">' . icone('erro', 14) . ' ' . e($erros[$campo]) . '</span>' : '';
};
?>
<nav class="migalhas" aria-label="Caminho">
  <a href="<?php echo $url_alias; ?>/utilizador">Utilizadores</a> <?php echo icone('seta-dir', 14); ?> <span aria-current="page"><?php echo e($u['nome']); ?></span>
</nav>

<header class="cabecalho">
  <div>
    <h1>Editar utilizador</h1>
    <p><?php echo e($u['id_utilizador']); ?><?php echo $data['eu'] ? ' (você)' : ''; ?></p>
  </div>
</header>

<form class="cartao cartao-corpo" action="<?php echo $url_alias; ?>/utilizador/update/<?php echo rawurlencode($u['id_utilizador']); ?>" method="POST" novalidate>
  <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
  <input type="text" name="utilizador" value="<?php echo e($u['id_utilizador']); ?>" autocomplete="username" hidden>
  <div class="campo">
    <label for="nome">Nome</label>
    <input class="input" id="nome" name="nome" type="text" required maxlength="400" value="<?php echo e($u['nome']); ?>" <?php echo isset($erros['nome']) ? 'aria-invalid="true" aria-describedby="nome-erro"' : ''; ?>>
    <?php echo $erro('nome'); ?>
  </div>
  <div>
    <label class="checkbox"><input type="checkbox" name="admin" value="1" <?php echo (int) $u['admin'] === 1 ? 'checked' : ''; ?>> Administrador (pode gerir utilizadores)</label>
    <?php echo $erro('admin'); ?>
  </div>
  <div class="campo">
    <label for="pp">Nova palavra-passe temporária <span class="opcional">(opcional)</span></label>
    <input class="input" id="pp" name="palavra_passe" type="password" minlength="8" autocomplete="new-password" <?php echo isset($erros['palavra_passe']) ? 'aria-invalid="true" aria-describedby="palavra_passe-erro"' : 'aria-describedby="pp-ajuda"'; ?>>
    <?php echo $erro('palavra_passe') ?: '<span class="campo-ajuda" id="pp-ajuda">Preencha só para redefinir a palavra-passe. A pessoa terá de a alterar no próximo acesso.</span>'; ?>
  </div>
  <div class="barra-acoes">
    <a class="btn btn-secundario" href="<?php echo $url_alias; ?>/utilizador">Cancelar</a>
    <button type="submit" class="btn btn-primario">Guardar alterações</button>
  </div>
</form>
