<?php
namespace app\core;

/**
* Classe que instancia um model e invoca uma view
*/
class Controller {

  /**
  * Método para a invocação de um model.
  *
  * @param  string  $model   Model que será instanciado para usar numa view
  */
  public function model($model) {
    require_once 'app/models/' . $model . '.php'; // require_once: o mesmo model pode ser pedido mais do que uma vez
    $classe = 'app\\models\\' . $model; // *
    return new $classe();
    // *  o duplo backslash deve-se ao facto de estar a ser referenciada uma classe numa string
    //    o primeiro backslash "escapa" o segundo
  }

  /**
  * Método para a invocação de uma view (página).
  *
  * @param  string  $view    A view que será invocada
  * @param  array   $data    Dados a exibir na view ('titulo' define o título da página)
  * @param  bool    $layout  false para páginas sem menu (ex.: login)
  */
  public function view(string $view, $data = [], bool $layout = true) {
    $url_alias = URL_BASE;
    $csrf = Sessao::csrfToken();
    $utilizador = Sessao::utilizador();
    $titulo = $data['titulo'] ?? null;
    $classe_conteudo = $data['classe_conteudo'] ?? '';
    if ($layout && $utilizador !== null) {
      require 'app/views/layout/cabecalho.php';
      require 'app/views/' . $view . '.php';
      require 'app/views/layout/rodape.php';
    } else {
      require 'app/views/' . $view . '.php';
    }
  }

  /**
  * Redireciona para outra página da aplicação (padrão Post/Redirect/Get)
  *
  * @param  string  $caminho  caminho a seguir ao alias, ex.: '/artigo'
  */
  protected function redirect(string $caminho) {
    header('Location: ' . URL_BASE . $caminho);
    exit;
  }

  /**
  * Método herdado em todas as subclasses.
  * É invocado quando o método ou classe não são encontrados.
  */
  public function pageNotFound() {
    http_response_code(404);
    $this->view('erro404');
  }
}
