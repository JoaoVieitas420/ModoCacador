<?php
$editarId = $data['editar_id'];
$erroCriar = $data['erro'] && !$editarId;
$erroEditar = $data['erro'] && $editarId;
?>
<header class="cabecalho">
  <div>
    <h1>Categorias</h1>
    <p>Organize os artigos do catálogo. Só é possível eliminar categorias sem artigos.</p>
  </div>
</header>

<form class="cartao cartao-corpo" action="<?php echo $url_alias; ?>/categoria/create" method="POST" aria-labelledby="nova-titulo" style="gap: 12px">
  <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
  <h2 id="nova-titulo" style="font-size: 18px">Nova categoria</h2>
  <div class="grelha-campos" style="align-items: flex-end">
    <div class="campo" style="flex-grow: 3">
      <label for="nova-cat">Nome</label>
      <input class="input" id="nova-cat" name="categoria" type="text" required maxlength="255" placeholder="Ex.: Iscos e Atrativos"
        value="<?php echo $erroCriar ? e($data['valor']) : ''; ?>" <?php echo $erroCriar ? 'aria-invalid="true" aria-describedby="nova-cat-erro" autofocus' : ''; ?>>
      <?php if ($erroCriar) { ?><span class="campo-erro" id="nova-cat-erro"><?php echo icone('erro', 14); ?> <?php echo $data['erro']; ?></span><?php } ?>
    </div>
    <button type="submit" class="btn btn-primario" style="flex: 0 0 auto"><?php echo icone('mais', 16); ?> Criar categoria</button>
  </div>
</form>

<section class="cartao" aria-label="Lista de categorias">
  <div class="tabela-caixa">
    <table class="tabela" style="min-width: 560px">
      <thead>
        <tr>
          <th scope="col">Nome</th>
          <th scope="col">Artigos</th>
          <th scope="col"><span class="sr-only">Ações</span></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($data['categorias'] as $c) { ?>
          <?php if ((int) $c['id'] === $editarId) { ?>
            <tr style="background: #F8FBF8">
              <td colspan="3">
                <form action="<?php echo $url_alias; ?>/categoria/update/<?php echo $c['id']; ?>" method="POST" style="display: flex; flex-wrap: wrap; gap: 8px; align-items: flex-start">
                  <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                  <div class="campo" style="flex: 1 1 240px">
                    <label for="editar-cat" class="sr-only">Novo nome da categoria <?php echo e($c['categoria']); ?></label>
                    <input class="input" id="editar-cat" name="categoria" type="text" required maxlength="255" value="<?php echo e($data['valor']); ?>" autofocus
                      <?php echo $erroEditar ? 'aria-invalid="true" aria-describedby="editar-cat-erro"' : ''; ?>>
                    <?php if ($erroEditar) { ?><span class="campo-erro" id="editar-cat-erro"><?php echo icone('erro', 14); ?> <?php echo $data['erro']; ?></span><?php } ?>
                  </div>
                  <a class="btn btn-secundario" href="<?php echo $url_alias; ?>/categoria">Cancelar</a>
                  <button type="submit" class="btn btn-primario">Guardar</button>
                </form>
              </td>
            </tr>
          <?php } else { ?>
            <tr>
              <td><strong><?php echo e($c['categoria']); ?></strong></td>
              <td>
                <?php if ((int) $c['artigos'] > 0) { ?>
                  <a href="<?php echo $url_alias; ?>/artigo?categoria=<?php echo $c['id']; ?>"><?php echo $c['artigos']; ?> <?php echo (int) $c['artigos'] === 1 ? 'artigo' : 'artigos'; ?></a>
                <?php } else { ?>
                  <span class="secundario">Sem artigos</span>
                <?php } ?>
              </td>
              <td>
                <div class="acoes">
                  <a class="btn-icone" href="<?php echo $url_alias; ?>/categoria/update/<?php echo $c['id']; ?>"><?php echo icone('editar', 16); ?> Renomear<span class="sr-only"> <?php echo e($c['categoria']); ?></span></a>
                  <?php if ((int) $c['artigos'] === 0) { ?>
                    <button type="button" class="btn-icone perigo" aria-label="Eliminar <?php echo e($c['categoria']); ?>"
                      data-eliminar="<?php echo $url_alias; ?>/categoria/delete/<?php echo $c['id']; ?>"
                      data-titulo="Eliminar esta categoria?"
                      data-item="<?php echo e($c['categoria']); ?>"
                      data-consequencia=""
                      data-confirmar="Eliminar categoria"><?php echo icone('eliminar'); ?></button>
                  <?php } else { ?>
                    <button type="button" class="btn-icone" disabled title="Tem <?php echo $c['artigos']; ?> artigos. Mova-os para outra categoria primeiro." aria-label="Eliminar <?php echo e($c['categoria']); ?> — indisponível, tem <?php echo $c['artigos']; ?> artigos"><?php echo icone('eliminar'); ?></button>
                  <?php } ?>
                </div>
              </td>
            </tr>
          <?php } ?>
        <?php } ?>
      </tbody>
    </table>
  </div>
</section>
