<h2>Nova Variante</h2>

<form action="<?php echo $url_alias;?>/variante/create/<?php echo $data['artigo_id'];?>" method="POST">
  <label for="cor">Cor:</label>
  <input type="text" id="cor" name="cor"><br>

  <label for="tamanho">Tamanho:</label>
  <input type="text" id="tamanho" name="tamanho"><br>

  <label for="material">Material:</label>
  <input type="text" id="material" name="material"><br>

  <label for="stock">Stock:</label>
  <input type="number" min="0" id="stock" name="stock" value="0" required><br>

  <button type="submit">Criar Variante</button>
</form>
<a href="<?php echo $url_alias;?>/artigo/get/<?php echo $data['artigo_id'];?>">Voltar</a>
