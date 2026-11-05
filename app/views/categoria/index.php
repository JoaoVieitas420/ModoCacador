<h2>Lista de Categorias</h2>
<a href="<?php echo $url_alias;?>/categoria/create">NOVO</a>

<?php
// debug
//print_r($data);

if (isset($data['info']) && isset($data['type'])) {
  $type = $data['type'];
  switch ($type) {
    case 'INSERT':
      echo '<h3>Categoria - ' . $data['info']['categoria'] . ' - inserida com sucesso.</h3>';
      break;
    case 'UPDATE':
      echo '<h3>A categoria - ' . $data['info'][0] . ' - foi atualizada.</h3>';
      break;
    case 'DELETE':
      echo '<h3>A categoria - ' . $data['info']['categoria'] . ' - foi eliminada.</h3>';
      break;
    case 'DELETE_BLOCKED':
      echo '<h3>A categoria - ' . $data['info']['categoria'] . ' - tem artigos associados e não pode ser eliminada.</h3>';
      break;
  }
}
?>

<ul>
  <?php foreach ($data['categorias'] as $categoria) { ?>
    <li>
      <strong><?php echo $categoria['categoria']; ?></strong>
      <a href="<?php echo $url_alias;?>/categoria/get/<?php echo $categoria['id'];?>">Ver +</a> | 
      <a href="<?php echo $url_alias;?>/categoria/update/<?php echo $categoria['id'];?>" onclick="return confirm(<?php echo htmlspecialchars(json_encode('Editar a categoria "' . $categoria['categoria'] . '"?')); ?>);">Editar</a> | 
      <a href="<?php echo $url_alias;?>/categoria/delete/<?php echo $categoria['id'];?>" onclick="return confirm(<?php echo htmlspecialchars(json_encode('Tem a certeza que quer eliminar a categoria "' . $categoria['categoria'] . '"?')); ?>);">Eliminar</a>
    </li>
  <?php } ?>
</ul>
<a href="<?php echo $url_alias;?>/">Voltar</a>
