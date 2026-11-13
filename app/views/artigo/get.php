<?php
$a = $data['artigo'];
$imagens = $data['imagens'];
$variantes = $data['variantes'];
$stockTotal = array_sum(array_column($variantes, 'stock'));
$cores = array_unique(array_filter(array_column($variantes, 'cor')));
// imagem de cada variante: a da sua cor, senão a imagem principal do artigo
$imagemDaCor = function ($cor) use ($imagens, $a) {
  foreach ($imagens as $im) {
    if ($im['cor'] !== null && $im['cor'] === $cor) {
      return $im['caminho'];
    }
  }
  return $a['imagem'];
};
?>
<nav class="migalhas" aria-label="Caminho">
  <a href="<?php echo $url_alias; ?>/artigo">Artigos</a> <?php echo icone('seta-dir', 14); ?> <span aria-current="page"><?php echo e($a['nome']); ?></span>
</nav>

<header class="cabecalho">
  <div>
    <h1><?php echo e($a['nome']); ?></h1>
    <p><?php if ($data['categoria']) { ?><span class="chip"><?php echo e($data['categoria']['categoria']); ?></span> <?php } ?>#<?php echo $a['id']; ?> · Modelo <?php echo e($a['ano']); ?></p>
  </div>
  <div class="barra-acoes">
    <a class="btn btn-primario" href="<?php echo $url_alias; ?>/artigo/update/<?php echo $a['id']; ?>"><?php echo icone('editar', 16); ?> Editar artigo</a>
    <button type="button" class="btn btn-perigo-contorno"
      data-eliminar="<?php echo $url_alias; ?>/artigo/delete/<?php echo $a['id']; ?>"
      data-titulo="Eliminar este artigo?"
      data-item="<?php echo e($a['nome']); ?>"
      data-consequencia="<?php echo count($variantes) || count($imagens) ? 'Também serão eliminadas ' . count($variantes) . ' variantes e ' . count($imagens) . ' imagens.' : ''; ?>"
      data-confirmar="Eliminar artigo"><?php echo icone('eliminar', 16); ?> Eliminar</button>
  </div>
</header>

<div class="detalhe-grelha">
  <section class="cartao galeria" aria-label="Galeria">
    <?php if (empty($imagens)) { ?>
      <div class="sem-imagem">Sem imagens</div>
    <?php } else { ?>
      <img class="galeria-principal" id="galeria-principal" src="<?php echo e($imagens[0]['caminho']); ?>" alt="<?php echo e($a['nome']); ?>">
      <?php if (count($imagens) > 1) { ?>
        <div class="galeria-miniaturas">
          <?php foreach ($imagens as $i => $im) { ?>
            <button type="button" data-galeria="<?php echo e($im['caminho']); ?>" aria-pressed="<?php echo $i === 0 ? 'true' : 'false'; ?>" aria-label="Ver imagem <?php echo $i + 1; ?><?php echo $im['cor'] ? ' (' . e($im['cor']) . ')' : ''; ?>">
              <img src="<?php echo e($im['caminho']); ?>" alt="">
            </button>
          <?php } ?>
        </div>
      <?php } ?>
    <?php } ?>
  </section>

  <section class="cartao cartao-corpo" aria-labelledby="ficha-titulo">
    <h2 id="ficha-titulo">Ficha do artigo</h2>
    <dl class="ficha">
      <div><dt>Preço</dt><dd class="destaque"><?php echo euros($a['preco']); ?></dd></div>
      <div><dt>Avaliação</dt><dd class="destaque estrela"><?php echo icone('estrela', 22); ?> <?php echo e(str_replace('.', ',', $a['avaliacao'])); ?> <span class="secundario">/ 5</span></dd></div>
      <div><dt>Categoria</dt><dd><?php if ($data['categoria']) { ?><a href="<?php echo $url_alias; ?>/artigo?categoria=<?php echo $data['categoria']['id']; ?>"><?php echo e($data['categoria']['categoria']); ?></a><?php } ?></dd></div>
      <div><dt>Ano do modelo</dt><dd><?php echo e($a['ano']); ?></dd></div>
      <div><dt>Variantes</dt><dd><?php echo count($variantes); ?><?php echo $cores ? ' (' . count($cores) . ' ' . (count($cores) === 1 ? 'cor' : 'cores') . ')' : ''; ?></dd></div>
      <div><dt>Stock total</dt><dd><?php echo $stockTotal; ?> unidades</dd></div>
    </dl>
  </section>
</div>

<section class="cartao" aria-labelledby="variantes-titulo">
  <div class="cartao-cabecalho">
    <div>
      <h2 id="variantes-titulo">Variantes</h2>
      <p>Combinações de cor, tamanho e material, cada uma com o seu stock.</p>
    </div>
  </div>
  <?php if (empty($variantes)) { ?>
    <p class="vazio">Este artigo ainda não tem variantes.</p>
  <?php } else { ?>
  <div class="tabela-caixa">
    <table class="tabela" style="min-width: 640px">
      <thead>
        <tr>
          <th scope="col">Cor</th>
          <th scope="col">Tamanho</th>
          <th scope="col">Material</th>
          <th scope="col" class="num">Stock</th>
          <th scope="col"><span class="sr-only">Ações</span></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($variantes as $v) {
          $descricao = implode(' / ', array_filter([$v['cor'], $v['tamanho'], $v['material']])); ?>
          <tr>
            <td>
              <span class="artigo-celula">
                <?php $img = $imagemDaCor($v['cor']); if ($img) { ?><img class="miniatura miniatura-sm" src="<?php echo e($img); ?>" alt=""><?php } ?>
                <?php echo e($v['cor'] ?? '—'); ?>
              </span>
            </td>
            <td><strong><?php echo e($v['tamanho'] ?? '—'); ?></strong></td>
            <td><?php echo e($v['material'] ?? '—'); ?></td>
            <td class="num"><?php echo (int) $v['stock'] <= 5 ? '<span class="stock-baixo">' . (int) $v['stock'] . ' · baixo</span>' : (int) $v['stock']; ?></td>
            <td>
              <div class="acoes">
                <a class="btn-icone" href="<?php echo $url_alias; ?>/variante/update/<?php echo $v['id']; ?>" aria-label="Editar variante <?php echo e($descricao); ?>"><?php echo icone('editar'); ?></a>
                <button type="button" class="btn-icone perigo" aria-label="Eliminar variante <?php echo e($descricao); ?>"
                  data-eliminar="<?php echo $url_alias; ?>/variante/delete/<?php echo $v['id']; ?>"
                  data-titulo="Eliminar esta variante?"
                  data-item="<?php echo e($a['nome'] . ' — ' . $descricao); ?>"
                  data-consequencia="<?php echo (int) $v['stock']; ?> unidades em stock deixam de estar registadas."
                  data-confirmar="Eliminar variante"><?php echo icone('eliminar'); ?></button>
              </div>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
  <?php } ?>

  <form class="form-linha" aria-label="Nova variante" action="<?php echo $url_alias; ?>/variante/create/<?php echo $a['id']; ?>" method="POST">
    <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
    <div class="campo">
      <label for="nv-cor">Cor</label>
      <input class="input" id="nv-cor" name="cor" type="text" maxlength="100">
    </div>
    <div class="campo">
      <label for="nv-tam">Tamanho</label>
      <input class="input" id="nv-tam" name="tamanho" type="text" maxlength="50">
    </div>
    <div class="campo">
      <label for="nv-mat">Material <span class="opcional">(opcional)</span></label>
      <input class="input" id="nv-mat" name="material" type="text" maxlength="100">
    </div>
    <div class="campo" style="flex: 0 1 110px">
      <label for="nv-stock">Stock</label>
      <input class="input" id="nv-stock" name="stock" type="number" min="0" value="0" required>
    </div>
    <button type="submit" class="btn btn-primario"><?php echo icone('mais', 16); ?> Adicionar variante</button>
  </form>
</section>
