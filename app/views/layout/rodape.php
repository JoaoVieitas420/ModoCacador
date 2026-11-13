  </main>
</div>

<dialog class="dialogo" id="dialogo-eliminar" aria-labelledby="dialogo-titulo" aria-describedby="dialogo-texto">
  <form method="POST" class="dialogo-corpo" id="dialogo-form">
    <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
    <div class="dialogo-topo">
      <div class="dialogo-icone"><?php echo icone('eliminar', 22); ?></div>
      <div>
        <h2 id="dialogo-titulo">Eliminar?</h2>
        <p id="dialogo-texto" class="secundario">Esta ação não pode ser anulada.</p>
      </div>
    </div>
    <div class="dialogo-item" id="dialogo-item"></div>
    <p id="dialogo-consequencia" class="secundario"></p>
    <div class="barra-acoes">
      <button type="button" class="btn btn-secundario" data-fechar autofocus>Cancelar</button>
      <button type="submit" class="btn btn-perigo" id="dialogo-confirmar">Eliminar</button>
    </div>
  </form>
</dialog>

<script src="<?php echo $url_alias; ?>/assets/js/main.js?v=<?php echo filemtime('assets/js/main.js'); ?>"></script>
</body>
</html>
