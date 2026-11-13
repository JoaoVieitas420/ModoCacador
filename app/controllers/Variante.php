<?php
use app\core\Controller;
use app\core\Sessao;

class Variante extends Controller {

  public function index() {
    $this->redirect('/artigo');
  }

  /**
  * Criação de uma variante (formulário na página do artigo)
  *
  * @param  int   $artigoId   Id. artigo
  */
  public function create($artigoId = null) {
    $Artigos = $this->model('Artigos');
    $artigo = is_numeric($artigoId) ? ($Artigos::findArtigoById((int) $artigoId)[0] ?? null) : null;
    if ($artigo === null) {
      return $this->pageNotFound();
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $Variantes = $this->model('Variantes');
      [$dados, $erro] = $this->validar($_POST, (int) $artigoId, null);
      if ($erro) {
        Sessao::flash('erro', $erro);
      } else {
        $Variantes::addVariante($dados + ['artigo_id' => (int) $artigoId]);
        Sessao::flash('sucesso', 'Variante <strong>' . e($this->descricao($dados)) . '</strong> adicionada.');
      }
    }
    $this->redirect('/artigo/get/' . (int) $artigoId);
  }

  public function update($id = null) {
    $variante = $this->obterVariante($id);
    $Artigos = $this->model('Artigos');
    $artigo = $Artigos::findArtigoById((int) $variante['artigo_id'])[0];
    $erro = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $Variantes = $this->model('Variantes');
      [$dados, $erro] = $this->validar($_POST, (int) $variante['artigo_id'], (int) $id);
      if (!$erro) {
        $Variantes::updateVariante((int) $id, $dados);
        Sessao::flash('sucesso', 'Variante <strong>' . e($this->descricao($dados)) . '</strong> atualizada.');
        $this->redirect('/artigo/get/' . (int) $variante['artigo_id']);
      }
      $variante = array_merge($variante, $dados);
    }

    $this->view('variante/form', ['titulo' => 'Editar variante', 'variante' => $variante, 'artigo' => $artigo, 'erro' => $erro]);
  }

  /**
  * Eliminação (apenas POST, a partir do diálogo de confirmação)
  */
  public function delete($id = null) {
    $variante = $this->obterVariante($id);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $Variantes = $this->model('Variantes');
      $Variantes::deleteVariante((int) $id);
      Sessao::flash('sucesso', 'Variante <strong>' . e($this->descricao($variante)) . '</strong> eliminada.');
    }
    $this->redirect('/artigo/get/' . (int) $variante['artigo_id']);
  }

  private function obterVariante($id) {
    $Variantes = $this->model('Variantes');
    $variante = is_numeric($id) ? ($Variantes::findVarianteById((int) $id)[0] ?? null) : null;
    if ($variante === null) {
      $this->pageNotFound();
      exit;
    }
    return $variante;
  }

  /**
  * @return array [dados limpos, mensagem de erro ou null]
  */
  private function validar(array $post, int $artigoId, $ignorarId) {
    $limpar = function ($campo) use ($post) {
      $valor = trim($post[$campo] ?? '');
      return $valor !== '' ? $valor : null;
    };
    $dados = [
      'cor' => $limpar('cor'),
      'tamanho' => $limpar('tamanho'),
      'material' => $limpar('material'),
      'stock' => trim($post['stock'] ?? '')
    ];
    if ($dados['cor'] === null && $dados['tamanho'] === null && $dados['material'] === null) {
      return [$dados, 'Indique pelo menos a cor, o tamanho ou o material da variante.'];
    }
    if (!ctype_digit($dados['stock'])) {
      return [$dados, 'O stock tem de ser um número inteiro igual ou superior a 0.'];
    }
    $dados['stock'] = (int) $dados['stock'];
    $Variantes = $this->model('Variantes');
    if ($Variantes::existeVariante($artigoId, $dados['cor'], $dados['tamanho'], $dados['material'], $ignorarId)) {
      return [$dados, 'Já existe uma variante <strong>' . e($this->descricao($dados)) . '</strong> neste artigo.'];
    }
    return [$dados, null];
  }

  private function descricao(array $v) {
    return implode(' / ', array_filter([$v['cor'] ?? null, $v['tamanho'] ?? null, $v['material'] ?? null]));
  }
}
?>
