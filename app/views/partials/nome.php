<?php
// Nome do site "MODO CAÇAD⊕R" (o "O" final é uma mira)
// $wm_classe: classes extra (ex.: 'wm-xl wm-animar')
$wm_letras = function ($palavra, $inicio) {
  $html = '';
  foreach (mb_str_split($palavra) as $k => $letra) {
    $i = $inicio + $k;
    if ($letra === 'Ø') {
      $html .= '<span class="wm-l wm-o" style="--i: ' . $i . '"><svg class="ret" viewBox="0 0 24 24" aria-hidden="true"><circle class="ret-ring" cx="12" cy="12" r="8.5" pathLength="1"></circle><path class="ret-tick" d="M12 1.5v5M12 17.5v5M1.5 12h5M17.5 12h5"></path><circle class="ret-dot" cx="12" cy="12" r="1.7"></circle></svg></span>';
    } else {
      $html .= '<span class="wm-l" style="--i: ' . $i . '">' . $letra . '</span>';
    }
  }
  return $html;
};
?>
<span class="wm <?php echo $wm_classe ?? ''; ?>"><span class="sr-only">Modo Caçador</span><span class="wm-line" aria-hidden="true"><?php echo $wm_letras('MODO', 0); ?></span><span class="wm-line" aria-hidden="true"><?php echo $wm_letras('CAÇADØR', 4); ?></span></span>
