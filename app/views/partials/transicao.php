<?php
// Ativação das transições entre páginas (View Transitions), em linha no início do <head>.
// Não fica no main.css porque o browser verifica esta regra quando a página nova é mostrada:
// se calcular os estilos antes de o main.css chegar (acontece, por exemplo, com extensões
// que correm no início de cada página), não a encontra e cancela a transição com o erro
// "InvalidStateError: Transition was aborted because of invalid state. ViewTransition opt-in disabled".
// Em linha, a regra existe desde o primeiro instante. O resto (menu parado, fade) está no main.css.
?>
  <style>@view-transition { navigation: auto; }</style>
