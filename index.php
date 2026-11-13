<?php
  define('URL_BASE', '/modocacador'); // caminho do alias do Apache (usado nos links e redirecionamentos)

  require './app/load.php';
  require './app/helpers.php'; // funções auxiliares: e(), euros(), iniciais(), icone()
  use app\core\App; // importação/aliasing com o operador use
                    // a instrução acima é igual a: use app\core\App as App;
                    // a barra \ define o caminho das classes (diferente do caminho dos foicheiros "/")
  use app\core\Controller;
  use app\core\Sessao;

  Sessao::iniciar();
  $app = new App();

//================================================================================
// NOTAS:
//================================================================================
/*
Autoload function:
_ A técnica acima evita que sejam feitos "requires" para cada uma das classes.
_ O PHP pode fazer o carregamento das classes quando estas são necessárias.
*/
//================================================================================
?>
