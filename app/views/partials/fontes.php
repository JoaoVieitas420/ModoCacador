<?php
// Pré-carregamento das fontes locais (as regras @font-face estão no main.css).
// O browser começa a descarregá-las logo, em paralelo com o CSS, e depois ficam em cache:
// o texto é desenhado à primeira com a fonte certa, sem trocar de fonte a meio.
// crossorigin é obrigatório no preload de fontes (mesmo sendo do próprio site).
foreach (['inter-latin', 'space-grotesk-latin', 'saira-stencil-one-latin'] as $fonte) { ?>
  <link rel="preload" href="<?php echo $url_alias; ?>/assets/fonts/<?php echo $fonte; ?>.woff2" as="font" type="font/woff2" crossorigin>
<?php } ?>
