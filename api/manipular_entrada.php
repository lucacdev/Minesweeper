
<?php

class manipular_dados
{
  public $bloco_submetido;           // bloco que esta submetido a cada clique
  public $cels_marcadas = array();           // Array de tabela_referencias que estao marcadas
  public $cels_minas = array();           // Array de cels com bomba
  public $cels_valores = array();           // valores em cada celula, apenas para mostrar
  public $cels_visiveis = array();           // Array de tabela_referencias que sao 'visiveis' 
  public $marcacao;           // Indica se "marcar" está checado
  public $status = "";           // Especifica o status - new/start/game/game_won/game_over
  public $num_cols;           // Numero de colunas na tabela
  public $num_lins;           //  Numero de linhas na tabela
  public $num_minas;           // Numero de minas na tabela
  public $tabela_referencia = array();           // Array de todas as grid referencias
  public $valido = true;           // Indica se os inputs sao validos

  function __construct()
  {
    // status = game    jogo esta sendo jogado
    // se nenhuma cel for postada, gera grade e entao joga com celula submetida
    // se cels sao postadas, determina variaveis & jogajogo
    // status = new    nada é postado feijoada

    if (isset($_POST['status'])) {
      $this->status = $_POST['status'];
      if ($this->status == "game") {
        $this->bloco_submetido = $_POST['bloco_submetido'];
        $this->num_lins = $_POST['num_lins'];
        $this->num_cols = $_POST['num_cols'];
        $this->num_minas = $_POST['num_minas'];

        if (!isset($_POST['cels_valores'])) {
          $this->tabela_referencia = unserialize($_POST['tabela_referencia']);
          $this->gerar_valores();
          $this->jogar_jogo();
        } else {
          $this->tabela_referencia = unserialize($_POST['tabela_referencia']);
          $this->cels_valores = unserialize($_POST['cels_valores']);
          $this->cels_minas = unserialize($_POST['cels_minas']);
          $this->cels_visiveis = unserialize($_POST['cels_visiveis']);
          $this->cels_marcadas = unserialize($_POST['cels_marcadas']);
          if (isset($_POST['marcacao'])) {
            $this->marcacao = $_POST['marcacao'];
          } else {
            $this->marcacao = false;
          }
          $this->jogar_jogo();
        }
      }
      if ($this->status == "start") {
        $this->gerar_grade();
      }
    } else {
      $this->status = "new";
    }
  }

  function gerar_grade()
  {
    $this->num_lins = $_POST['num_lins'];
    $this->num_cols = $_POST['num_cols'];
    $this->num_minas = $_POST['num_minas'];

    if ((($this->num_lins) * ($this->num_cols)) <= ($this->num_minas)) {
      $this->valido = false;
    } else {
      // gera o array grade de referencia
      for ($x = 10; $x < ($this->num_lins + 10); $x++) {
        for ($y = 10; $y < ($this->num_cols + 10); $y++) {
          array_push($this->tabela_referencia, $x . $y);
        }
      }
    }
  }

  function gerar_valores()
  {
    // Gera cels_minas e cels_valores - precisa saber as cels_minas antes das cels_valores
    // cels_minas sao criadas usando as grid references, menos cels clicadas entao recortadas
    $this->cels_minas = $this->tabela_referencia;
    $key = array_search($this->bloco_submetido, $this->cels_minas);
    unset($this->cels_minas[$key]);
    shuffle($this->cels_minas);
    $this->cels_minas = array_values(array_slice($this->cels_minas, 0, $this->num_minas));

    // Calcula quantas minas existem ao redor da celula, se nao for uma mina
    // Se nenhuma, não define o valor

    foreach ($this->tabela_referencia as $celula) {
      if (!in_array($celula, $this->cels_minas)) {
        $cels_a_checar = array();
        $cels_a_checar = $this->get_cels_ao_redor($celula);
        $numero = count(array_intersect($cels_a_checar, $this->cels_minas));
        if ($numero > 0) {
          $this->cels_valores[$celula] = $numero;
        }
      }
    }
    // No primeiro round, faz o bloco submetido visivel
    $this->processar_cel($this->bloco_submetido);
  }

  function jogar_jogo()
  {
    // Se marcacao é true, rodar processar_cel e checar se o jogo esta ganho
    // ou entao, se a celula clicada
    // se o submetido nao for marcado, rodar uma funcao de clique baseado se for mina, numero ou branco

    if ($this->marcacao == true) {
      $this->processar_cel($this->bloco_submetido);
      $this->jogo_esta_ganhado();
      return;
    } else {
      if (!in_array($this->bloco_submetido, $this->cels_marcadas)) {
        if (in_array($this->bloco_submetido, $this->cels_minas)) {
          $this->clica_mina();
          return;
        } elseif (isset($this->cels_valores[$this->bloco_submetido])) {
          $this->clica_numero();
          return;
        } else {
          $this->clica_branco();
          return;
        }
      }
    }
  }

  function clica_mina()
  {
    $this->game_over();
  }

  function clica_numero()
  {
    // se uma celula com numero é clicado, apenas essa se faz visivel
    $this->processar_cel($this->bloco_submetido);
  }

  function clica_branco()
  {
    // se uma celula branca é clicada, todo bloco ao redor se faz visivel.
    // repetindo entao cada vez que uma cel branca se faz visivel.
    // Para reduzir o num de checks, cada vez que uma cel é marcada, vai para um array
    // para que nao seja marcada novamente.

    $cels_a_checar = $this->get_cels_ao_redor($this->bloco_submetido);
    $cels_marcadas = array();
    $this->processar_cel($this->bloco_submetido);
    $x = 1;
    while ($x > 0) {
      $x = 0;
      foreach ($cels_a_checar as $celula) {
        $this->processar_cel($celula);
        // se a celula for vazia e nao estiver preenchida, é add para as celulas marcadas
        // add as celulas brancas ao redor para o array para serem marcadas.
        if ((!isset($this->cels_valores[$celula])) && (!in_array($celula, $cels_marcadas))) {
          array_push($cels_marcadas, $celula);
          $cels_a_checar = array_merge($cels_a_checar, $this->get_cels_ao_redor($celula));
          array_diff($cels_a_checar, array($celula));
          $x++;
        }
      }
    }
  }

  function game_over()
  {
    // Faz todas as celulas visiveis e troca status para game_over
    $this->cels_visiveis = $this->tabela_referencia;
    $this->status = "game_over";
  }

  function get_cels_ao_redor($celula)
  {
    // Gera array de todas cels ao redor da selecionada, certificando que
    // os valores sao apenas esses no array "grid reference".
    // também remove as células marcadas para que não seja incluída uma célula já marcada.
    $cels_a_checar = array();
    array_push($cels_a_checar, substr($celula, 0, 2) - 1 . substr($celula, 2, 2) - 1);
    array_push($cels_a_checar, substr($celula, 0, 2) - 1 . substr($celula, 2, 2));
    array_push($cels_a_checar, substr($celula, 0, 2) - 1 . substr($celula, 2, 2) + 1);
    array_push($cels_a_checar, substr($celula, 0, 2) . substr($celula, 2, 2) - 1);
    array_push($cels_a_checar, substr($celula, 0, 2) . substr($celula, 2, 2) + 1);
    array_push($cels_a_checar, substr($celula, 0, 2) + 1 . substr($celula, 2, 2) - 1);
    array_push($cels_a_checar, substr($celula, 0, 2) + 1 . substr($celula, 2, 2));
    array_push($cels_a_checar, substr($celula, 0, 2) + 1 . substr($celula, 2, 2) + 1);
    $cels_a_checar = array_intersect($cels_a_checar, $this->tabela_referencia);
    $cels_a_checar = array_diff($cels_a_checar, $this->cels_marcadas);
    return $cels_a_checar;
  }

  function processar_cel($celula)
  {
    // funcao geral para processar uma celula submitada.

    if (($celula == $this->bloco_submetido) && ($this->marcacao == true) && (!in_array($this->bloco_submetido, $this->cels_marcadas))) {
      array_push($this->cels_marcadas, $this->bloco_submetido);
      return;
    } elseif (($celula == $this->bloco_submetido) && ($this->marcacao == true) && (in_array($this->bloco_submetido, $this->cels_marcadas))) {
      $key = array_search($this->bloco_submetido, $this->cels_marcadas);
      unset($this->cels_marcadas[$key]);
      return;
    }

    // se nao esta nas cels marcadas, faz visivel entao checa se ganhou o jogo.
    if (!in_array($celula, $this->cels_marcadas)) {
      array_push($this->cels_visiveis, $celula);
      $this->cels_visiveis = array_unique($this->cels_visiveis);
      $this->jogo_esta_ganhado();
    }
  }

  function jogo_esta_ganhado()
  {
    // checa se o jogo está ganho
    if ((isset($_POST)) && ((count($this->tabela_referencia) - count($this->cels_visiveis)) == count($this->cels_minas))) {
      $this->status = "game_won";
    }
  }
}

$data = new manipular_dados();
