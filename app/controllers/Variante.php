<?php
use app\core\Controller;
class Variante extends Controller {

  /**
  * Criação de uma variante para o artigo indicado
  *
  * @param  int   $artigoId   Id. artigo
  */
  public function create($artigoId = null) {
    if (!is_numeric($artigoId)) {
      return $this->pageNotFound();
    }
    $Variantes = $this->model('Variantes');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $newVarianteData = [
        'artigo_id' => $artigoId,
        'cor' => ($_POST['cor'] ?? '') !== '' ? $_POST['cor'] : null,
        'tamanho' => ($_POST['tamanho'] ?? '') !== '' ? $_POST['tamanho'] : null,
        'material' => ($_POST['material'] ?? '') !== '' ? $_POST['material'] : null,
        'stock' => (int) $_POST['stock']
      ];
      $info = $Variantes::addVariante($newVarianteData);
      $this->showArtigo($artigoId, $Variantes, $info, 'INSERT');
    } else {
      $this->view('variante/create', ['artigo_id' => $artigoId]);
    }
  }

  public function update($id = null) {
    if (!is_numeric($id)) {
      return $this->pageNotFound();
    }
    $Variantes = $this->model('Variantes');
    $variante = $Variantes::findVarianteById($id);
    if (count($variante) == 0) {
      return $this->pageNotFound();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $updatedVarianteData = [
        'cor' => ($_POST['cor'] ?? '') !== '' ? $_POST['cor'] : null,
        'tamanho' => ($_POST['tamanho'] ?? '') !== '' ? $_POST['tamanho'] : null,
        'material' => ($_POST['material'] ?? '') !== '' ? $_POST['material'] : null,
        'stock' => (int) $_POST['stock']
      ];
      $info = $Variantes::updateVariante($id, $updatedVarianteData);
      $this->showArtigo($variante[0]['artigo_id'], $Variantes, $info, 'UPDATE');
    } else {
      $this->view('variante/update', ['variante' => $variante]);
    }
  }

  public function delete($id = null) {
    if (!is_numeric($id)) {
      return $this->pageNotFound();
    }
    $Variantes = $this->model('Variantes');
    $info = $Variantes::deleteVariante($id);
    if ($info === null) {
      return $this->pageNotFound();
    }
    $this->showArtigo($info['artigo_id'], $Variantes, $info, 'DELETE');
  }

  /**
  * Depois de uma operação sobre uma variante, volta à página do artigo (view artigo/get)
  */
  private function showArtigo($artigoId, $Variantes, $info, $type) {
    $Artigos = $this->model('Artigos');
    $Categorias = $this->model('Categorias');
    $Imagens = $this->model('Imagens');
    $this->view('artigo/get', [
      'artigos' => $Artigos::findArtigoById($artigoId),
      'categorias' => $Categorias::getAllCategorias(),
      'variantes' => $Variantes::getVariantesByArtigo($artigoId),
      'imagens' => $Imagens::getImagensByArtigo($artigoId),
      'info' => $info,
      'type' => $type
    ]);
  }
}
?>
