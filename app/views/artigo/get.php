<pre>
<?php 
// debug
// print_r($data);
?>
</pre>

<?php
if (count($data['artigos']) == 0) {
?>
  <h1>O artigo não existe na nossa base de dados...</h1>
<?php 
} else {
  $idCategoria = $data['artigos'][0]['categoria_id'];
  $categoria_filtrada = array_filter($data['categorias'], function ($categoria) use ($idCategoria) {
      return $categoria['id'] == $idCategoria;
  });
  $values = array_values($categoria_filtrada);
  $categoria_descr = $values[0]['categoria'];
?>

  <div>
  <?php
  echo "Nome: " . $data['artigos'][0]['nome'];
  ?>
  </div>

  <div>
  <?php
  echo "Preço: " . $data['artigos'][0]['preco'] . " €";
  ?>
  </div>

  <div>
  <?php
  echo "Avaliação: " . $data['artigos'][0]['avaliacao'];
  ?>
  </div>

  <div>
  <?php
  echo "Ano: " . $data['artigos'][0]['ano'];
  ?>
  </div>

  <?php if (!empty($data['imagens'])) { ?>
  <div>
    <?php foreach ($data['imagens'] as $imagem) { ?>
      <figure style="display:inline-block;vertical-align:top;max-width:220px;margin:4px">
        <a href="<?php echo htmlspecialchars($imagem['caminho']); ?>" target="_blank">
          <img src="<?php echo htmlspecialchars($imagem['caminho']); ?>" alt="<?php echo htmlspecialchars($imagem['descricao'] ?? $data['artigos'][0]['nome']); ?>" style="max-width:220px;max-height:180px">
        </a>
        <?php if (!empty($imagem['cor'])) { ?>
          <figcaption style="font-size:11px">Cor: <?php echo htmlspecialchars($imagem['cor']); ?></figcaption>
        <?php } ?>
      </figure>
    <?php } ?>
  </div>
  <?php } elseif (!empty($data['artigos'][0]['imagem'])) { ?>
  <div>
    <img src="<?php echo htmlspecialchars($data['artigos'][0]['imagem']); ?>" alt="<?php echo htmlspecialchars($data['artigos'][0]['nome']); ?>" style="max-width:300px">
  </div>
  <?php } ?>

  <div>
  <?php
  echo "Categoria: " . $categoria_descr;
  ?>
  </div>

  <h3>Variantes</h3>
  <?php
  if (isset($data['info']) && isset($data['type'])) {
    switch ($data['type']) {
      case 'INSERT':
        echo '<p>Variante inserida com sucesso.</p>';
        break;
      case 'UPDATE':
        echo '<p>A variante foi atualizada.</p>';
        break;
      case 'DELETE':
        echo '<p>A variante foi eliminada.</p>';
        break;
    }
  }
  ?>
  <a href="<?php echo $url_alias;?>/variante/create/<?php echo $data['artigos'][0]['id'];?>">NOVA VARIANTE</a>

  <?php if (empty($data['variantes'])) { ?>
    <p>Este artigo não tem variantes.</p>
  <?php } else { ?>
    <ul>
      <?php foreach ($data['variantes'] as $variante) { ?>
        <li>
          <?php
          // imagem da variante: a da sua cor, senão a imagem principal do artigo
          $imagemVariante = $data['artigos'][0]['imagem'];
          foreach ($data['imagens'] ?? [] as $imagem) {
            if ($imagem['cor'] !== null && $imagem['cor'] === $variante['cor']) {
              $imagemVariante = $imagem['caminho'];
              break;
            }
          }
          if (!empty($imagemVariante)) {
            echo '<img src="' . htmlspecialchars($imagemVariante) . '" alt="" style="width:40px;height:40px;object-fit:cover;vertical-align:middle"> ';
          }
          $descricao = array_filter([$variante['cor'], $variante['tamanho'], $variante['material']]);
          echo htmlspecialchars(implode(' / ', $descricao)) . ' - Stock: ' . $variante['stock'];
          ?>
          <a href="<?php echo $url_alias;?>/variante/update/<?php echo $variante['id'];?>" onclick="return confirm(<?php echo htmlspecialchars(json_encode('Editar a variante "' . implode(' / ', $descricao) . '"?')); ?>);">Editar</a> |
          <a href="<?php echo $url_alias;?>/variante/delete/<?php echo $variante['id'];?>" onclick="return confirm(<?php echo htmlspecialchars(json_encode('Tem a certeza que quer eliminar a variante "' . implode(' / ', $descricao) . '"?')); ?>);">Eliminar</a>
        </li>
      <?php } ?>
    </ul>
  <?php } ?>
<?php
}
?>
<a href="<?php echo $url_alias;?>/artigo">Voltar</a>