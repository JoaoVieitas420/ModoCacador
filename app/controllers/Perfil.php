<?php
use app\core\Controller;
use app\core\Sessao;

class Perfil extends Controller {

  const FOTO_MAX_BYTES = 2097152; // 2 MB
  const FOTO_TIPOS = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

  public function index() {
    $this->mostrar([]);
  }

  /**
  * Atualiza o nome e, opcionalmente, a fotografia
  */
  public function dados() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->redirect('/perfil');
    }
    $sessao = Sessao::utilizador();
    $utilizador = $this->obterAtual();
    $nome = trim($_POST['nome'] ?? '');
    $fotografia = $utilizador['fotografia'];
    $erros = [];

    if ($nome === '' || mb_strlen($nome) > 400) {
      $erros['nome'] = 'Indique o seu nome (até 400 caracteres).';
    }

    if (!empty($_POST['remover_fotografia'])) {
      $fotografia = null;
    } elseif (!empty($_FILES['fotografia']['name'])) {
      $ficheiro = $_FILES['fotografia'];
      $tipo = $ficheiro['error'] === UPLOAD_ERR_OK ? (new finfo(FILEINFO_MIME_TYPE))->file($ficheiro['tmp_name']) : null;
      if ($ficheiro['error'] !== UPLOAD_ERR_OK || $ficheiro['size'] > self::FOTO_MAX_BYTES) {
        $erros['fotografia'] = 'A fotografia tem de ter no máximo 2 MB.';
      } elseif (!isset(self::FOTO_TIPOS[$tipo])) {
        $erros['fotografia'] = 'A fotografia tem de ser JPG, PNG ou WebP.';
      } else {
        // nome do ficheiro gerado no servidor (nunca o nome enviado pelo utilizador)
        $nomeFicheiro = substr(sha1($sessao['id']), 0, 12) . '-' . time() . '.' . self::FOTO_TIPOS[$tipo];
        if (move_uploaded_file($ficheiro['tmp_name'], 'assets/img/utilizadores/' . $nomeFicheiro)) {
          $fotografia = URL_BASE . '/assets/img/utilizadores/' . $nomeFicheiro;
        } else {
          $erros['fotografia'] = 'Não foi possível guardar a fotografia. Tente novamente.';
        }
      }
    }

    if (!empty($erros)) {
      return $this->mostrar($erros, ['nome' => $nome]);
    }

    $Utilizadores = $this->model('Utilizadores');
    $Utilizadores::updateDados($sessao['id'], $nome, $fotografia);
    Sessao::atualizar(['nome' => $nome, 'fotografia' => $fotografia]);
    Sessao::flash('sucesso', 'Os seus dados foram guardados.');
    $this->redirect('/perfil');
  }

  /**
  * Alteração da palavra-passe (obrigatória no primeiro acesso)
  */
  public function palavraPasse() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->redirect('/perfil');
    }
    $sessao = Sessao::utilizador();
    $utilizador = $this->obterAtual();
    $atual = $_POST['atual'] ?? '';
    $nova = $_POST['nova'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';
    $erros = [];

    if (!password_verify($atual, $utilizador['palavra_passe'])) {
      $erros['atual'] = 'A palavra-passe atual não está correta.';
    }
    if (mb_strlen($nova) < 8 || !preg_match('/\pL/u', $nova) || !preg_match('/\d/', $nova)) {
      $erros['nova'] = 'Tem de ter pelo menos 8 caracteres, com letras e números.';
    } elseif ($nova === $atual) {
      $erros['nova'] = 'Tem de ser diferente da palavra-passe atual.';
    }
    if (!isset($erros['nova']) && $confirmar !== $nova) {
      $erros['confirmar'] = 'As palavras-passe não coincidem.';
    }
    if (!empty($erros)) {
      return $this->mostrar($erros);
    }

    $Utilizadores = $this->model('Utilizadores');
    $Utilizadores::updatePalavraPasse($sessao['id'], $nova, 0);
    $eraObrigatorio = $sessao['alterar_palavra_passe'];
    Sessao::atualizar(['alterar_palavra_passe' => false]);
    Sessao::flash('sucesso', 'Palavra-passe alterada com sucesso.');
    $this->redirect($eraObrigatorio ? '/artigo' : '/perfil');
  }

  private function obterAtual() {
    $Utilizadores = $this->model('Utilizadores');
    return $Utilizadores::findUtilizadorById(Sessao::utilizador()['id'])[0];
  }

  private function mostrar(array $erros, array $valores = []) {
    $utilizador = $this->obterAtual();
    unset($utilizador['palavra_passe']);
    $this->view('perfil/index', [
      'titulo' => 'O meu perfil',
      'perfil' => array_merge($utilizador, $valores),
      'erros' => $erros,
      'obrigatorio' => Sessao::utilizador()['alterar_palavra_passe'],
      'classe_conteudo' => 'conteudo-estreito'
    ]);
  }
}
?>
