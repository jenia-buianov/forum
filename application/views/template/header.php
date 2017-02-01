
<!DOCTYPE html>
<html lang="<?=getLang()?>">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" type="image/png" href="<?=$settings['logo']?>" />
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
                                            echo'
                                            <li class="small-cube"><a href="'.base_url('users/view/'.getUser()).'" title="'.translation('my profile').'"><i class="fa fa-user"></i></a></li>
                                            <li class="small-cube"><a href="'.base_url('auth/logout').'" title="'.translation('logout').'"><i class="fa fa-power-off"></i></a></li>
                                            ';
                                        }
                                        ?>

                                    </ul>

                                </li>


                            </ul>

                        </div>
                    </div>
				</div>
            </div>

            <div class="navbar-bottom">


                <div id="search-box" class="search-box search-header">
                    <form action="./search.php" method="get" id="search">
                        <fieldset>
                            <input name="keywords" id="keywords" type="search" maxlength="128" title="Search for keywords" class="inputbox search tiny" size="20" value="" placeholder="<?=translation('search')?>">
                            <button class="button icon-button search-icon" type="submit" title="<?=translation('search')?>"></button>

                        </fieldset>
                    </form>
                </div>

                <ul id="nav-breadcrumbs" class="linklist navlinks" role="menubar">
                    <li class="small-icon icon-home breadcrumbs">
                        <span class="crumb" itemscope=""><a href="<?=$breadcrumbs[0]['url']?>" accesskey="h" data-navbar-reference="index" itemprop="url" title="<?=$breadcrumbs[0]['title']?>"><span itemprop="title"><?=$breadcrumbs[0]['title']?></span></a></span>
                        <?php
                        for($k=1;$k<count($breadcrumbs);$k++){
                            echo'<span class="crumb" itemscope="" data-forum-id="'.$k.'"><a href="'.$breadcrumbs[$k]['url'].'" itemprop="url" title="'.$breadcrumbs[$k]['title'].'"><span itemprop="title">'.$breadcrumbs[$k]['title'].'</span></a></span>';
                        }
                        ?>
                    </li>

                </ul>


            </div>

        </div>
        <div id="page-body" role="main">
            <div id="sidebar">

                <?php
                foreach ($banners as $k=>$v){?>
                    <div class="sblock">
                        <div class="sheader cube-color-2">
                            <h3><?=$v->title?></h3>
                        </div>
                        <div class="scontent" style="text-align: center">
                            <a target="_blank" title="<?=$v->title?>" href="<?=$v->link?>"><img src="<?=base_url('uploads/banners/'.$v->image)?>" alt="<?=$v->title?>" style="max-width: 250px"></a>
                        </div>
                    </div>
                    <?php
                }
                ?>

            </div>
            <div id="body-with-sidebar">
