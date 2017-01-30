<?php
if (!empty($topics)) {
        ?>
        <div class="forabg">
            <div class="inner">
                <ul class="topiclist">
                    <li class="header">
                        <dl class="row-item">
                            <dt>
                            <div class="list-inner"><a href="<?= base_url($topics['title']['url']) ?>"><?= $topics['title']['title'] ?></a></div>
                            </dt>
                            <dd class="topics"><?= translation('views') ?></dd>
                            <dd class="posts"><?= translation('posts') ?></dd>
                            <dd class="lastpost"><span><?= translation('last post') ?></span></dd>
                        </dl>
                    </li>
                </ul>
                <?php
                $count = 0;
                $style = "";
                if (count($topics['topics']) > 0) {
                    ?>
                    <ul class="topiclist forums">
                        <?php
                        foreach ($topics['topics'] as $i => $v) {
                            $count++;
                            if (!empty($v['image']))
                            {
                                $style.="li:nth-child(".$count.") .forum_read:before{
                                content:initial;}
                                li:nth-child(".$count.") .forum_read{
                                background-image:url('".$v['image']."');
                                background-repeat:no-repeat;
                                background-size:50px 50px;
                                }
                                ";
                            }
                            ?>

                            <li class="row">
                                <dl class="row-item forum_read"">
                                    <dt title="<?= translation('No unread posts') ?>">
                                    <div class="list-inner">
                                        <!--
                    <a class="feed-icon-forum" title="Feed - Unread forum" href="/phpBB3/app.php/feed?sid=e1aecf323556410204de2a657483e518?f=5">
                        <i class="icon fa-rss-square fa-fw icon-orange" aria-hidden="true"></i><span class="sr-only">Feed - Unread forum</span>
                    </a>
                -->
                                        <? if($v['vip']) echo'<i class="fa fa-exclamation-circle" aria-hidden="true" style="margin-right: 0.5em;color:#AA0000;font-size:1.5em"></i> ';?><a href="<?= base_url($i) ?>" class="forumtitle" <? if($v['vip']) echo'style="color:#AA0000"'; ?>><?= $v['title'] ?></a>
                                        <br>
                                        <a href="<?=base_url('users/view/'.$v['author']['id'])?>"><?=$v['author']['name']?></a>, <?= date('d.m.Y ' . translation('in') . ' H:i', strtotime($v['date'])) ?>
                                        <div class="responsive-show" style="display: none;">
                                        </div>
                                    </div>
                                    </dt>
                                    <dd class="topics"><?= $v['views'] ?> <dfn>Topics</dfn></dd>
                                    <dd class="posts"><?= $v['messages'] ?> <dfn>Posts</dfn></dd>
                                    <dd class="lastpost">
                                <span><?php
                                    if (!empty($v['last'])) {
                                        ?>

                                        <dfn>Last post</dfn>
                                        <a href="<?= base_url('topic/view/' . $i) ?>"
                                           title="<?= $v['last']['text'] ?>"
                                           class="lastsubject"><?= $v['last']['text'] ?></a> <br>
                                        <a href="<?= base_url('users/view/' . $v['last']['userId']) ?>"
                                           style="color: #AA0000;"
                                           class="username-coloured"><?= $v['last']['name'] ?></a>, <?= date('d.m.Y ' . translation('in') . ' H:i', strtotime($v['last']['date'])) ?>

                                    <?
                                    } else echo translation('no messages');
                                    ?></span>
                                    </dd>
                                </dl>
                            </li>
                        <?
                        }
                        ?>
                    </ul>
                <?
                echo '<style>'.$style.'</style>';
                }
                ?>
            </div>
        </div>
        <?php

}
?>
