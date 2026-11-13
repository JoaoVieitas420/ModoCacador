<?php
use app\core\Controller;
use app\core\Sessao;

class Categoria extends Controller {

  public function index() {
    $this->listar();
  }

  /**
  * Lista de categorias (com o formulário de criação no topo).
  * Privado: os parâmetros não podem vir do URL.
  *
  * @param  int|null  $editarId   categoria em modo de edição (renomear na própria linha)
  */
  private function listar($editarId = null, $erro = null, $valor = '') {
    $Categorias = $this->model('Categorias');
    $this->view('categoria/index', [
      'titulo' => 'Categorias',
      'categorias' => $Categorias::getCategoriasComContagem(),
      'editar_id' => (int) $editarId,
      'erro' => $erro,
      'valor' => $valor,
      'classe_conteudo' => 'conteudo-estreito'
    ]);
  }

  /**
  * Ver uma categoria = lista de artigos filtrada por essa categoria
  */
  public function get($id = null) {
    $this->redirect('/artigo?categoria=' . (int) $id);
  }

  public function create() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->redirect('/categoria');
    }
    $nome = trim($_POST['categoria'] ?? '');
    $erro = $this->validar($nome, null);
    if ($erro) {
      return $this->listar(null, $erro, $nome);
    }
    $Categorias = $this->model('Categorias');
    $Categorias::addCategoria(['categoria' => $nome]);
    Sessao::flash('sucesso', 'Categoria <strong>' . e($nome) . '</strong> criada.');
    $this->redirect('/categoria');
  }

  public function update($id = null) {
    $categoria = $this->obterCategoria($id);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $nome = trim($_POST['categoria'] ?? '');
      $erro = $this->validar($nome, (int) $id);
      if ($erro) {
        return $this->listar($id, $erro, $nome);
      }
      $Categorias = $this->model('Categorias');
      $Categorias::updateCategoria((int) $id, ['categoria' => $nome]);
      Sessao::flash('sucesso', 'Categoria <strong>' . e($categoria['categoria']) . '</strong> renomeada para <strong>' . e($nome) . '</strong>.');
      $this->redirect('/categoria');
    }
    $this->listar($id, null, $categoria['categoria']);
  }

  /**
  * Eliminação (apenas POST). Uma categoria com artigos não pode ser eliminada (chave estrangeira em artigos).
  */
  public function delete($id = null) {
    $categoria = $this->obterCategoria($id);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $Categorias = $this->model('Categorias');
      $total = count($Categorias::getArtigosByCategoria((int) $id));
      if ($total > 0) {
        Sessao::flash('erro', 'A categoria <strong>' . e($categoria['categoria']) . '</strong> tem ' . $total . ' artigos e não pode ser eliminada. Mova-os primeiro para outra categoria.');
      } else {
        $Categorias::deleteCategoria((int) $id);
        Sessao::flash('sucesso', 'Categoria <strong>' . e($categoria['categoria']) . '</strong> eliminada.');
      }
    }
    $this->redirect('/categoria');
  }

  private function obterCategoria($id) {
    $Categorias = $this->model('Categorias');
    $categoria = is_numeric($id) ? ($Categorias::findCategoriaById((int) $id)[0] ?? null) : null;
    if ($categoria === null) {
      $this->pageNotFound();
      exit;
    }
    return $categoria;
  }

  private function validar(string $nome, $ignorarId) {
    if ($nome === '') {
      return 'Indique o nome da categoria.';
    }
    if (mb_strlen($nome) > 255) {
      return 'O nome pode ter no máximo 255 caracteres.';
    }
    $Categorias = $this->model('Categorias');
    if ($Categorias::existeCategoria($nome, $ignorarId)) {
      return 'Já existe uma categoria com o nome "' . e($nome) . '".';
    }
    return null;
  }
}
?>
