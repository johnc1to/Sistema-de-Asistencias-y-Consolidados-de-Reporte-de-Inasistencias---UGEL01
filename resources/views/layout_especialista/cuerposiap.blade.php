<!doctype html>
<html lang="en">
<?php
$session = session()->get('siic01_admin');
?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>SIIC01</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
    <meta name="description" content="SIIC01">
    <meta name="msapplication-tap-highlight" content="no">
    /*inicio jmmj 16-06-2023*/
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    /*fin jmmj 16-06-2023*/
    <link href="{{asset('main.css')}}" rel="stylesheet">
    <script type="text/javascript" src="{{asset('assets/scripts/jquery-3.5.1.min.js')}}"></script>
    <!--
    =========================================================
    * ArchitectUI HTML Theme Dashboard - v1.0.0
    =========================================================
    * Product Page: https://dashboardpack.com
    * Copyright 2019 DashboardPack (https://dashboardpack.com)
    * Licensed under MIT (https://github.com/DashboardPack/architectui-html-theme-free/blob/master/LICENSE)
    =========================================================
    * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
    -->

</head>
<body>
    <div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
        <div class="app-header header-shadow">
            <div class="app-header__logo">
                <div class="" style="font-weight: bold;color:#000;font-size:24px;">SIIC01</div><!--logo-src-->
                <div class="header__pane ml-auto">
                    <div>
                        <button type="button" class="hamburger close-sidebar-btn hamburger--elastic" data-class="closed-sidebar">
                            <span class="hamburger-box">
                                <span class="hamburger-inner"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="app-header__mobile-menu">
                <div>
                    <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                        <span class="hamburger-box">
                            <span class="hamburger-inner"></span>
                        </span>
                    </button>
                </div>
            </div>
            <div class="app-header__menu">
                <span>
                    <button type="button" class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                        <span class="btn-icon-wrapper">
                            <i class="fa fa-ellipsis-v fa-w-6"></i>
                        </span>
                    </button>
                </span>
            </div>    <div class="app-header__content">
                <div class="app-header-left">
                    <!--
                    <div class="search-wrapper">
                        <div class="input-holder">
                            <input type="text" class="search-input" placeholder="Type to search">
                            <button class="search-icon"><span></span></button>
                        </div>
                        <button class="close"></button>
                    </div>
                    -->
                    <ul class="header-menu nav">
                    <!--
                        <li class="nav-item">
                            <a href="javascript:void(0);" class="nav-link">
                                <i class="nav-link-icon fa fa-database"> </i>
                                Statistics
                            </a>
                        </li>
                        <li class="btn-group nav-item">
                            <a href="javascript:void(0);" class="nav-link">
                                <i class="nav-link-icon fa fa-edit"></i>
                                Projects
                            </a>
                        </li>
                        <li class="dropdown nav-item">
                            <a href="javascript:void(0);" class="nav-link">
                                <i class="nav-link-icon fa fa-cog"></i>
                                Settings
                            </a>
                        </li>
                    -->
                    </ul>
                </div>
                <div class="app-header-right">
                    <div class="header-btn-lg pr-0">
                        <div class="widget-content p-0">
                            <div class="widget-content-wrapper">
                                <div class="widget-content-left">
                                    <div class="btn-group">
                                        <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="p-0 btn">

                                            <img width="42" class="rounded-circle" src="assets/images/avatars/user-4.png" alt="">
                                            <i class="fa fa-angle-down ml-2 opacity-8"></i>
                                        </a>
                                        <div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu dropdown-menu-right">
                                            <button type="button" tabindex="0" class="dropdown-item">User Account</button>
                                            <button type="button" tabindex="0" class="dropdown-item">Settings</button>
                                            <h6 tabindex="-1" class="dropdown-header">Header</h6>
                                            <button type="button" tabindex="0" class="dropdown-item">Actions</button>
                                            <div tabindex="-1" class="dropdown-divider"></div>
                                            <button type="button" tabindex="0" class="dropdown-item">Dividers</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="widget-content-left  ml-3 header-user-info">
                                    <div class="widget-heading">
                                        <?=$session['nombre']?>
                                    </div>
                                    <div class="widget-subheading">
                                        <?=$session['cargo']?>
                                    </div>
                                </div>
                                <div class="widget-content-right header-user-info ml-3">
                                    <!--
                                    <button type="button" class="btn-shadow p-1 btn btn-primary btn-sm show-toastr-example">
                                        <i class="fa text-white fa-calendar pr-1 pl-1"></i>
                                    </button>
                                    -->
                                </div>
                            </div>
                        </div>
                    </div>        </div>
            </div>
        </div>        <div class="ui-theme-settings">
            <button type="button" id="TooltipDemo" class="btn-open-options btn btn-warning">
                <i class="fa fa-cog fa-w-16 fa-spin fa-2x"></i>
            </button>
            <div class="theme-settings__inner">
                <div class="scrollbar-container">
                    <div class="theme-settings__options-wrapper">
                        <h3 class="themeoptions-heading">Opciones de personalización
                        </h3>
                        <div class="p-3">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <div class="widget-content p-0">
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left mr-3">
                                                <div class="switch has-switch switch-container-class" data-class="fixed-header">
                                                    <div class="switch-animate switch-on">
                                                        <input type="checkbox" checked data-toggle="toggle" data-onstyle="success">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="widget-content-left">
                                                <div class="widget-heading">Encabezado fijo
                                                </div>
                                                <div class="widget-subheading">¡Hace que la parte superior del encabezado sea fija, siempre visible!
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="widget-content p-0">
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left mr-3">
                                                <div class="switch has-switch switch-container-class" data-class="fixed-sidebar">
                                                    <div class="switch-animate switch-on">
                                                        <input type="checkbox" checked data-toggle="toggle" data-onstyle="success">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="widget-content-left">
                                                <div class="widget-heading">Barra lateral fija
                                                </div>
                                                <div class="widget-subheading">¡Hace que la barra lateral izquierda esté fija, siempre visible!
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="widget-content p-0">
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left mr-3">
                                                <div class="switch has-switch switch-container-class" data-class="fixed-footer">
                                                    <div class="switch-animate switch-off">
                                                        <input type="checkbox" data-toggle="toggle" data-onstyle="success">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="widget-content-left">
                                                <div class="widget-heading">Pie de página fijo
                                                </div>
                                                <div class="widget-subheading">¡Hace que la parte inferior del pie de página de la aplicación sea fija, siempre visible!
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <h3 class="themeoptions-heading">
                            <div>
                                Opciones de encabezado
                            </div>
                            <button type="button" class="btn-pill btn-shadow btn-wide ml-auto btn btn-focus btn-sm switch-header-cs-class" data-class="">
                                Restaurar valores predeterminados
                            </button>
                        </h3>
                        <div class="p-3">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <h5 class="pb-2">ELEGIR ESQUEMA DE COLOR
                                    </h5>
                                    <div class="theme-settings-swatches">
                                        <div class="swatch-holder bg-primary switch-header-cs-class" data-class="bg-primary header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-secondary switch-header-cs-class" data-class="bg-secondary header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-success switch-header-cs-class" data-class="bg-success header-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-info switch-header-cs-class" data-class="bg-info header-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-warning switch-header-cs-class" data-class="bg-warning header-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-danger switch-header-cs-class" data-class="bg-danger header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-light switch-header-cs-class" data-class="bg-light header-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-dark switch-header-cs-class" data-class="bg-dark header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-focus switch-header-cs-class" data-class="bg-focus header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-alternate switch-header-cs-class" data-class="bg-alternate header-text-light">
                                        </div>
                                        <div class="divider">
                                        </div>
                                        <div class="swatch-holder bg-vicious-stance switch-header-cs-class" data-class="bg-vicious-stance header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-midnight-bloom switch-header-cs-class" data-class="bg-midnight-bloom header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-night-sky switch-header-cs-class" data-class="bg-night-sky header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-slick-carbon switch-header-cs-class" data-class="bg-slick-carbon header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-asteroid switch-header-cs-class" data-class="bg-asteroid header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-royal switch-header-cs-class" data-class="bg-royal header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-warm-flame switch-header-cs-class" data-class="bg-warm-flame header-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-night-fade switch-header-cs-class" data-class="bg-night-fade header-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-sunny-morning switch-header-cs-class" data-class="bg-sunny-morning header-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-tempting-azure switch-header-cs-class" data-class="bg-tempting-azure header-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-amy-crisp switch-header-cs-class" data-class="bg-amy-crisp header-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-heavy-rain switch-header-cs-class" data-class="bg-heavy-rain header-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-mean-fruit switch-header-cs-class" data-class="bg-mean-fruit header-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-malibu-beach switch-header-cs-class" data-class="bg-malibu-beach header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-deep-blue switch-header-cs-class" data-class="bg-deep-blue header-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-ripe-malin switch-header-cs-class" data-class="bg-ripe-malin header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-arielle-smile switch-header-cs-class" data-class="bg-arielle-smile header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-plum-plate switch-header-cs-class" data-class="bg-plum-plate header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-happy-fisher switch-header-cs-class" data-class="bg-happy-fisher header-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-happy-itmeo switch-header-cs-class" data-class="bg-happy-itmeo header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-mixed-hopes switch-header-cs-class" data-class="bg-mixed-hopes header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-strong-bliss switch-header-cs-class" data-class="bg-strong-bliss header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-grow-early switch-header-cs-class" data-class="bg-grow-early header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-love-kiss switch-header-cs-class" data-class="bg-love-kiss header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-premium-dark switch-header-cs-class" data-class="bg-premium-dark header-text-light">
                                        </div>
                                        <div class="swatch-holder bg-happy-green switch-header-cs-class" data-class="bg-happy-green header-text-light">
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <h3 class="themeoptions-heading">
                            <div>Opciones de la barra lateral</div>
                            <button type="button" class="btn-pill btn-shadow btn-wide ml-auto btn btn-focus btn-sm switch-sidebar-cs-class" data-class="">
                                Restaurar valores predeterminados
                            </button>
                        </h3>
                        <div class="p-3">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <h5 class="pb-2">ELEGIR ESQUEMA DE COLOR
                                    </h5>
                                    <div class="theme-settings-swatches">
                                        <div class="swatch-holder bg-primary switch-sidebar-cs-class" data-class="bg-primary sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-secondary switch-sidebar-cs-class" data-class="bg-secondary sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-success switch-sidebar-cs-class" data-class="bg-success sidebar-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-info switch-sidebar-cs-class" data-class="bg-info sidebar-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-warning switch-sidebar-cs-class" data-class="bg-warning sidebar-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-danger switch-sidebar-cs-class" data-class="bg-danger sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-light switch-sidebar-cs-class" data-class="bg-light sidebar-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-dark switch-sidebar-cs-class" data-class="bg-dark sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-focus switch-sidebar-cs-class" data-class="bg-focus sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-alternate switch-sidebar-cs-class" data-class="bg-alternate sidebar-text-light">
                                        </div>
                                        <div class="divider">
                                        </div>
                                        <div class="swatch-holder bg-vicious-stance switch-sidebar-cs-class" data-class="bg-vicious-stance sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-midnight-bloom switch-sidebar-cs-class" data-class="bg-midnight-bloom sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-night-sky switch-sidebar-cs-class" data-class="bg-night-sky sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-slick-carbon switch-sidebar-cs-class" data-class="bg-slick-carbon sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-asteroid switch-sidebar-cs-class" data-class="bg-asteroid sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-royal switch-sidebar-cs-class" data-class="bg-royal sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-warm-flame switch-sidebar-cs-class" data-class="bg-warm-flame sidebar-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-night-fade switch-sidebar-cs-class" data-class="bg-night-fade sidebar-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-sunny-morning switch-sidebar-cs-class" data-class="bg-sunny-morning sidebar-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-tempting-azure switch-sidebar-cs-class" data-class="bg-tempting-azure sidebar-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-amy-crisp switch-sidebar-cs-class" data-class="bg-amy-crisp sidebar-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-heavy-rain switch-sidebar-cs-class" data-class="bg-heavy-rain sidebar-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-mean-fruit switch-sidebar-cs-class" data-class="bg-mean-fruit sidebar-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-malibu-beach switch-sidebar-cs-class" data-class="bg-malibu-beach sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-deep-blue switch-sidebar-cs-class" data-class="bg-deep-blue sidebar-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-ripe-malin switch-sidebar-cs-class" data-class="bg-ripe-malin sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-arielle-smile switch-sidebar-cs-class" data-class="bg-arielle-smile sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-plum-plate switch-sidebar-cs-class" data-class="bg-plum-plate sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-happy-fisher switch-sidebar-cs-class" data-class="bg-happy-fisher sidebar-text-dark">
                                        </div>
                                        <div class="swatch-holder bg-happy-itmeo switch-sidebar-cs-class" data-class="bg-happy-itmeo sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-mixed-hopes switch-sidebar-cs-class" data-class="bg-mixed-hopes sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-strong-bliss switch-sidebar-cs-class" data-class="bg-strong-bliss sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-grow-early switch-sidebar-cs-class" data-class="bg-grow-early sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-love-kiss switch-sidebar-cs-class" data-class="bg-love-kiss sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-premium-dark switch-sidebar-cs-class" data-class="bg-premium-dark sidebar-text-light">
                                        </div>
                                        <div class="swatch-holder bg-happy-green switch-sidebar-cs-class" data-class="bg-happy-green sidebar-text-light">
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <h3 class="themeoptions-heading">
                            <div>Opciones de contenido principal</div>
                            <button type="button" class="btn-pill btn-shadow btn-wide ml-auto active btn btn-focus btn-sm">Restaurar valores predeterminados
                            </button>
                        </h3>
                        <div class="p-3">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <h5 class="pb-2">PESTAÑAS DE SECCIÓN DE PÁGINA
                                    </h5>
                                    <div class="theme-settings-swatches">
                                        <div role="group" class="mt-2 btn-group">
                                            <button type="button" class="btn-wide btn-shadow btn-primary btn btn-secondary switch-theme-class" data-class="body-tabs-line">
                                                línea
                                            </button>
                                            <button type="button" class="btn-wide btn-shadow btn-primary active btn btn-secondary switch-theme-class" data-class="body-tabs-shadow">
                                                Sombra
                                            </button>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>        <div class="app-main">
                <div class="app-sidebar sidebar-shadow">
                    <div class="app-header__logo">
                        <div class="" style="font-weight: bold;color:#000;font-size:24px;">SIIC01</div><!--logo-src-->
                        <div class="header__pane ml-auto">
                            <div>
                                <button type="button" class="hamburger close-sidebar-btn hamburger--elastic" data-class="closed-sidebar">
                                    <span class="hamburger-box">
                                        <span class="hamburger-inner"></span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="app-header__mobile-menu">
                        <div>
                            <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                                <span class="hamburger-box">
                                    <span class="hamburger-inner"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                    <div class="app-header__menu">
                        <span>
                            <button type="button" class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                                <span class="btn-icon-wrapper">
                                    <i class="fa fa-ellipsis-v fa-w-6"></i>
                                </span>
                            </button>
                        </span>
                    </div>

                    <div class="scrollbar-sidebar">
                        <div class="app-sidebar__inner">
                            <ul class="vertical-nav-menu">
                                <li class="app-sidebar__heading">OPCIONES</li>
                                <!--OPCION SIMPLE-->
                                <?php
                                if($session['modulos_adicionales']){
    					        foreach ($session['modulos_adicionales'] as $key) {
                                ?><li><a href="<?=($key['laravel']==1)?$key['url_laravel']:'http://siic01.ugel01.gob.pe/index.php/'.$key['url']?>"><i class="metismenu-icon pe-7s-angle-right-circle"></i><?=$key['nombre_modulo']?></a></li><?php
                                }
                                }
                                ?>
                                <!--OPCION SIMPLE-->

                                <li class="app-sidebar__heading">MENU</li>
                                <!--MENU-->
                                <?php
                                if($session['grupos_adicionales']){
    					        foreach ($session['grupos_adicionales'] as $key) {
                                ?>
                                <li>
                                    <a href="#"><i class="metismenu-icon pe-7s-angle-down-circle"></i><?=$key['grupo']?><i class="metismenu-state-icon pe-7s-angle-down caret-left"></i></a>
                                    <ul>
                                    <?php
                                    if($key['modulos']){
                    				foreach ($key['modulos'] as $mod) {
                                    ?><li><a href="<?=($mod['laravel']==1)?$mod['url_laravel']:'http://siic01.ugel01.gob.pe/index.php/'.$mod['url']?>"><i class="metismenu-icon"></i><?=$mod['nombre_modulo']?></a></li><?php
                                    }
                                    }
                                    ?>
                                    </ul>
                                </li>
                                <?php
                                }
                                }
                                ?>
                                <!--MENU-->
                            </ul>
                        </div>
                    </div>
                </div>    <div class="app-main__outer">
                    <div class="app-main__inner">
                        <div class="col-lg-12">@yield('html') <!--variable en el layout en donde se colocara el texto contenido en @ section --></div>
                    </div>
                <!--
                    <div class="app-wrapper-footer">
                        <div class="app-footer">
                            <div class="app-footer__inner">
                                <div class="app-footer-left">
                                    <ul class="nav">
                                        <li class="nav-item">
                                            <a href="javascript:void(0);" class="nav-link">
                                                Footer Link 1
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="javascript:void(0);" class="nav-link">
                                                Footer Link 2
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="app-footer-right">
                                    <ul class="nav">
                                        <li class="nav-item">
                                            <a href="javascript:void(0);" class="nav-link">
                                                Footer Link 3
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="javascript:void(0);" class="nav-link">
                                                <div class="badge badge-success mr-1 ml-0">
                                                    <small>NEW</small>
                                                </div>
                                                Footer Link 4
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                -->
                </div>
        </div>
    </div>
    <!--../js/main.js-->
    <script type="text/javascript" src="{{asset('assets/scripts/main.js')}}"></script>
{{--  /*incio jmmj 16-06-2023*/  --}}
<div class="modal fade"  id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="1000" aria-labelledby="staticBackdropLabel"
aria-hidden="true">
   <div class="modal-dialog modal-xl" >
     <div class="modal-content">
       <div class="modal-header">
         <h1 class="modal-title fs-5" id="staticBackdropLabel">Aprobar/Observar Título</h1>
         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
       </div>
       <div class="modal-body" >
         ...
       </div>
       <div class="modal-footer">
         <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
         <button type="button" class="btn btn-danger observar" data-bs-target="#exampleModalToggle2" data-bs-toggle="modal">Observar</button>
         <button type="button" class="btn btn-success aprobar" id="aprobar">Aprobar</button>
       </div>
     </div>
   </div>
 </div>

 <div class="modal fade" id="exampleModalToggle2" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalToggleLabel2">Observar título</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="{{ route('saveObservacionTitulo') }}" method="POST" id="form-id">
            <div class="mb-3">
                <input type="hidden" name="idTit" id="idTit" value="0">
                @csrf
                <label for="exampleFormControlTextarea1" class="form-label">Ingresar Observación</label>
                <textarea class="form-control" id="exampleFormControlTextarea1" name="observacion" rows="3"></textarea>
              </div>
          </form>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary ver_titulo" id="0" data-bs-target="#staticBackdrop" data-bs-toggle="modal">Volver</button>
          <button type="button" class="btn btn-success"
          id="saveObservacionTitulo">Guardar</button>
        </div>
      </div>
    </div>
  </div>
<script>
    $("#saveObservacionTitulo").on("click",function()
    {
        let data= $("#form-id").serializeArray()
        $.ajax({
            type: "post",
            url: "{{ route('saveObservacionTitulo') }}",
            data:data,
            datType:"JSON",
            beforeSend: function()
            {

            },
            error: function(){
              alert("Hubo un error");
            },
            success: function(data){

                var queryParamsType = $("#codmod").val()

                $table.bootstrapTable('refreshOptions', {
                  queryParamsType: queryParamsType
                })
                $("#exampleModalToggle2").modal("hide")
                alert(data.mensaje)
            }
          });
    })

    $("#aprobar").on("click",function()
    {
        if (confirm("Esta segura de aprobar el título") == true) {
            let id = $("#staticBackdrop #idTitulo").val()
            let token = $("input[name='_token']").val()
            $.ajax({
                type: "post",
                url: "{{ route('saveAprobarTitulo') }}",
                data:{idTit:id,"_token":token},
                datType:"JSON",
                beforeSend: function()
                {

                },
                error: function(){
                  alert("Hubo un error");
                },
                success: function(data){

                    var queryParamsType = $("#codmod").val()

                    $table.bootstrapTable('refreshOptions', {
                      queryParamsType: queryParamsType
                    })
                $("#staticBackdrop").modal("hide")

                    alert(data.mensaje)
                }
              });
          } else {
            alert("cancelado")
          }
    })
    const myModalEl = document.getElementById('exampleModalToggle2')
    const staticBackdrop = document.getElementById('staticBackdrop')

        myModalEl.addEventListener('hidden.bs.modal', event => {
            $("#exampleFormControlTextarea1").val("")
        })
        staticBackdrop.addEventListener('hidden.bs.modal', event => {
            $("#exampleFormControlTextarea1").val("")
        })

        myModalEl.addEventListener('shown.bs.modal', event => {
            $("#exampleFormControlTextarea1").focus()
        })

</script>
{{--  /*fin jmmj 16-06-2023*/

/*inicio jmmj 20-06-2023*/  --}}
<div class="modal fade"  id="numeracionUgel" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="1000" aria-labelledby="numeracionUgelLabel"
aria-hidden="true">
   <div class="modal-dialog modal-xl" >
     <div class="modal-content">
       <div class="modal-header">
         <h1 class="modal-title fs-5" id="numeracionUgelLabel">Título CETPRO</h1>
         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
       </div>
       <div class="modal-body" >
         ...
       </div>
       <div class="modal-footer">
         <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
         <button type="button" class="btn btn-danger numerar" data-bs-target="#saveNumeracionModel" data-bs-toggle="modal">Numerar</button>

       </div>
     </div>
   </div>
 </div>
 <div class="modal fade" id="saveNumeracionModel" aria-hidden="true" aria-labelledby="saveNumeracionModelLabel2" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="saveNumeracionModelLabel2">Registro de numeración título CETPROS</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="{{ route('saveNumeracion') }}" method="POST" id="form-numeracion">
            <div class="mb-3">
                <input type="hidden" name="titulo_id" id="titulo_id" value="0">
                @csrf
                <label for="exampleFormControlTextarea1" class="form-label">Ingresar código de registro UGEL 01</label>
                <input class="form-control" type="text" id="exampleFormControlTextarea1" name="codigo_ugel" >
              </div>
          </form>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary ver_titulo1" id="0" data-bs-target="#numeracionUgel" data-bs-toggle="modal">Volver</button>
          <button type="button" class="btn btn-success"
          id="saveNumeracion">Guardar</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    $("#saveNumeracion").on("click",function()
    {
        let data= $("#form-numeracion").serializeArray()
        $.ajax({
            type: "post",
            url: "{{ route('saveNumeracion') }}",
            data:data,
            datType:"JSON",
            beforeSend: function()
            {

            },
            error: function(){
              alert("Hubo un error");
            },
            success: function(data){

                var queryParamsType = $("#codmod").val()

                $table.bootstrapTable('refreshOptions', {
                  queryParamsType: queryParamsType
                })
                $("#saveNumeracionModel").modal("hide")
                alert(data.mensaje)
            }
          });
    })
  </script>
{{--  /*fin jmmj 20-06-2023*/  --}}

{{--  inicio jmmj 22-06-2023  --}}
<style type="text/css">
    #global {
        height: 300px;
        width: 200px;
        border: 1px solid #ddd;
        background: #f1f1f1;
        overflow-y: scroll;
    }
    #mensajes {
        height: auto;
    }

    </style>
<div class="modal fade" id="seguimientoEgresado" aria-hidden="true" aria-labelledby="seguimientoEgresadoLabel2" tabindex="-1">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="seguimientoEgresadoLabel2">seguimiento al Estudiante Egresado</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <form action="{{ route('saveSeguimientoEstudiante') }}" method="POST" id="form-seguimiento-estudiante">
                        @csrf

                        <div class="mb-3">
                            <label class="form-check-label" for="trabaja">
                               ¿Trabaja actualmente?
                            </label>
                            <div class="form-check">
                            <input class="form-check-input" type="radio" name="trabaja" value="SI" id="si" >
                            <label class="form-check-label" for="si">
                                SI
                            </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="trabaja" value="NO" id="no" >
                                <label class="form-check-label" for="no">
                                    NO
                                </label>
                            </div>

                        </div>
                        <div class="mb-3">
                            <label class="form-check-label" for="depend_indeped">
                               ¿Cuenta con trabajo dependiente o independiente?
                            </label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="depend_indeped" value="Dependiente" id="dependiente" >
                                <label class="form-check-label" for="dependiente">
                                    Dependiente
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="depend_indeped" value="Independiente" id="independiente" >
                                <label class="form-check-label" for="independiente">
                                    Independiente
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-check-label" for="lugarLaborando">
                              Razón social de la empresa donde trabaja
                               </label>
                            <input type="text" class="form-control" name="lugarLaborando" >
                        </div>
                        <div class="mb-3">
                            <label class="form-check-label" for="giro">
                               Giro a la que se dedica la empresa
                               </label>
                            <input type="text" class="form-control" name="giro" >
                        </div>
                        <div class="mb-3">
                            <label class="form-check-label" for="cantidadTrabajadores">
                              Cantidad de trabajadores de la empresa
                               </label>
                            <input type="number" class="form-control" name="cantidadTrabajadores" >
                        </div>
                        <div class="mb-3">
                        <input type="hidden" name="idTit" id="idTit" value="0">
                        <input type="hidden" name="idAlu" id="idAlu" value="0">
                        <input type="hidden" name="idPro" id="idPro" value="0">

                        <label class="form-check-label">
                            ¿Trabaja en algo relacionado al perfil de egreso que estudio?
                            </label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="laborandoEnLaCarrera" value="SI" id="si" >
                            <label class="form-check-label" for="si">
                                SI
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="laborandoEnLaCarrera" value="NO" id="no" >
                            <label class="form-check-label" for="no">
                                NO
                            </label>
                        </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-check-label" for="puestoTrabajo">
                               Puesto de trabajo actual
                               </label>
                            <input type="text" class="form-control" name="puestoTrabajo" >
                        </div>
                        <div class="mb-3">
                            <label class="form-check-label" for="experiencia">
                               Años de experiencia en el puesto actual
                               </label>
                            <input type="text" class="form-control" name="experiencia" >
                        </div>
                          <div class="mb-3">
                            <label class="form-check-label" for="rango">
                               ¿Aproximadamente cuánto percibe?
                               </label>
                            <input type="text" class="form-control" name="rangoSueldo" >
                          </div>
                      </form>
                </div>
                <div class="col-md-6" id="global">
                    <div id="mensajes">

                    </div>
                </div>
            </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success"
          id="saveSeguimientoEstudiante">Guardar</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    let modalSeguimientoEstudiante = document.getElementById("seguimientoEgresado")
    modalSeguimientoEstudiante.addEventListener('show.bs.modal', function (event) {
        // Botón que activó el modal
        var button = event.relatedTarget
        // Extraer información de los atributos data-bs-*
        var idTit = button.getAttribute('data-bs-idtit')
        var idAlu = button.getAttribute('data-bs-idalu')

        var idPro = button.getAttribute('data-bs-idpro')

        // Si es necesario, puedes iniciar una solicitud AJAX aquí
        // y luego realiza la actualización en una devolución de llamada.
        //
        // Actualizar el contenido del modal.
        var inputIdTit = modalSeguimientoEstudiante.querySelector('.modal-body #idTit')
        inputIdTit.value = idTit

        var inputIdAlu = modalSeguimientoEstudiante.querySelector('.modal-body #idAlu')
        inputIdAlu.value = idAlu

        var inputIdPro= modalSeguimientoEstudiante.querySelector('.modal-body #idPro')
        inputIdPro.value = idPro

        const params = { idTit: idTit, idAlu:idAlu, idPro:idPro, _token:$("input[name='_token']").val()}

        $.ajax({
            type: "post",
            url: "{{ route('getHistory') }}",
            data:params,
            datType:"JSON",
            beforeSend: function()
            {
            },
            error: function(){
              alert("Hubo un error");
            },
            success: function(data){
                let estructura = ""
                let $contenedor = modalSeguimientoEstudiante.querySelector('.modal-body #global #mensajes')
                $.each(data.data,function(e,i){
                    const fechaActual = new Date(i.created_at);
                    const opciones = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' };
                    estructura+=`<div class="mb-3">`;
                    estructura+=`Fecha consulta : <strong>${fechaActual.toLocaleDateString('es-PE', opciones)}</strong><br>`;
                    info = JSON.parse(i.descripcion)
                    //objeto = JSON.stringify(info)
                    estructura+=`¿Trabaja actualmente? : <strong>${info.trabaja}</strong><br>`;
                    estructura+=`¿Cuenta con trabajo dependiente o independiente? : <strong>${info.depend_indeped}</strong><br>`;

                    estructura+=`Razón social de la empresa donde trabaja : <strong>${info.lugarLaborando}</strong><br>`;
                    estructura+=`Giro a la que se dedica la empresa : <strong>${info.giro}</strong><br>`;
                    estructura+=`Cantidad de trabajadores de la empresa : <strong>${info.cantidadTrabajadores}</strong><br>`;

                    estructura+=`Trabaja en algo relacionado al perfil de egreso que estudio : <strong>${info.laborandoEnLaCarrera}</strong><br>`;
                    estructura+=`Puesto de trabajo actual : <strong>${info.puestoTrabajo}</strong><br>`;
                    estructura+=`Años de experiencia en el puesto actual : <strong>${info.experiencia}</strong><br>`;


                    estructura+=`¿Aproximadamente cuánto percibe? : <strong>${info.rangoSueldo}</strong><br>`;
                    estructura+=`</div>`;
                })

                console.log($contenedor)
                $contenedor.innerHTML=estructura
            }
          });

      })
    $("#saveSeguimientoEstudiante").on("click",function()
    {
        let data= $("#form-seguimiento-estudiante").serializeArray()
        $.ajax({
            type: "post",
            url: "{{ route('saveSeguimientoEstudiante') }}",
            data:data,
            datType:"JSON",
            beforeSend: function()
            {

            },
            error: function(){
              alert("Hubo un error");
            },
            success: function(data){

                var queryParamsType = $("#codmod").val()

                $table.bootstrapTable('refreshOptions', {
                  queryParamsType: queryParamsType
                })
                $("#seguimientoEgresado").modal("hide")
                alert(data.mensaje)
            }
          });
    })
  </script>
{{--  fin jmmj 22-06-2023  --}}
</body>
</html>


<div id="popup01" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content"></div>
    </div>
  </div>

<div id="fc_popup"  data-toggle="modal" data-target="#popup01"></div>

<div id="popuplg" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content"></div>
    </div>
  </div>

  <div id="fc_popuplg"  data-toggle="modal" data-target="#popuplg"></div>
