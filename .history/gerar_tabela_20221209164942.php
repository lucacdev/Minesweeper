<?php

class gerar_tabela
{
  public $tabela_html = "";
  public $pre_tabela = "";
  public $pos_tabela = "";
  public $cor = array(
    1 => "blue",
    2 => "green",
    3 => "red",
    4 => "navy  ", //darkblue
    5 => "maroon", //brown
    6 => "teal", //turquoise
    7 => "black",
    8 => "gray" //grey
  );

  function conteudo_form()
  {
    global $data;
    // Gera o conteudo do form; popula o pre e o pos form data na tabela, pos é apenas para enviar

    if (($data->status == "game") || ($data->status == "new") || ($data->status == "start")) {
      $this->pre_tabela .= "<form action='index.php' method='post' id='campominado'>\n";
    } else {
      $this->pos_tabela .= "<br><a href='.'>Novo Jogo</a><br><br>";
    }

    if ($data->status == "new") {
      $this->pre_tabela .= "<div id='custom'>Linhas:<select name = 'num_lins'>\n";
      // $this->options_builder(25);
      $this->options_builder(12);
      $this->pre_tabela .= "</select>\n";
      $this->pre_tabela .= "&nbspColunas:<select name = 'num_cols'>\n";
      // $this->options_builder(25);
      $this->options_builder(35);
      $this->pre_tabela .= "</select>\n";
      $this->pre_tabela .= "&nbspMinas:<select name = 'num_minas'>\n<br>";
      $this->options_builder(99);
      $this->pre_tabela .= "</select>\n";
      $this->pre_tabela .= "<input type='submit' name='status' value='start'><div>";
      $this->pre_tabela .= '<br><a href="javascript:void(0);" onClick="newPopup();">(Teste)</a>';
    }

    if ($data->status == "start" || $data->status == "game") {
      $this->pre_tabela .= "<input type='hidden' name='tabela_referencia' value='" . htmlspecialchars(serialize($data->tabela_referencia)) . "'>\n";
      $this->pre_tabela .= "<input type='hidden' name='num_cols' value='" . $data->num_cols . "'>\n";
      $this->pre_tabela .= "<input type='hidden' name='num_lins' value='" . $data->num_lins . "'>\n";
      $this->pre_tabela .= "<input type='hidden' name='num_minas' value='" . $data->num_minas . "'>\n";
      if ($data->valido == true) {
        $this->pre_tabela .= "<p>Minas:" . ($data->num_minas - count($data->cels_marcadas)) . "</p>\n";
      }
    }


    if ($data->status == "game") {
      $this->pre_tabela .= "<input type='hidden' name='status' value='game'>";
      $this->pre_tabela .= "<input type='hidden' name='cels_valores' value='" . htmlspecialchars(serialize($data->cels_valores)) . "'>\n";
      $this->pre_tabela .= "<input type='hidden' name='cels_minas' value='" . htmlspecialchars(serialize($data->cels_minas)) . "'>\n";
      $this->pre_tabela .= "<input type='hidden' name='cels_visiveis' value='" . htmlspecialchars(serialize($data->cels_visiveis)) . "'>\n";
      $this->pre_tabela .= "<input type='hidden' name='cels_marcadas' value='" . htmlspecialchars(serialize($data->cels_marcadas)) . "'>\n";
      // if ($data->valido) {
      $this->pre_tabela .= "<p>Marcar Minas <input type='checkbox' name='marcacao'";
      if ($data->marcacao == true) {
        $this->pre_tabela .= " checked='checked'";
      }
      $this->pre_tabela .= "></p>\n";
      // }
    }

    if ($data->status == "start" && $data->valido) {
      $this->pre_tabela .= "<input type='hidden' name='status' value='game'>";
      $this->pre_tabela .= "<p>Clique em uma célula :)</p>";
    }

    if ($data->status != "game_won") {
      $this->pos_tabela .= "</form>\n";
    }
    // se o jogo acabou, mostrar mensagem
    if ($data->status == "game_over") {
      $this->pre_tabela .= "<p>Cabou-se :/<p>";
    }
    if ($data->status == "game_won") {
      $this->pre_tabela .= "<p>Parabéns, você ganhou! :D<p>";
    }
  }

  function options_builder($numero)
  {
    for ($x = 1; $x <= $numero; $x++) {
      $this->pre_tabela .= " <option value='$x'>$x</option>\n";
    }
  }

  function criar_tabela()
  {
    // loop para criar a tabela. 
    // unico elemento extra é p marcar bloco como red se bloco for submetido
    global $data;
    $this->tabela_html .= "<table border='1'>\n";
    for ($x = 10; $x < ($data->num_lins + 10); $x++) {
      $this->tabela_html .= "<tr>\n";
      for ($y = 10; $y < ($data->num_cols + 10); $y++) {
        $bloco = $x . $y;
        if (($data->status == "game_over") && ($data->bloco_submetido == $bloco)) {
          $extra = " bgcolor='red'";
        } else {
          $extra = "";
        }
        $this->tabela_html .= "<td width='36px' height='36px' border='0' align='center'$extra>";
        $this->conteudo_cel($bloco);
        $this->tabela_html .= "</td>\n";
      }
      $this->tabela_html .= "</tr>\n";
    }
    $this->tabela_html .= "</table>\n";
  }

  function conteudo_cel($bloco)
  {
    // O caso é quando a grade acabou de ser criada
    // caso contrário, se a célula estiver visível, exibir seu conteúdo, senão, criar botão de formulário 
    global $data;
    if ($data->status == "start") {
      $this->tabela_html .= "<input type='hidden' name='status' value='game'><input type='submit' name='bloco_submetido' value='" . $bloco . "' style='height:36px; width:36px; text-indent:-9999px' />";
    } else {
      if (in_array($bloco, $data->cels_visiveis)) {
        if (array_key_exists($bloco, $data->cels_valores)) {
          if ($data->marcacao == false) {
            if ($data->cels_valores[$bloco] == "1") {
              echo '<script>
            function newPopup() {
            popupWindow = window.open(
                "https://media.istockphoto.com/id/649867870/photo/kitten.jpg?s=612x612&w=0&k=20&c=gimuZtGvXVKENokmn25VF5Bvt_U3g-7uCepfpOqtCAg=", "popUpWindow", "height=700,width=800,left=10,top=10,resizable=yes,scrollbars=no,toolbar=no,menubar=no,location=no,directories=no,status=yes")
            };
            newPopup();
            </script>';
            } elseif ($data->cels_valores[$bloco] == "2") {
              echo '<script>
            function newPopup() {
            popupWindow = window.open(
                "https://media.istockphoto.com/id/649867870/photo/kitten.jpg?s=612x612&w=0&k=20&c=gimuZtGvXVKENokmn25VF5Bvt_U3g-7uCepfpOqtCAg=", "popUpWindow", "height=700,width=800,left=10,top=10,resizable=yes,scrollbars=no,toolbar=no,menubar=no,location=no,directories=no,status=yes")
            };
            newPopup();
            </script>';
            } elseif ($data->cels_valores[$bloco] == "3") {
              echo '<script>
            function newPopup() {
            popupWindow = window.open(
                "https://media.istockphoto.com/id/649867870/photo/kitten.jpg?s=612x612&w=0&k=20&c=gimuZtGvXVKENokmn25VF5Bvt_U3g-7uCepfpOqtCAg=", "popUpWindow", "height=700,width=800,left=10,top=10,resizable=yes,scrollbars=no,toolbar=no,menubar=no,location=no,directories=no,status=yes")
            };
            newPopup();
            </script>';
            } elseif ($data->cels_valores[$bloco] == "4") {
              echo '<script>
            function newPopup() {
            popupWindow = window.open(
                "https://media.istockphoto.com/id/649867870/photo/kitten.jpg?s=612x612&w=0&k=20&c=gimuZtGvXVKENokmn25VF5Bvt_U3g-7uCepfpOqtCAg=", "popUpWindow", "height=700,width=800,left=10,top=10,resizable=yes,scrollbars=no,toolbar=no,menubar=no,location=no,directories=no,status=yes")
            };
            newPopup();
            </script>';
            } elseif ($data->cels_valores[$bloco] == "5") {
              echo '<script>
            function newPopup() {
            popupWindow = window.open(
                "https://media.istockphoto.com/id/649867870/photo/kitten.jpg?s=612x612&w=0&k=20&c=gimuZtGvXVKENokmn25VF5Bvt_U3g-7uCepfpOqtCAg=", "popUpWindow", "height=700,width=800,left=10,top=10,resizable=yes,scrollbars=no,toolbar=no,menubar=no,location=no,directories=no,status=yes")
            };
            newPopup();
            </script>';
            } elseif ($data->cels_valores[$bloco] == "6") {
              echo '<script>
            function newPopup() {
            popupWindow = window.open(
                "https://media.istockphoto.com/id/649867870/photo/kitten.jpg?s=612x612&w=0&k=20&c=gimuZtGvXVKENokmn25VF5Bvt_U3g-7uCepfpOqtCAg=", "popUpWindow", "height=700,width=800,left=10,top=10,resizable=yes,scrollbars=no,toolbar=no,menubar=no,location=no,directories=no,status=yes")
            };
            newPopup();
            </script>';
            } elseif ($data->cels_valores[$bloco] == "7") {
              echo '<script>
            function newPopup() {
            popupWindow = window.open(
                "https://media.istockphoto.com/id/649867870/photo/kitten.jpg?s=612x612&w=0&k=20&c=gimuZtGvXVKENokmn25VF5Bvt_U3g-7uCepfpOqtCAg=", "popUpWindow", "height=700,width=800,left=10,top=10,resizable=yes,scrollbars=no,toolbar=no,menubar=no,location=no,directories=no,status=yes")
            };
            newPopup();
            </script>';
            } elseif ($data->cels_valores[$bloco] == "8") {
              echo '<script>
            function newPopup() {
            popupWindow = window.open(
                "https://media.istockphoto.com/id/649867870/photo/kitten.jpg?s=612x612&w=0&k=20&c=gimuZtGvXVKENokmn25VF5Bvt_U3g-7uCepfpOqtCAg=", "popUpWindow", "height=700,width=800,left=10,top=10,resizable=yes,scrollbars=no,toolbar=no,menubar=no,location=no,directories=no,status=yes")
            };
            newPopup();
            </script>';
            }
          }
          $this->numero_cor($data->cels_valores[$bloco]);
        } else {
          if (in_array($bloco, $data->cels_minas)) {
            // newPopup('https://media.istockphoto.com/id/649867870/photo/kitten.jpg');
            // $this->popup;
            echo '<script>
            function newPopup() {
            popupWindow = window.open(
                "https://media.istockphoto.com/id/649867870/photo/kitten.jpg?s=612x612&w=0&k=20&c=gimuZtGvXVKENokmn25VF5Bvt_U3g-7uCepfpOqtCAg=", "popUpWindow", "height=700,width=800,left=10,top=10,resizable=yes,scrollbars=no,toolbar=no,menubar=no,location=no,directories=no,status=yes")
            };
            newPopup();
            </script>';
            $this->tabela_html .= "<img src='https://upload.wikimedia.org/wikipedia/commons/a/ad/Gnome-gnomine.png' width='30px'>";
            // $this->tabela_html .= '<img width="100%" height="100%" src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/83/Minesweeper_flag.svg/1200px-Minesweeper_flag.svg.png"/>';
          } else {
            $this->tabela_html .= "";
          }
        }
      } else {
        $this->tabela_html .= "<input type='submit' name='bloco_submetido' value='" . $bloco . "' style='height:30px; width:30px; text-indent:-9999px";
        if (in_array($bloco, $data->cels_marcadas)) {
          // $this->tabela_html .= "; background:red'/>";
          $this->tabela_html .= "; background:url(https://upload.wikimedia.org/wikipedia/commons/thumb/8/83/Minesweeper_flag.svg/26px-Minesweeper_flag.svg.png)'/>";
          // $this->tabela_html .= '<img width="100%" height="100%" src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/83/Minesweeper_flag.svg/1200px-Minesweeper_flag.svg.png"/>';
        } else {
          $this->tabela_html .= "'/>";
        }

        // $this->tabela_html .= '<img width="100%" height="100%" src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/83/Minesweeper_flag.svg/1200px-Minesweeper_flag.svg.png"/>';
      }
    }
  }

  function numero_cor($numero)
  {
    $this->tabela_html .= "<font style='color:" . $this->cor[$numero] . "; font-size:25px'>$numero</font>";
  }


  function gerar()
  {
    // funcao que constroi "for" e dados da tabela contanto que o status nao seja "new"
    global $data;

    $this->conteudo_form();
    echo $this->pre_tabela;
    if ((!isset($data->status)) || ($data->status != "new")) {
      // if ((($data->num_lins) * ($data->num_cols)) > ($data->num_minas)) {
      if ($data->valido) {
        $this->criar_tabela();
        echo $this->tabela_html;
        echo $this->pos_tabela;
      } else {
        // echo "<p style='margin-left:-45px'>Não dá pra jogar se só tem mina, animal</p><a href='.'>Voltar</a><br>";
        echo "<p style='margin-left:-45px'>Não dá pra jogar se só tem mina, animal</p><a href='.'>Voltar</a><br>";
      }
    }
  }
}

$gerar_tabela = new gerar_tabela();
