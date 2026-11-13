<?php
use app\core\Controller;

class Home extends Controller {
  // a página inicial da aplicação é a lista de artigos
  public function index() {
    $this->redirect('/artigo');
  }

}
?>
