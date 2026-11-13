<?php
use app\core\Controller;
use app\core\Sessao;

class Artigo extends Controller {

  const POR_PAGINA = 10;

  /**
  * Lista de artigos com pesquisa, filtro por categoria, ordenação e paginação (parâmetros GET)
  */
  public function index() {
    $Artigos = $this->model('Artigos');
    $Categorias = $this->model('Categorias');

    $pesquisa = trim($_GET['q'] ?? '');
    $categoriaId = (int) ($_GET['categoria'] ?? 0);
    $ordem = array_key_exists($_GET['ordem'] ?? '', $Artigos::ORDENACOES) ? $_GET['ordem'] : 'nome';
    $total = $Artigos::contarArtigos($pesquisa, $categoriaId);
    $paginas = max(1, (int) ceil($total / self::POR_PAGINA));
    $pagina = min(max(1, (int) ($_GET['pagina'] ?? 1)), $paginas);

    $this->view('artigo/index', [
      'titulo' => 'Artigos',
      'artigos' => $Artigos::pesquisarArtigos($pesquisa, $categoriaId, $ordem, self::POR_PAGINA, ($pagina - 1) * self::POR_PAGINA),
      'categorias' => $Categorias::getAllCategorias(),
      'filtros' => ['q' => $pesquisa, 'categoria' => $categoriaId, 'ordem' => $ordem],
      'total' => $total,
      'pagina' => $pagina,
      'paginas' => $paginas,
      'por_pagina' => self::POR_PAGINA
    ]);
  }

  /**
  * Detalhe do artigo: ficha, galeria e variantes
  *
  * @param  int   $id   Id. artigo
  */
  public function get($id = null) {
    $artigo = $this->obterArtigo($id);
    $Categorias = $this->model('Categorias');
    $Variantes = $this->model('Variantes');
    $Imagens = $this->model('Imagens');
    $categoria = $Categorias::findCategoriaById((int) $artigo['categoria_id']);

    $this->view('artigo/get', [
      'titulo' => $artigo['nome'],
      'artigo' => $artigo,
      'categoria' => $categoria[0] ?? null,
      'variantes' => $Variantes::getVariantesByArtigo((int) $id),
      'imagens' => $Imagens::getImagensByArtigo((int) $id)
    ]);
  }

  public function create() {
    $Artigos = $this->model('Artigos');
    $Categorias = $this->model('Categorias');
    $categorias = $Categorias::getAllCategorias();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      [$dados, $erros] = $this->validar($_POST, $categorias);
      if (empty($erros)) {
        $novo = $Artigos::addArtigo($dados);
        Sessao::flash('sucesso', 'Artigo <strong>' . e($novo['nome']) . '</strong> criado.');
        $this->redirect('/artigo/get/' . $novo['id']);
      }
      $this->view('artigo/form', ['titulo' => 'Novo artigo', 'artigo' => $dados, 'categorias' => $categorias, 'erros' => $erros]);
      return;
    }

    $this->view('artigo/form', ['titulo' => 'Novo artigo', 'artigo' => [], 'categorias' => $categorias, 'erros' => []]);
  }

  public function update($id = null) {
    $artigo = $this->obterArtigo($id);
    $Artigos = $this->model('Artigos');
    $Categorias = $this->model('Categorias');
    $Imagens = $this->model('Imagens');
    $categorias = $Categorias::getAllCategorias();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      [$dados, $erros] = $this->validar($_POST, $categorias);
      if (empty($erros)) {
        $dados['imagem'] = $artigo['imagem']; // a imagem principal é gerida pela galeria
        $Artigos::updateArtigo((int) $id, $dados);
        Sessao::flash('sucesso', 'Artigo <strong>' . e($dados['nome']) . '</strong> atualizado.');
        $this->redirect('/artigo/get/' . (int) $id);
      }
      $artigo = array_merge($artigo, $dados);
    } else {
      $erros = [];
    }

    $this->view('artigo/form', [
      'titulo' => 'Editar artigo',
      'artigo' => $artigo,
      'categorias' => $categorias,
      'imagens' => $Imagens::getImagensByArtigo((int) $id),
      'erros' => $erros
    ]);
  }

  /**
  * Eliminação (apenas POST, a partir do diálogo de confirmação).
  * As variantes e imagens do artigo são eliminadas em cascata pela base de dados.
  */
  public function delete($id = null) {
    $artigo = $this->obterArtigo($id);
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->redirect('/artigo/get/' . (int) $id);
    }
    $Artigos = $this->model('Artigos');
    $Artigos::deleteArtigo((int) $id);
    Sessao::flash('sucesso', 'Artigo <strong>' . e($artigo['nome']) . '</strong> eliminado.');
    $this->redirect('/artigo');
  }

  /**
  * Acrescenta uma imagem (caminho) à galeria do artigo
  */
  public function adicionarImagem($id = null) {
    $this->obterArtigo($id);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $caminho = trim($_POST['caminho'] ?? '');
      $cor = trim($_POST['cor'] ?? '');
      if ($caminho === '') {
        Sessao::flash('erro', 'Indique o caminho da imagem.');
      } else {
        $Imagens = $this->model('Imagens');
        $Imagens::addImagem((int) $id, $caminho, $cor !== '' ? $cor : null);
        Sessao::flash('sucesso', 'Imagem adicionada à galeria.');
      }
    }
    $this->redirect('/artigo/update/' . (int) $id);
  }

  public function removerImagem($imagemId = null) {
    $Imagens = $this->model('Imagens');
    $imagem = is_numeric($imagemId) ? ($Imagens::findImagemById((int) $imagemId)[0] ?? null) : null;
    if ($imagem === null) {
      return $this->pageNotFound();
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $Imagens::deleteImagem((int) $imagemId);
      Sessao::flash('sucesso', 'Imagem removida da galeria.');
    }
    $this->redirect('/artigo/update/' . (int) $imagem['artigo_id']);
  }

  /**
  * Devolve o artigo ou mostra a página 404 (e termina)
  */
  private function obterArtigo($id) {
    $Artigos = $this->model('Artigos');
    $artigo = is_numeric($id) ? ($Artigos::findArtigoById((int) $id)[0] ?? null) : null;
    if ($artigo === null) {
      $this->pageNotFound();
      exit;
    }
    return $artigo;
  }

  /**
  * Validação dos dados do formulário do artigo
  *
  * @return array [dados limpos, erros por campo]
  */
  private function validar(array $post, array $categorias) {
    $dados = [
      'nome' => trim($post['nome'] ?? ''),
      'preco' => str_replace(',', '.', trim($post['preco'] ?? '')),
      'avaliacao' => str_replace(',', '.', trim($post['avaliacao'] ?? '')),
      'ano' => trim($post['ano'] ?? ''),
      'categoria_id' => (int) ($post['categoria_id'] ?? 0),
      'imagem' => null
    ];
    $erros = [];
    if ($dados['nome'] === '') {
      $erros['nome'] = 'Indique o nome do artigo.';
    } elseif (mb_strlen($dados['nome']) > 255) {
      $erros['nome'] = 'O nome pode ter no máximo 255 caracteres.';
    }
    if (!is_numeric($dados['preco']) || (float) $dados['preco'] <= 0) {
      $erros['preco'] = 'Tem de ser maior que 0,00 €.';
    }
    if (!is_numeric($dados['avaliacao']) || (float) $dados['avaliacao'] < 0 || (float) $dados['avaliacao'] > 5) {
      $erros['avaliacao'] = 'Tem de estar entre 0 e 5.';
    }
    if (!ctype_digit($dados['ano']) || (int) $dados['ano'] < 1900 || (int) $dados['ano'] > (int) date('Y') + 1) {
      $erros['ano'] = 'Indique um ano entre 1900 e ' . ((int) date('Y') + 1) . '.';
    }
    if (!in_array($dados['categoria_id'], array_map('intval', array_column($categorias, 'id')), true)) {
      $erros['categoria_id'] = 'Escolha uma categoria.';
    }
    return [$dados, $erros];
  }
}
?>
