<html>

<head>
    <title>Campo Minado</title>
    <style>
        * {
            font-family: 'Press Start 2P', cursive;
            /* justify-content: center; */
            /* margin-left: 10%; */
        }

        html,
        body {
            margin-top: 40%;
            height: 100%;
        }

        html {
            display: table;
            margin: auto;
        }

        body {
            display: table-cell;
            /* vertical-align: middle; */
            padding-top: 72px;
        }

        p {
            font-size: 20px;
        }

        table {
            margin-left: auto;
            margin-right: auto;
        }

        #custom {
            font-size: 20px;
            margin-left: -60px;
        }

        @import url('https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap');
    </style>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">

    <script>
        function newPopup() {
            popupWindow = window.open(
                'https://media.istockphoto.com/id/157030584/vector/thumb-up-emoticon.jpg?s=612x612&w=0&k=20&c=GGl4NM_6_BzvJxLSl7uCDF4Vlo_zHGZVmmqOBIewgKg=', 'popUpWindow', 'height=700,width=800,left=10,top=10,resizable=yes,scrollbars=no,toolbar=no,menubar=no,location=no,directories=no,status=yes')
        }
    </script>
</head>

<body>

    <!-- <body style="justify-content: space-around;"> -->
    <h1 style="font-size: 40px; margin-left: 70px;">Campo&nbspMinado</h1>
    <?php
    $output = "";
    include('manipular_entrada.php');
    include('gerar_tabela.php');
    $gerar_tabela->gerar();
    ?>
    <!-- <br> -->
    <!-- <a href="javascript:void(0);" onClick="newPopup();">(Teste)</a> -->
</body>

</html>