<?php
$v = $data['variante'];
$a = $data['artigo'];
?>
<nav class="migalhas" aria-label="Caminho">
  <a href="<?php echo $url_alias; ?>/artigo">Artigos</a> <?php echo icone('seta-dir', 14); ?>
  <a href="<?php echo $url_alias; ?>/artigo/get/<?php echo $a['id']; ?>"><?php echo e($a['nome']); ?></a> <?php echo icone('seta-dir', 14); ?>
  <span aria-current="page">Editar variante</span>
</nav>

<header class="cabecalho">
  <div>
    <h1>Editar variante</h1>
    <p><?php echo e($a['nome']); ?></p>
  </div>
</header>

<?php if (!empty($data['erro'])) { ?>
  <div class="alerta alerta-erro" role="alert"><?php echo icone('erro', 20); ?><span><?php echo $data['erro']; ?></span></div>
<?php } ?>

<form class="cartao cartao-corpo" action="<?php echo $url_alias; ?>/variante/update/<?php echo $v['id']; ?>" method="POST" style="max-width: 720px">
  <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
  <div class="grelha-campos">
    <div class="campo">
      <label for="cor">Cor</label>
      <input class="input" id="cor" name="cor" type="text" maxlength="100" value="<?php echo e($v['cor']); ?>">
    </div>
    <div class="campo">
      <label for="tamanho">Tamanho</label>
      <input class="input" id="tamanho" name="tamanho" type="text" maxlength="50" value="<?php echo e($v['tamanho']); ?>">
    </div>
  </div>
  <div class="grelha-campos">
    <div class="campo">
      <label for="material">Material <span class="opcional">(opcional)</span></label>
      <input class="input" id="material" name="material" type="text" maxlength="100" value="<?php echo e($v['material']); ?>">
    </div>
    <div class="campo">
      <label for="stock">Stock</label>
      <input class="input" id="stock" name="stock" type="number" min="0" required value="<?php echo e($v['stock']); ?>">
    </div>
  </div>
  <div class="barra-acoes">
    <a class="btn btn-secundario" href="<?php echo $url_alias; ?>/artigo/get/<?php echo $a['id']; ?>">Cancelar</a>
    <button type="submit" class="btn btn-primario">Guardar alterações</button>
  </div>
</form>
