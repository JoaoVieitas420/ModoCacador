<?php
$erros = $data['erros'];
$v = $data['valores'];
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
    <h1>Utilizadores</h1>
    <p>Quem pode entrar na gestão do catálogo. As contas são desativadas em vez de eliminadas, para manter o histórico.</p>
  </div>
</header>

<div style="display: flex; flex-wrap: wrap; gap: 24px; align-items: flex-start">
  <section class="cartao" aria-label="Lista de utilizadores" style="flex: 2 1 520px; min-width: 0">
    <div class="tabela-caixa">
      <table class="tabela" style="min-width: 560px">
        <thead>
          <tr>
            <th scope="col">Nome</th>
            <th scope="col">Estado</th>
            <th scope="col"><span class="sr-only">Ações</span></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($data['utilizadores'] as $u) {
            $eu = $u['id_utilizador'] === $utilizador['id'];
            $idUrl = rawurlencode($u['id_utilizador']); ?>
            <tr>
              <td>
                <div class="artigo-celula">
                  <span class="avatar avatar-md" aria-hidden="true">
                    <?php if (!empty($u['fotografia'])) { ?><img src="<?php echo e($u['fotografia']); ?>" alt=""><?php } else { echo e(iniciais($u['nome'])); } ?>
                  </span>
                  <div>
                    <strong><?php echo e($u['nome']); ?></strong><?php echo $eu ? ' <span class="secundario">(você)</span>' : ''; ?>
                    <div class="secundario"><?php echo e($u['id_utilizador']); ?><?php echo (int) $u['admin'] === 1 ? ' · Administrador' : ''; ?></div>
                  </div>
                </div>
              </td>
              <td><span class="estado <?php echo (int) $u['ativo'] === 1 ? 'estado-ativo' : 'estado-inativo'; ?>"><?php echo (int) $u['ativo'] === 1 ? 'Ativo' : 'Inativo'; ?></span></td>
              <td>
                <div class="acoes">
                  <a class="btn-icone" href="<?php echo $url_alias; ?>/utilizador/update/<?php echo $idUrl; ?>"><?php echo icone('editar', 16); ?> Editar<span class="sr-only"> <?php echo e($u['nome']); ?></span></a>
                  <?php if ((int) $u['ativo'] === 1) { ?>
                    <form action="<?php echo $url_alias; ?>/utilizador/desativar/<?php echo $idUrl; ?>" method="POST">
                      <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                      <button type="submit" class="btn-icone perigo" <?php echo $eu ? 'disabled title="Não pode desativar a sua própria conta."' : ''; ?>>Desativar<span class="sr-only"> <?php echo e($u['nome']); ?></span></button>
                    </form>
                  <?php } else { ?>
                    <form action="<?php echo $url_alias; ?>/utilizador/reativar/<?php echo $idUrl; ?>" method="POST">
                      <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                      <button type="submit" class="btn-icone">Reativar<span class="sr-only"> <?php echo e($u['nome']); ?></span></button>
                    </form>
                  <?php } ?>
                </div>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </section>

  <form class="cartao cartao-corpo" action="<?php echo $url_alias; ?>/utilizador/create" method="POST" aria-labelledby="novo-titulo" style="flex: 1 1 320px; gap: 16px" novalidate>
    <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
    <div>
      <h2 id="novo-titulo">Novo utilizador</h2>
      <p class="secundario">A pessoa altera a palavra-passe no primeiro acesso.</p>
    </div>
    <div class="campo">
      <label for="nu-nome">Nome</label>
      <input class="input" id="nu-nome" name="nome" type="text" required maxlength="400" autocomplete="off" value="<?php echo e($v['nome'] ?? ''); ?>" <?php echo $invalido('nome'); ?>>
      <?php echo $erro('nome'); ?>
    </div>
    <div class="campo">
      <label for="nu-id">Utilizador</label>
      <input class="input" id="nu-id" name="id_utilizador" type="text" required maxlength="50" autocomplete="off" value="<?php echo e($v['id_utilizador'] ?? ''); ?>" <?php echo $invalido('id_utilizador', 'nu-id-ajuda'); ?>>
      <?php echo $erro('id_utilizador') ?: '<span class="campo-ajuda" id="nu-id-ajuda">Usado para iniciar sessão. Não pode ser alterado depois.</span>'; ?>
    </div>
    <div class="campo">
      <label for="nu-pass">Palavra-passe temporária</label>
      <input class="input" id="nu-pass" name="palavra_passe" type="password" required minlength="8" autocomplete="new-password" <?php echo $invalido('palavra_passe', 'nu-pass-ajuda'); ?>>
      <?php echo $erro('palavra_passe') ?: '<span class="campo-ajuda" id="nu-pass-ajuda">Mínimo de 8 caracteres.</span>'; ?>
    </div>
    <label class="checkbox"><input type="checkbox" name="ativo" value="1" <?php echo !isset($v['ativo']) || $v['ativo'] ? 'checked' : ''; ?>> Conta ativa</label>
    <label class="checkbox"><input type="checkbox" name="admin" value="1" <?php echo !empty($v['admin']) ? 'checked' : ''; ?>> Administrador (pode gerir utilizadores)</label>
    <button type="submit" class="btn btn-primario">Criar utilizador</button>
  </form>
</div>
