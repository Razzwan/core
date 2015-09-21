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

    <link rel="stylesheet" type="text/css" media="screen" href="/css/dev.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="/css/site.css" />

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>

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
    <div class="error">
        <?=$this->view;?>
    </div>
</div>

<footer>
    <p>&copy; Razzwan <span class="logo">LIW</span> 2015</p>
</footer>


<div id="tooltip"></div>


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="/js/jquery.js" type="text/javascript" ></script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/jquery-ui.min.js" type="text/javascript" ></script>
<script src="/js/dev.js" type="text/javascript" ></script>
<script src="/js/js.js" type="text/javascript" ></script>
<script src="/js/captcha.js" type="text/javascript" ></script>

<div id="develop_button">Time:<?=' ' . sprintf("%G",(sprintf("%d", (microtime(true)-$_SERVER["REQUEST_TIME_FLOAT"])*100000))/100) . 'ms';?></div>
</body>
</html>
