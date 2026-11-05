<h2>Criar Nova Categoria</h2>

<form action="<?php echo $url_alias;?>/categoria/create" method="POST">
  <label for="categoria">Nome:</label>
  <input type="text" id="categoria" name="categoria" required><br>

  <button type="submit">Criar Categoria</button>
</form>
<a href="<?php echo $url_alias;?>/categoria">Voltar</a>
