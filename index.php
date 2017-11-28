<?php
session_start();

if( !isset( $_SESSION['uid'] ) ){
    header('Location: /prestamoswilson/login/');
    die;
}

?>
<!DOCTYPE html>
<html lang="en" ng-app="prestamosModule">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Prestamos</title>

	<base href="/prestamoswilson/">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta name="description" content="Sistema de Contro de Prestamos">
	<meta name="author" content="Wilson Alfonzo Medina Chipana">


	<!-- Librerias CSS-->
	<link rel="stylesheet" href="app/css/loading-bar.min.css">
	<link rel="stylesheet" href="app/css/bootstrap.min.css">
	<link rel="stylesheet" href="app/css/font-awesome.css">
	<link rel="stylesheet" href="app/css/ionicons.min.css">

	<link rel="stylesheet" href="app/css/AdminLTE.css">
	<link rel="stylesheet" href="app/css/skin-red.min.css">
    <link rel="stylesheet" href="app/css/sweetalert.css">
	<!-- Fin Librerias CSS-->
    <link rel="stylesheet" href="app/css/animate.css">
	<link href="app/css/styles.css" rel="stylesheet">
	
	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
</head>
<body class="hold-transition skin-red sidebar-mini" ng-controller="mainCtrl" ng-init="init();">


	<!--<div>
		<nav ng-include="'public/main/views/nav.view.html'"></nav>
		<section ng-view></section>
	</div>-->
	<div class="wrapper">

        <!-- Main Header -->
        <header class="main-header">

            <!-- Logo -->
            <a ng-href="/prestamoswilson" class="logo">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class="logo-mini"><b>{{ config.iniciales[0] }}</b>{{ config.iniciales | quitarletra }}</span>
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg"><b> {{ config.aplicativo }} </b>{{ config.iniciales }}</span>
            </a>

            <!-- Header Navbar -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                </a>
                <!-- Navbar Right Menu -->
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">

                        <!-- Messages: style can be found in dropdown.less
                        <li class="dropdown messages-menu"
                                ng-include="'public/main/views/message.view.html'">
                        </li>
                        messages-menu

                        Notifications Menu
                        <li class="dropdown notifications-menu"
                                ng-include="'public/main/views/notifications.view.html'">
                        </li>-->

                        <!-- User Account Menu -->
                        <li class="dropdown user user-menu"
                                ng-include="'public/main/views/user.view.html'">
                        </li>

                    </ul>
                </div>
            </nav>
        </header>
        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar"
            ng-include="'public/main/views/menu.view.html'">
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">

                <h1>
                    <span>{{ titulo }}</span>  
                    <small>{{ subtitulo }}</small>
                </h1>

            </section>

            <!-- Main content -->
            <section class="content creditos" ng-view>

                <!-- Your Page Content Here -->

            </section><!-- /.content -->
        </div><!-- /.content-wrapper -->

        <!-- Main Footer -->
        <footer class="main-footer">
            <!-- To the right -->
            <div class="pull-right hidden-xs">
                {{ config.version }}
            </div>
            <!-- Default to the left -->
            <strong>Copyright &copy; {{ config.anio }} 
                    <a href="{{ config.web }}" target="blank">Compañía</a>.
            </strong> Todos los derechos reservados.
        </footer>

        <div ng-include="'public/main/views/userModal.view.html'"></div>
    </div><!-- ./wrapper -->
	
	<!-- Todas las librerias externas -->
	<script src="app/lib/jquery.min.js"></script>
	<script src="app/lib/angular.min.js"></script>
	<script src="app/lib/angular-resource.min.js"></script>
	<script src="app/lib/angular-route.min.js "></script>
	<script src="app/lib/angular-touch.min.js"></script>
	<script src="app/lib/loading-bar.min.js"></script>
	<script src="app/lib/jcs-auto-validate.js"></script>
	<script src="app/lib/bootstrap.min.js"></script>
	<script src="app/lib/AdminLTEapp.js"></script>
    <script src="app/lib/sweetalert.min.js"></script>
	<!--<script src="js/ie10-viewport-bug-workaround.js"></script>-->
	<!-- Fin todas las librerias externas -->

	<!-- Module -->
	<script src="public/clients/module.js"></script>
    <script src="public/give/module.js"></script>
    <script src="public/graphics/module.js"></script>
    <script src="public/reports/module.js"></script>
    <script src="public/user/module.js"></script>
    <!-- Fin Module -->
    
    <!-- Route -->
    <script src="public/clients/route.js"></script>
    <script src="public/give/route.js"></script>
    <script src="public/graphics/route.js"></script>
    <script src="public/reports/route.js"></script>
    <script src="public/user/route.js"></script>
    <!-- Fin Route -->
    
    <!-- Service -->
    <script src="public/clients/service.js"></script>
    <script src="public/user/service.js"></script>
    <script src="public/give/service.js"></script>
    <script src="public/reports/service.js"></script>
    <!--
    <script src="public/graphics/service.js"></script>
    -->
    <!-- Fin Service -->

    <!-- Controllers -->
    <script src="public/clients/controller.js"></script>
    <script src="public/give/controller.js"></script>
    <script src="public/graphics/controller.js"></script>
    <script src="public/reports/controller.js"></script>
    <script src="public/user/controller.js"></script>
    <!-- Fin Controllers -->


    <script src="public/app.js"></script>

</body>
</html>
