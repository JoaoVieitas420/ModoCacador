<pre>
<?php 
// debug
// print_r($data);
?>
</pre>

<?php
if (count($data['categorias']) == 0) {
?>
  <h1>A categoria não existe na nossa base de dados...</h1>
<?php 
} else {
?>

  <div>
  <?php
  echo "Categoria: " . $data['categorias'][0]['categoria'];
  ?>
  </div>

  <h3>Artigos desta categoria</h3>
  <?php if (empty($data['artigos'])) { ?>
    <p>Esta categoria ainda não tem artigos.</p>
  <?php } else { ?>
    <ul>
      <?php foreach ($data['artigos'] as $artigo) { ?>
        <li>
          <a href="<?php echo $url_alias;?>/artigo/get/<?php echo $artigo['id'];?>"><?php echo $artigo['nome']; ?></a>
          - <?php echo $artigo['preco']; ?> €
        </li>
      <?php } ?>
    </ul>
  <?php } ?>
<?php 
}
?>
<a href="<?php echo $url_alias;?>/categoria">Voltar</a>
