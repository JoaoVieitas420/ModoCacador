<?php
$f = $data['filtros'];
// link de uma página mantendo os filtros atuais
$linkPagina = function ($p) use ($url_alias, $f) {
  return $url_alias . '/artigo?' . http_build_query(['q' => $f['q'], 'categoria' => $f['categoria'] ?: null, 'ordem' => $f['ordem'], 'pagina' => $p]);
};
$inicio = $data['total'] ? ($data['pagina'] - 1) * $data['por_pagina'] + 1 : 0;
$fim = min($data['pagina'] * $data['por_pagina'], $data['total']);
?>
<header class="cabecalho">
  <div>
    <h1>Artigos</h1>
    <p><?php echo $data['total']; ?> <?php echo $data['total'] === 1 ? 'artigo' : 'artigos'; ?><?php echo ($f['q'] !== '' || $f['categoria']) ? ' encontrados' : ' no catálogo'; ?></p>
  </div>
  <a class="btn btn-primario" href="<?php echo $url_alias; ?>/artigo/create"><?php echo icone('mais'); ?> Novo artigo</a>
</header>

<form class="filtros" role="search" action="<?php echo $url_alias; ?>/artigo" method="GET">
  <div class="campo campo-pesquisa">
    <label for="pesquisa">Pesquisar</label>
    <input class="input" id="pesquisa" name="q" type="search" placeholder="Nome do artigo" value="<?php echo e($f['q']); ?>">
  </div>
  <div class="campo">
    <label for="filtro-categoria">Categoria</label>
    <select class="select" id="filtro-categoria" name="categoria">
      <option value="0">Todas as categorias</option>
      <?php foreach ($data['categorias'] as $c) { ?>
        <option value="<?php echo $c['id']; ?>" <?php echo (int) $c['id'] === $f['categoria'] ? 'selected' : ''; ?>><?php echo e($c['categoria']); ?></option>
      <?php } ?>
    </select>
  </div>
  <div class="campo">
    <label for="ordem">Ordenar por</label>
    <select class="select" id="ordem" name="ordem">
      <?php foreach (['nome' => 'Nome (A–Z)', 'preco' => 'Preço (menor primeiro)', 'preco_desc' => 'Preço (maior primeiro)', 'recentes' => 'Mais recentes'] as $valor => $rotulo) { ?>
        <option value="<?php echo $valor; ?>" <?php echo $f['ordem'] === $valor ? 'selected' : ''; ?>><?php echo $rotulo; ?></option>
      <?php } ?>
    </select>
  </div>
  <button type="submit" class="btn btn-secundario"><?php echo icone('pesquisa'); ?> Filtrar</button>
</form>

<section class="cartao" aria-label="Lista de artigos">
  <?php if (empty($data['artigos'])) { ?>
    <p class="vazio">Nenhum artigo corresponde à pesquisa. <a href="<?php echo $url_alias; ?>/artigo">Limpar filtros</a></p>
  <?php } else { ?>
  <div class="tabela-caixa">
    <table class="tabela" style="min-width: 820px">
      <thead>
        <tr>
          <th scope="col">Artigo</th>
          <th scope="col">Categoria</th>
          <th scope="col" class="num">Preço</th>
          <th scope="col">Avaliação</th>
          <th scope="col">Variantes</th>
          <th scope="col"><span class="sr-only">Ações</span></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($data['artigos'] as $a) { ?>
          <tr>
            <td>
              <div class="artigo-celula">
                <?php if (!empty($a['imagem'])) { ?>
                  <img class="miniatura" src="<?php echo e($a['imagem']); ?>" alt="">
                <?php } else { ?>
                  <span class="miniatura" aria-hidden="true"></span>
                <?php } ?>
                <div>
                  <a class="artigo-nome" href="<?php echo $url_alias; ?>/artigo/get/<?php echo $a['id']; ?>"><?php echo e($a['nome']); ?></a>
                  <div class="secundario">#<?php echo $a['id']; ?> · <?php echo e($a['ano']); ?></div>
                </div>
              </div>
            </td>
            <td><span class="chip"><?php echo e($a['categoria']); ?></span></td>
            <td class="num"><?php echo euros($a['preco']); ?></td>
            <td><span class="estrela"><?php echo icone('estrela', 16); ?> <?php echo e(str_replace('.', ',', $a['avaliacao'])); ?></span></td>
            <td>
              <?php if ((int) $a['nv'] === 0) { ?>
                <span class="secundario">Sem variantes</span>
              <?php } else { ?>
                <div><?php echo $a['nv']; ?> <?php echo (int) $a['nv'] === 1 ? 'variante' : 'variantes'; ?></div>
                <?php if ((int) $a['stock'] < 15) { ?>
                  <div class="aviso-texto">Stock baixo · <?php echo $a['stock']; ?> un.</div>
                <?php } else { ?>
                  <div class="secundario"><?php echo $a['stock']; ?> un. em stock</div>
                <?php } ?>
              <?php } ?>
            </td>
            <td>
              <div class="acoes">
                <a class="btn-icone" href="<?php echo $url_alias; ?>/artigo/update/<?php echo $a['id']; ?>"><?php echo icone('editar', 16); ?> Editar<span class="sr-only"> <?php echo e($a['nome']); ?></span></a>
                <button type="button" class="btn-icone perigo" aria-label="Eliminar <?php echo e($a['nome']); ?>"
                  data-eliminar="<?php echo $url_alias; ?>/artigo/delete/<?php echo $a['id']; ?>"
                  data-titulo="Eliminar este artigo?"
                  data-item="<?php echo e($a['nome']); ?>"
                  data-consequencia="<?php echo (int) $a['nv'] > 0 ? 'Também serão eliminadas as ' . $a['nv'] . ' variantes e as imagens do artigo.' : 'As imagens do artigo também serão eliminadas.'; ?>"
                  data-confirmar="Eliminar artigo"><?php echo icone('eliminar'); ?></button>
              </div>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
  <footer class="paginacao-rodape">
    <span>A mostrar <?php echo $inicio; ?>–<?php echo $fim; ?> de <?php echo $data['total']; ?></span>
    <?php if ($data['paginas'] > 1) { ?>
      <nav class="paginacao" aria-label="Paginação">
        <?php if ($data['pagina'] > 1) { ?>
          <a href="<?php echo e($linkPagina($data['pagina'] - 1)); ?>" aria-label="Página anterior"><?php echo icone('seta-esq'); ?></a>
        <?php } else { ?>
          <span class="desativado" aria-hidden="true"><?php echo icone('seta-esq'); ?></span>
        <?php } ?>
        <?php for ($p = 1; $p <= $data['paginas']; $p++) { ?>
          <a href="<?php echo e($linkPagina($p)); ?>" <?php echo $p === $data['pagina'] ? 'aria-current="page"' : ''; ?>><?php echo $p; ?></a>
        <?php } ?>
        <?php if ($data['pagina'] < $data['paginas']) { ?>
          <a href="<?php echo e($linkPagina($data['pagina'] + 1)); ?>" aria-label="Página seguinte"><?php echo icone('seta-dir'); ?></a>
        <?php } else { ?>
          <span class="desativado" aria-hidden="true"><?php echo icone('seta-dir'); ?></span>
        <?php } ?>
      </nav>
    <?php } ?>
  </footer>
  <?php } ?>
</section>
