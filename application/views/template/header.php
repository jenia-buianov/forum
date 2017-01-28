
<!DOCTYPE html>
<html dir="ltr" lang="en-gb">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?=$page['title'];?></title>
    <?=$included_css;?>

</head>
<body id="phpbb" class="nojs   notouch section-index ltr  ">
    <div id="wrap">
        <div id="page-header">



            <div class="headerbar" role="banner">


                <div class="inner">
                    <div class="top"></div>

                    <div id="site-description">
                        <!-- LOGO START BELOW-->
                        <a id="logo" class="logo" href="<?=base_url()?>" title="Board index">
                            <img src="<?=$settings['logo']?>" width="34" height="34"> <span class="logo-text"><?=$settings['title']?></span>
                        </a>
                        <!-- LOGO END -->
                        <p class="skiplink"><a href="#start_here">Skip to content</a></p>
                    </div>

                    <div class="navbar" role="navigation">
                        <div class="inner">

                            <div id="responsive-menu-button">
                                <a href="#"></a>
                            </div>

                            <ul id="nav-main" role="menubar">

                                <?php
                                foreach ($menu as $k=>$v){
                                    echo '<li>
                                              <a href="'.base_url().$v->link.'" title="'.$v->text.'" style="text-align:center">
                                                <i class="fa fa-'.$v->icon.'" aria-hidden="true"></i>
                                                <span>'.mb_strtoupper($v->text).'</span>
                                              </a>
                                          </li>';
                                }
                                ?>


                                <li class="four-cubes">
                                    <ul>
                                        <?php
                                        if(!getUser()){
                                            echo'
                                            <li class="small-cube"><a href="'.base_url().'auth/registration" title="'.translation('registration').'"><i class="fa fa-registered"></i></a></li>
                                            <li class="small-cube"><a href="'.base_url('auth/singin').'" title="'.translation('sing in').'"><i class="fa fa-sign-in"></i></a></li>
                                            ';
                                        }
                                        else{

                                        }
                                        ?>

                                    </ul>

                                </li>
                            </ul>

                        </div>
                    </div>

                    <!-- Modal login -->
                    <div class="modal fade" id="loginmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content cube-bg-1">
                                <form method="post" action="./ucp.php?mode=login">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-uniE0BE"></i></button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="modal-login-block">
                                            <div class="modal-login-header">
                                                <h2>Login</h2>
                                                <a href="./ucp.php?mode=register" class="small-link">Register</a>
                                            </div>
                                            <div class="modal-login-content">
                                                <fieldset class="quick-login">
                                                    <div class="mb20">
                                                        <input type="text" placeholder="" name="username" id="username" size="10" class="inputbox autowidth input-icon" title="Username">
                                                    </div>
                                                    <div class="mb20">
                                                        <input placeholder="" type="password" name="password" id="password" size="10" class="inputbox autowidth input-icon" title="Password">
                                                    </div>

                                                    <div class="mb10">
                                                        <a class="op-link" href="http://komidesign.com/phpBB3/ucp.php?mode=sendpassword">I forgot my password</a>
                                                    </div>

                                                    <div class="mb10">
                                                        <label class="op-link" for="autologin">Remember me <input type="checkbox" name="autologin" id="autologin"></label>
                                                    </div>

                                                    <div class="mb20">
                                                        <label class="op-link" for="viewonline">Hide my online status this session <input type="checkbox" name="viewonline" id="viewonline" tabindex="5"></label>
                                                    </div>

                                                    <input type="submit" name="login" value="Login" class="button2">
                                                    <input type="hidden" name="redirect" value="./index.php?">

                                                </fieldset>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                    <!-- Modal login END-->										</div>
            </div>

            <div class="navbar-bottom">


                <div id="search-box" class="search-box search-header">
                    <form action="./search.php" method="get" id="search">
                        <fieldset>
                            <input name="keywords" id="keywords" type="search" maxlength="128" title="Search for keywords" class="inputbox search tiny" size="20" value="" placeholder="Search…">
                            <button class="button icon-button search-icon" type="submit" title="Search"></button>
                            <a href="./search.php" class="button icon-button search-adv-icon" title="Advanced search"></a>

                        </fieldset>
                    </form>
                </div>

                <ul id="nav-breadcrumbs" class="linklist navlinks" role="menubar">
                    <li class="small-icon icon-home breadcrumbs">
                        <span class="crumb" itemtype="http://data-vocabulary.org/Breadcrumb" itemscope=""><a href="./index.php" accesskey="h" data-navbar-reference="index" itemprop="url" title="Board index"><span itemprop="title">Board index</span></a></span>
                    </li>

                </ul>


            </div>

        </div>
        <div id="page-body" role="main">
            <div id="body-with-sidebar">
