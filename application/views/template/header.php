
<!DOCTYPE html>
<html dir="ltr" lang="en-gb">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>komidesign.com - Index page</title>
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
                        <a id="logo" class="logo" href="./index.php" title="Board index">
                            <i class="icon-thunder"></i>						<span class="logo-text">Subway</span>
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

                                <li class="li-home"><a href="./index.php" title="Board index"><i class="icon-home"></i><span>Home</span></a></li>

                                <li class="has-dropdown li-useful"><a href="#"><i class="icon-lifebuoy"></i><span>Quick links</span></a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="/phpBB3/app.php/help/faq" rel="help" title="Frequently Asked Questions" role="menuitem"><i class="icon-uniE09F"></i>FAQ</a></li>



                                        <li><a href="./memberlist.php?mode=team" role="menuitem"><i class="icon-uniE0BA"></i>The team</a></li>


                                    </ul>
                                </li>

                                <li class="has-dropdown li-forum"><a href="#"><i class="icon-comments"></i><span>Forum</span></a>
                                    <ul class="dropdown-menu" role="menu">






                                        <li><a href="./search.php?search_id=unanswered" role="menuitem"><i class="icon-uniE04C"></i>Unanswered topics</a></li>

                                        <li><a href="./search.php?search_id=active_topics" role="menuitem"><i class="icon-uniE03A"></i>Active topics</a></li>





                                    </ul><!-- end dropdown-menu -->
                                </li>




                                <li class="four-cubes">
                                    <ul>
                                        <li class="small-cube"><a href="#" title="Registration is disabled"><i class="icon-upload"></i></a></li>

                                        <li class="small-cube"><a href="#" title="Login" data-toggle="modal" data-target="#loginmodal" accesskey="x" role="menuitem"><i class="icon-switch"></i><span>Login</span></a></li>

                                        <li class="small-cube hide-max992 guest-link"><a href="#" title="Hello, guest !"><i class="icon-user3"></i></a></li>

                                        <li class="small-cube"><a href="" title="Contact us" role="menuitem"><i class="icon-feather"></i><span>Contact us</span></a></li>

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
