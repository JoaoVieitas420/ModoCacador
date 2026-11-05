<?php
// debug
// print_r($data);
?>

<h2>Editar Categoria</h2>
<form action="<?php echo $url_alias;?>/categoria/update/<?php echo $data['categoria'][0]['id'];?>" method="POST" onsubmit="return confirm('Guardar as alterações desta categoria?');">
  <label for="categoria">Nome:</label>
  <input type="text" id="categoria" name="categoria" value="<?php echo $data['categoria'][0]['categoria']; ?>" required><br>

  <button type="submit">Atualizar Categoria</button>
</form>
<a href="<?php echo $url_alias;?>/categoria">Voltar</a>
