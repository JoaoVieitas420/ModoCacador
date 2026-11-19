<?php
use app\core\Controller;
use app\core\Sessao;

class Artigo extends Controller {

  const POR_PAGINA = 10;

  // fotografias enviadas na criação do artigo
  const FOTO_PASTA = 'assets/img/artigos/';
  const FOTO_MAX_BYTES = 5242880; // 5 MB por fotografia
  const FOTO_MAX_FICHEIROS = 10;
  const FOTO_TIPOS = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

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
      [$fotos, $erroFotos] = $this->validarFotos($_FILES['fotos'] ?? null);
      if ($erroFotos !== null) {
        $erros['fotos'] = $erroFotos;
      }
      if (empty($erros)) {
        $novo = $Artigos::addArtigo($dados);
        $guardadas = $this->guardarFotos((int) $novo['id'], $fotos);
        $mensagem = 'Artigo <strong>' . e($novo['nome']) . '</strong> criado';
        if ($guardadas > 0) {
          $mensagem .= ' com ' . $guardadas . ($guardadas === 1 ? ' fotografia' : ' fotografias');
        }
        if ($guardadas < count($fotos)) {
          Sessao::flash('erro', $mensagem . ', mas não foi possível guardar ' . (count($fotos) - $guardadas) . ' das fotografias. Adicione-as em Editar artigo.');
        } else {
          Sessao::flash('sucesso', $mensagem . '.');
        }
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
  * Envia uma ou mais fotografias (upload) para a galeria do artigo, com cor opcional
  */
  public function adicionarImagem($id = null) {
    $this->obterArtigo($id);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $cor = trim($_POST['cor'] ?? '');
      [$fotos, $erro] = $this->validarFotos($_FILES['fotos'] ?? null);
      if ($erro !== null) {
        Sessao::flash('erro', e($erro));
      } elseif (empty($fotos)) {
        Sessao::flash('erro', 'Escolha pelo menos uma fotografia.');
      } elseif (mb_strlen($cor) > 100) {
        Sessao::flash('erro', 'A cor pode ter no máximo 100 caracteres.');
      } else {
        $guardadas = $this->guardarFotos((int) $id, $fotos, $cor !== '' ? $cor : null);
        if ($guardadas < count($fotos)) {
          Sessao::flash('erro', 'Não foi possível guardar ' . (count($fotos) - $guardadas) . ' de ' . count($fotos) . ' fotografias. Tente novamente.');
        } else {
          Sessao::flash('sucesso', $guardadas === 1 ? 'Fotografia adicionada à galeria.' : $guardadas . ' fotografias adicionadas à galeria.');
        }
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
  * Validação das fotografias enviadas no formulário de criação (campo opcional, múltiplo)
  *
  * @param  array|null  $ficheiros  $_FILES['fotos']
  *
  * @return array [lista de fotos válidas (tmp_name, nome, extensao), mensagem de erro ou null]
  */
  private function validarFotos($ficheiros) {
    if (empty($ficheiros) || !is_array($ficheiros['name'])) {
      return [[], null];
    }
    $fotos = [];
    foreach ($ficheiros['name'] as $i => $nomeOriginal) {
      $erro = $ficheiros['error'][$i];
      if ($erro === UPLOAD_ERR_NO_FILE) {
        continue; // nenhum ficheiro escolhido
      }
      if ($erro === UPLOAD_ERR_INI_SIZE || $erro === UPLOAD_ERR_FORM_SIZE || ($erro === UPLOAD_ERR_OK && $ficheiros['size'][$i] > self::FOTO_MAX_BYTES)) {
        return [[], '«' . $nomeOriginal . '» tem mais de 5 MB.'];
      }
      if ($erro !== UPLOAD_ERR_OK) {
        return [[], 'Não foi possível receber «' . $nomeOriginal . '». Tente novamente.'];
      }
      $tipo = (new finfo(FILEINFO_MIME_TYPE))->file($ficheiros['tmp_name'][$i]);
      if (!isset(self::FOTO_TIPOS[$tipo])) {
        return [[], '«' . $nomeOriginal . '» não é JPG, PNG ou WebP.'];
      }
      $fotos[] = ['tmp_name' => $ficheiros['tmp_name'][$i], 'nome' => $nomeOriginal, 'extensao' => self::FOTO_TIPOS[$tipo]];
    }
    if (count($fotos) > self::FOTO_MAX_FICHEIROS) {
      return [[], 'Pode enviar no máximo ' . self::FOTO_MAX_FICHEIROS . ' fotografias de cada vez.'];
    }
    return [$fotos, null];
  }

  /**
  * Copia as fotografias para assets/img/artigos/ (com o nome do ficheiro enviado)
  * e acrescenta-as à galeria do artigo, pela ordem em que foram escolhidas.
  *
  * @param  string|null  $cor  cor das variantes a que as fotografias correspondem (null = todas)
  *
  * @return int número de fotografias guardadas
  */
  private function guardarFotos(int $artigoId, array $fotos, $cor = null) {
    $Imagens = $this->model('Imagens');
    $guardadas = 0;
    foreach ($fotos as $foto) {
      $nomeFicheiro = $this->nomeFicheiroLivre($foto['nome'], $foto['extensao']);
      if (move_uploaded_file($foto['tmp_name'], self::FOTO_PASTA . $nomeFicheiro)) {
        $Imagens::addImagem($artigoId, URL_BASE . '/' . self::FOTO_PASTA . $nomeFicheiro, $cor);
        $guardadas++;
      }
    }
    return $guardadas;
  }

  /**
  * Nome do ficheiro a partir do nome enviado: sem caminho nem caracteres especiais,
  * com a extensão do tipo real da imagem. Se já existir um ficheiro com esse nome,
  * acrescenta -2, -3, ... para não substituir imagens existentes.
  */
  private function nomeFicheiroLivre(string $nomeOriginal, string $extensao) {
    $base = pathinfo(basename($nomeOriginal), PATHINFO_FILENAME);
    // tira os acentos ("Colóquio ção" -> "Coloquio cao"); tabela fixa porque o iconv depende do locale do servidor
    $base = strtr($base, [
      'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'Á' => 'A', 'À' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A',
      'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e', 'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
      'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i', 'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I',
      'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'Ó' => 'O', 'Ò' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O',
      'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
      'ç' => 'c', 'Ç' => 'C', 'ñ' => 'n', 'Ñ' => 'N'
    ]);
    $base = trim(preg_replace('/[^A-Za-z0-9_-]+/', '-', $base), '-_');
    $base = substr($base !== '' ? $base : 'imagem', 0, 100);
    $nome = $base . '.' . $extensao;
    for ($n = 2; file_exists(self::FOTO_PASTA . $nome); $n++) {
      $nome = $base . '-' . $n . '.' . $extensao;
    }
    return $nome;
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
