<?php
/* ======================================================================
Esta classe é responsável por:
> Obtenção do URL
> Divide-o pelo /
> Cria um array (pela divisão do URL pelo /)
  - Índice 0: controller a instanciar
  - Índice 1: método a invocar (do controller instanciado)
  - Índices restantes: parâmetros do método
====================================================================== */
namespace app\core;
class App {
  private $controller;
  private $method;
  private $params;
  private $URLArray;
  private $startIndexFromUrl;
  private $pageNotFound;

  public function __construct() {
    $this->controller = 'Home';
    $this->method = 'index';
    $this->params = [];
    $this->startIndexFromUrl = 1; // NO MEU AMBIENTE DE DESENVOLVIMENTO!
    $this->pageNotFound = '';     // boolean
    $this->parseURL();
    $this->controlarAcesso();
    $this->setController();
    $this->setMethodFromUrl();
    $this->setParamsFromUrl();
    
    // Invocação de um método de uma classe de forma dinâmica
    // ================================================================
    // chama um método de uma classe passando os parâmetros
    // call_user_func_array(array($classInstance, $methodName), array($arg1, $arg2, $arg3));
    call_user_func_array([$this->controller, $this->method], $this->params);
  }

  /**
  * Método responsável pela manipulação do URL (ignora o domínio do site)
  *
  */
  private function parseURL() {
    // só o caminho: a query string (ex.: ?q=faca&pagina=2) é lida pelos controladores em $_GET
    $this->URLArray = explode('/', substr(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), 1));
    // print_r($this->URLArray);
    // $_SERVER['REQUEST_URI'] - Exemplo:
    // para: http://www.ipvc.pt/ecgm/ano3.php?uc=pw
    // devolve: /ecgm/ano3.php?uc=pw
  }

  /**
  * Controlo de acesso, feito antes de instanciar o controlador:
  * > sem sessão iniciada só a página de login (controlador Auth) está disponível
  * > quem tem uma palavra-passe temporária só pode alterá-la (controlador Perfil)
  * > a gestão de utilizadores é reservada a administradores
  * > todos os pedidos POST têm de trazer o token CSRF da sessão
  */
  private function controlarAcesso() {
    $nome = ucfirst(strtolower($this->URLArray[$this->startIndexFromUrl] ?? ''));
    $utilizador = Sessao::utilizador();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !Sessao::csrfValido($_POST['csrf'] ?? null)) {
      http_response_code(400);
      exit('Pedido inválido (token de segurança expirado). Volte atrás, recarregue a página e tente novamente.');
    }

    if ($utilizador === null) {
      if ($nome !== 'Auth') {
        $this->redirecionar('/auth/login');
      }
      return;
    }

    if ($utilizador['alterar_palavra_passe'] && !in_array($nome, ['Perfil', 'Auth'])) {
      $this->redirecionar('/perfil');
    }

    if ($nome === 'Utilizador' && !Sessao::eAdmin()) {
      Sessao::flash('erro', 'Apenas administradores podem gerir utilizadores.');
      $this->redirecionar('/artigo');
    }
  }

  private function redirecionar(string $caminho) {
    header('Location: ' . URL_BASE . $caminho);
    exit;
  }

  /**
  * Método que instancia um controlador.
  * Este método verifica se o array possui dados na posição 0 -> CONTROLADOR
  * e define-o
  *
  */
  private function setController() {
    $controller = $this->URLArray[$this->startIndexFromUrl];
    if (isset($controller) && !empty($controller)) {
      if (file_exists('app/controllers/' . ucfirst($controller) . '.php')) {
        $this->controller = ucfirst($controller);
      } else {
        $this->pageNotFound = true;
      }
    }
    require_once('app/controllers/' . $this->controller . '.php');
    $this->controller = new $this->controller();
  }

  /**
  * Este método verifica se o array possui dados na posição 1 -> MÉTODO
  * e define-o
  *
  */
  private function setMethodFromUrl() {
    $url = $this->URLArray;
    $startIndex = $this->startIndexFromUrl + 1;
    
    if ($this->pageNotFound) {
      // controlador inexistente (ex.: /xyz): página 404, mesmo sem método no URL
      $this->method = 'pageNotFound';
    } elseif (!empty($url[$startIndex]) && isset($url[$startIndex])) {
      // is_callable (e não method_exists): métodos privados dos controladores não podem ser abertos pelo URL
      if (is_callable([$this->controller, $url[$startIndex]]) && !$this->pageNotFound) {
        $this->method = $url[$startIndex];
      } else {
        $this->method = 'pageNotFound';
      }
    }
  }

  /**
  * Este método verifica se o array possui a quantidade de elementos maior do que 2 -> PARÂMETROS
  * e define-o
  *
  */
  private function setParamsFromUrl() {
    $url = $this->URLArray;
    $startIndex = $this->startIndexFromUrl + 2;
    if (count($url) > $startIndex) {
      $this->params = array_slice($url, $startIndex);
    }
  }
  
}

/*
NOTAS:
=======================================================================================================
Exemplos de URIs (em testes locais):
http://localhost
http://localhost/modocacador/artigo/ - invoca método index() do controlador Artigo (listagem de todos os artigos)
http://localhost/modocacador/artigo/get/5 - invoca método get() do controlador Artigo (listagem de um artigo)


Método parseURL:
> parse = dividir um determinado dado em partes (que podem ser facilmente armazenadas e/ou manipuladas)
> $_SERVER['REQUEST_URI']:
  devolve o endereço (URL) do ficheiro PHP em execução, incluíndo a informação 
  do caminho e a query string (parâmetros). É incluído o slash inicial.
*/
?>