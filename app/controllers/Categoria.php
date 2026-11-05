<?php
use app\core\Controller;
class Categoria extends Controller {
  /**
  * Invocação da view index.php
  */
  public function index() {
    $Categorias = $this->model('Categorias');
    $data = $Categorias::getAllCategorias();
    $this->view('categoria/index', ['categorias' => $data]);
  }

  /**
  * Invocação da view get.php
  *
  * @param  int   $id   Id. categoria
  */
  public function get($id = null) {
    if (is_numeric($id)) {
      $Categorias = $this->model('Categorias');
      $data = $Categorias::findCategoriaById($id);
      $artigos = $Categorias::getArtigosByCategoria($id);
      $this->view('categoria/get', ['categorias' => $data, 'artigos' => $artigos]);
    } else {
      $this->pageNotFound();
    }
  }

  public function create() {
    $Categorias = $this->model('Categorias');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $newCategoriaData = [
        'categoria' => $_POST['categoria']
      ];
      $info = $Categorias::addCategoria($newCategoriaData);

      $data = $Categorias::getAllCategorias();
      $this->view('categoria/index', ['categorias' => $data, 'info' => $info, 'type' => 'INSERT']);
    } else {
      $this->view('categoria/create');
    }
  }

  public function update($id = null) {
    $Categorias = $this->model('Categorias');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $updatedCategoriaData = [
        'categoria' => $_POST['categoria']
      ];
      $info = $Categorias::updateCategoria($id, $updatedCategoriaData);

      $data = $Categorias::getAllCategorias();
      $this->view('categoria/index', ['categorias' => $data, 'info' => $info, 'type' => 'UPDATE']);
    } else {
      $data = $Categorias::findCategoriaById($id);
      $this->view('categoria/update', ['categoria' => $data]);
    }
  }

  public function delete($id = null) {
    if (is_numeric($id)) {
      $Categorias = $this->model('Categorias');

      // uma categoria com artigos associados não pode ser eliminada (chave estrangeira em artigos)
      if (count($Categorias::getArtigosByCategoria($id)) > 0) {
        $info = $Categorias::findCategoriaById($id)[0];
        $type = 'DELETE_BLOCKED';
      } else {
        $info = $Categorias::deleteCategoria($id);
        $type = 'DELETE';
      }

      $data = $Categorias::getAllCategorias();
      $this->view('categoria/index', ['categorias' => $data, 'info' => $info, 'type' => $type]);
    } else {
      $this->pageNotFound();
    }
  }
}
?>
