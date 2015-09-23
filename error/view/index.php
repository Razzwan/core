<?php
/**
 * @var $this object liw\core\base\View
 */
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Ошибка!</title>

    <!-- Bootstrap core CSS -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <style>
        @font-face {
            font-family: "Open Sans";
            src: url(/fonts/OpenSans.ttf);
        }
        html,
        body {
            display: table;
            font-family: "Open Sans", sans-serif;
            font-size: 14px;
            height: 100%;
            margin: 0;
            padding: 0;
            width: 100%;
        }
        #main_wrapper {
            margin-top: 75px;
            text-align: center;
        }
        @media (min-width: 920px) {
            #main_wrapper {
                display: table-cell;
                vertical-align: middle;
            }
            .jumbotron {
                text-align: center;
                display: inline-block;
            }
        }

    </style>
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container">
            <div id="navbar" class="navbar-collapse">
                <div class="navbar-form navbar-right">
                    <div class="form-group">
                        <a href="/" class="btn btn-success">Вернуться на главную</a>
                    </div>
                </div>
            </div><!--/.navbar-collapse -->
        </div>
    </nav>

    <div id="main_wrapper">
        <div class="jumbotron alert alert-danger">
            <?=$this->view;?>
        </div>
    </div>

    <script src="/js/jquery.min.js" type="text/javascript" ></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/dev.js" type="text/javascript" ></script>

</body>
</html>
