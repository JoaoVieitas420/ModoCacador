<?php if ($utilizador === null) { ?>
<!doctype html>
<html lang="pt-PT">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Página não encontrada — Modo Caçador</title>
  <link rel="stylesheet" href="<?php echo $url_alias; ?>/assets/css/main.css?v=<?php echo filemtime('assets/css/main.css'); ?>">
</head>
<body style="padding: 48px 24px">
<?php } ?>
<header class="cabecalho">
  <div>
    <h1>Página não encontrada</h1>
    <p>O endereço pode estar errado ou o registo já foi eliminado.</p>
  </div>
</header>
<p><a class="btn btn-primario" href="<?php echo $url_alias; ?>/artigo">Voltar aos artigos</a></p>
<?php if ($utilizador === null) { ?>
</body>
</html>
<?php } ?>
