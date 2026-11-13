<?php
$a = $data['artigo'];
$erros = $data['erros'];
$editar = !empty($a['id']);
$acao = $editar ? $url_alias . '/artigo/update/' . $a['id'] : $url_alias . '/artigo/create';
$rotulos = ['nome' => 'Nome', 'categoria_id' => 'Categoria', 'ano' => 'Ano do modelo', 'preco' => 'Preço', 'avaliacao' => 'Avaliação'];
$invalido = function ($campo) use ($erros) {
  return isset($erros[$campo]) ? 'aria-invalid="true" aria-describedby="' . $campo . '-erro"' : '';
};
$erro = function ($campo) use ($erros) {
  return isset($erros[$campo]) ? '<span class="campo-erro" id="' . $campo . '-erro">' . icone('erro', 14) . ' ' . e($erros[$campo]) . '</span>' : '';
};
?>
<nav class="migalhas" aria-label="Caminho">
  <a href="<?php echo $url_alias; ?>/artigo">Artigos</a> <?php echo icone('seta-dir', 14); ?>
  <?php if ($editar) { ?><a href="<?php echo $url_alias; ?>/artigo/get/<?php echo $a['id']; ?>"><?php echo e($a['nome']); ?></a> <?php echo icone('seta-dir', 14); ?> <span aria-current="page">Editar</span>
  <?php } else { ?><span aria-current="page">Novo artigo</span><?php } ?>
</nav>

<header class="cabecalho">
  <div>
    <h1><?php echo $editar ? 'Editar artigo' : 'Novo artigo'; ?></h1>
    <p>Os campos marcados com <span aria-hidden="true">*</span><span class="sr-only">asterisco</span> são obrigatórios.</p>
  </div>
</header>

<?php if (!empty($erros)) { ?>
  <div class="alerta alerta-erro" role="alert">
    <?php echo icone('erro', 20); ?>
    <div>
      <strong>Não foi possível guardar. Corrija <?php echo count($erros) === 1 ? '1 campo' : count($erros) . ' campos'; ?>:</strong>
      <?php foreach ($erros as $campo => $mensagem) { ?>
        <div><a href="#<?php echo $campo; ?>" style="color: inherit"><?php echo $rotulos[$campo]; ?> — <?php echo e($mensagem); ?></a></div>
      <?php } ?>
    </div>
  </div>
<?php } ?>

<form action="<?php echo $acao; ?>" method="POST" novalidate style="display: flex; flex-direction: column; gap: 24px">
  <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">

  <section class="cartao cartao-corpo" aria-labelledby="s-info">
    <h2 id="s-info">Informação</h2>
    <div class="campo">
      <label for="nome">Nome <span aria-hidden="true" style="color: var(--perigo)">*</span></label>
      <input class="input" id="nome" name="nome" type="text" required maxlength="255" value="<?php echo e($a['nome'] ?? ''); ?>" <?php echo $invalido('nome'); ?>>
      <?php echo $erro('nome'); ?>
    </div>
    <div class="grelha-campos">
      <div class="campo" style="flex-grow: 2">
        <label for="categoria_id">Categoria <span aria-hidden="true" style="color: var(--perigo)">*</span></label>
        <select class="select" id="categoria_id" name="categoria_id" required <?php echo $invalido('categoria_id'); ?>>
          <option value="">Escolha uma categoria</option>
          <?php foreach ($data['categorias'] as $c) { ?>
            <option value="<?php echo $c['id']; ?>" <?php echo (int) ($a['categoria_id'] ?? 0) === (int) $c['id'] ? 'selected' : ''; ?>><?php echo e($c['categoria']); ?></option>
          <?php } ?>
        </select>
        <?php echo $erro('categoria_id'); ?>
        <a class="campo-ajuda" href="<?php echo $url_alias; ?>/categoria">Gerir categorias</a>
      </div>
      <div class="campo">
        <label for="ano">Ano do modelo <span aria-hidden="true" style="color: var(--perigo)">*</span></label>
        <input class="input" id="ano" name="ano" type="number" required min="1900" max="<?php echo (int) date('Y') + 1; ?>" value="<?php echo e($a['ano'] ?? date('Y')); ?>" <?php echo $invalido('ano'); ?>>
        <?php echo $erro('ano'); ?>
      </div>
    </div>
  </section>

  <section class="cartao cartao-corpo" aria-labelledby="s-preco">
    <h2 id="s-preco">Preço e avaliação</h2>
    <div class="grelha-campos">
      <div class="campo">
        <label for="preco">Preço (€) <span aria-hidden="true" style="color: var(--perigo)">*</span></label>
        <input class="input" id="preco" name="preco" type="number" step="0.01" min="0.01" required value="<?php echo e($a['preco'] ?? ''); ?>" <?php echo $invalido('preco'); ?>>
        <?php echo $erro('preco'); ?>
      </div>
      <div class="campo">
        <label for="avaliacao">Avaliação <span aria-hidden="true" style="color: var(--perigo)">*</span></label>
        <input class="input" id="avaliacao" name="avaliacao" type="number" step="0.1" min="0" max="5" required value="<?php echo e($a['avaliacao'] ?? ''); ?>" <?php echo $invalido('avaliacao') ?: 'aria-describedby="avaliacao-ajuda"'; ?>>
        <?php echo $erro('avaliacao') ?: '<span class="campo-ajuda" id="avaliacao-ajuda">De 0 a 5, com uma casa decimal.</span>'; ?>
      </div>
    </div>
  </section>

  <?php if ($editar) { ?>
  <section class="cartao cartao-corpo" aria-labelledby="s-img">
    <div>
      <h2 id="s-img">Imagens</h2>
      <p class="secundario">A primeira imagem é a principal. Uma imagem com cor é usada nas variantes dessa cor.</p>
    </div>
    <?php if (!empty($data['imagens'])) { ?>
      <ul class="lista-imagens">
        <?php foreach ($data['imagens'] as $i => $im) { ?>
          <li>
            <img src="<?php echo e($im['caminho']); ?>" alt="Imagem <?php echo $i + 1; ?>">
            <span class="secundario"><?php echo $i === 0 ? '<strong>Principal</strong> · ' : ''; ?><?php echo $im['cor'] ? 'Cor: ' . e($im['cor']) : 'Todas as cores'; ?></span>
            <button type="button" class="btn-icone perigo"
              data-eliminar="<?php echo $url_alias; ?>/artigo/removerImagem/<?php echo $im['id']; ?>"
              data-titulo="Remover esta imagem?"
              data-item="Imagem <?php echo $i + 1; ?> de <?php echo e($a['nome']); ?>"
              data-consequencia="O ficheiro continua na pasta do projeto; só deixa de estar na galeria."
              data-confirmar="Remover imagem"><?php echo icone('eliminar', 16); ?> Remover</button>
          </li>
        <?php } ?>
      </ul>
    <?php } ?>
    <div class="grelha-campos" style="align-items: flex-end">
      <div class="campo" style="flex-grow: 3">
        <label for="nova-imagem">Adicionar imagem</label>
        <input class="input" id="nova-imagem" name="caminho" type="text" form="form-imagem" placeholder="<?php echo $url_alias; ?>/assets/img/artigos/<?php echo $a['id']; ?>_5.jpg">
      </div>
      <div class="campo">
        <label for="nova-imagem-cor">Cor <span class="opcional">(opcional)</span></label>
        <input class="input" id="nova-imagem-cor" name="cor" type="text" form="form-imagem" maxlength="100">
      </div>
      <button type="submit" class="btn btn-secundario" form="form-imagem" style="flex: 0 0 auto">Adicionar</button>
    </div>
  </section>
  <?php } ?>

  <div class="barra-fixa barra-acoes">
    <a class="btn btn-secundario" href="<?php echo $editar ? $url_alias . '/artigo/get/' . $a['id'] : $url_alias . '/artigo'; ?>">Cancelar</a>
    <button type="submit" class="btn btn-primario"><?php echo $editar ? 'Guardar alterações' : 'Criar artigo'; ?></button>
  </div>
</form>

<?php if ($editar) { ?>
  <form id="form-imagem" action="<?php echo $url_alias; ?>/artigo/adicionarImagem/<?php echo $a['id']; ?>" method="POST">
    <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
  </form>
<?php } ?>
