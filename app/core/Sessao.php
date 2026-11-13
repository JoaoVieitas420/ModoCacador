<?php
namespace app\core;

/**
* Classe responsável pela sessão do utilizador:
* > autenticação (quem tem sessão iniciada)
* > token CSRF dos formulários
* > mensagens "flash" (mostradas uma única vez, depois de um redirecionamento)
* > animação do nome do site, feita apenas uma vez por login
*/
class Sessao {

  /**
  * Inicia a sessão com cookies mais seguros
  */
  public static function iniciar() {
    if (session_status() === PHP_SESSION_NONE) {
      session_set_cookie_params([
        'lifetime' => 0,
        'path' => URL_BASE . '/',
        'httponly' => true,     // o JavaScript não consegue ler o cookie da sessão
        'samesite' => 'Lax'     // o cookie não é enviado em pedidos POST vindos de outros sites
      ]);
      session_start();
    }
  }

  /**
  * Devolve os dados do utilizador autenticado (ou null)
  *
  * @return array|null
  */
  public static function utilizador() {
    return $_SESSION['utilizador'] ?? null;
  }

  public static function eAdmin() {
    return !empty($_SESSION['utilizador']['admin']);
  }

  /**
  * Regista o utilizador na sessão depois de um login válido
  *
  * @param  array  $utilizador  registo da tabela utilizador
  */
  public static function entrar(array $utilizador) {
    session_regenerate_id(true); // novo id de sessão: evita "session fixation"
    $_SESSION['utilizador'] = [
      'id' => $utilizador['id_utilizador'],
      'nome' => $utilizador['nome'],
      'fotografia' => $utilizador['fotografia'],
      'admin' => (int) $utilizador['admin'] === 1,
      'alterar_palavra_passe' => (int) $utilizador['alterar_palavra_passe'] === 1
    ];
    $_SESSION['animar_logo'] = true;
  }

  /**
  * Atualiza um ou mais campos do utilizador guardado na sessão
  */
  public static function atualizar(array $campos) {
    if (isset($_SESSION['utilizador'])) {
      $_SESSION['utilizador'] = array_merge($_SESSION['utilizador'], $campos);
    }
  }

  public static function sair() {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
      $p = session_get_cookie_params();
      setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
  }

  /**
  * Token CSRF: um valor aleatório por sessão, enviado em todos os formulários POST
  */
  public static function csrfToken() {
    if (empty($_SESSION['csrf'])) {
      $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
  }

  public static function csrfValido($token) {
    return is_string($token) && !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
  }

  /**
  * Mensagens flash: guardadas antes de um redirecionamento e lidas (uma vez) na página seguinte
  */
  public static function flash(string $tipo, string $mensagem) {
    $_SESSION['flash'] = ['tipo' => $tipo, 'mensagem' => $mensagem];
  }

  public static function obterFlash() {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
  }

  /**
  * Devolve true apenas na primeira página depois do login
  */
  public static function animarLogo() {
    $animar = !empty($_SESSION['animar_logo']);
    unset($_SESSION['animar_logo']);
    return $animar;
  }
}
