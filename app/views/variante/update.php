<?php
// debug
// print_r($data);
?>

<h2>Editar Variante</h2>
<form action="<?php echo $url_alias;?>/variante/update/<?php echo $data['variante'][0]['id'];?>" method="POST" onsubmit="return confirm('Guardar as alterações desta variante?');">
  <label for="cor">Cor:</label>
  <input type="text" id="cor" name="cor" value="<?php echo htmlspecialchars($data['variante'][0]['cor'] ?? ''); ?>"><br>

  <label for="tamanho">Tamanho:</label>
  <input type="text" id="tamanho" name="tamanho" value="<?php echo htmlspecialchars($data['variante'][0]['tamanho'] ?? ''); ?>"><br>

  <label for="material">Material:</label>
  <input type="text" id="material" name="material" value="<?php echo htmlspecialchars($data['variante'][0]['material'] ?? ''); ?>"><br>

  <label for="stock">Stock:</label>
  <input type="number" min="0" id="stock" name="stock" value="<?php echo $data['variante'][0]['stock']; ?>" required><br>

  <button type="submit">Atualizar Variante</button>
</form>
<a href="<?php echo $url_alias;?>/artigo/get/<?php echo $data['variante'][0]['artigo_id'];?>">Voltar</a>
