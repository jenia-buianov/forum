<?php
foreach ($categories as $category=>$item) {
    ?>
    <div class="forabg">
        <div class="inner">
            <ul class="topiclist">
                <li class="header">
                    <dl class="row-item">
                        <dt>
                        <div class="list-inner"><a href="<?=base_url($category)?>"><?=$item['title']?></a></div>
                        </dt>
                        <dd class="topics"><?=translation('topics')?></dd>
                        <dd class="posts"><?=translation('posts')?></dd>
                        <dd class="lastpost"><span><?=translation('last post')?></span></dd>
                    </dl>
                </li>
            </ul>
            <?php
            if (count($item['child'])>0)
            {?>
            <ul class="topiclist forums">
                <?php
                foreach ($item['child'] as $i=>$v){
                ?>
                    <li class="row">
                        <dl class="row-item <?php if($v['unread']) echo 'forum_unread'; elseif($v['locked']) echo 'forum_read_locked'; else  echo 'forum_read'; ?>">
                            <dt title="<?=translation('No unread posts')?>">
                            <div class="list-inner">
                                <!--
            <a class="feed-icon-forum" title="Feed - Unread forum" href="/phpBB3/app.php/feed?sid=e1aecf323556410204de2a657483e518?f=5">
                <i class="icon fa-rss-square fa-fw icon-orange" aria-hidden="true"></i><span class="sr-only">Feed - Unread forum</span>
            </a>
        -->
                                <a href="<?=base_url('category/view/'.$v['url'])?>" class="forumtitle"><?=$v['title']?></a>
                                <br>
                                <?=$v['desc']?>
                                <div class="responsive-show" style="display: none;">
                                </div>
                            </div>
                            </dt>
                            <dd class="topics"><?=$v['topics']?> <dfn>Topics</dfn></dd>
                            <dd class="posts"><?=$v['messages']?> <dfn>Posts</dfn></dd>
                            <dd class="lastpost">
                                <span><?php
                                if(!empty($v['last'])){
                                    ?>

                                    <dfn>Last post</dfn>
                                    <a href="<?=base_url('topic/view/'.$v['last']['url'])?>" title="<?=$v['last']['title']?>" class="lastsubject"><?=$v['last']['title']?></a> <br>
									<a href="<?=base_url('users/view/'.$v['last']['userId'])?>" style="color: #AA0000;" class="username-coloured"><?=$v['last']['name']?></a>
                                    <br><?=date('d.m.Y '.translation('in').' H:i',strtotime($v['last']['datetime']))?>

                                    <?
                                }else echo translation('no messages');
                                    ?></span>
                            </dd>
                        </dl>
                    </li>
                <?}
                ?>
            </ul>
            <?}
            ?>
        </div>
    </div>
    <?php
}
?>
<div class="stat-block online-list">
    <h3><?=translation('online list')?></h3>
    <p>
        <?=translation('total online').': <b>'.$online.'</b><br>'.translation('guests').': <b>'.($online - count($onlineUsers)).'</b><br>';?>
        <?php
            if(count($onlineUsers)){
                foreach ($onlineUsers as $k=>$v){
                    echo '<a href="'.base_url('users/view/'.$v->userId).'">'.$v->name.'</a> ';
                }
            }
        ?>
    </p>
</div>
<div class="stat-block online-list">
    <h3><?=translation('topics list')?></h3>
    <p>
        <?=translation('total categories').': <b>'.$countCategories.'</b><br>'.translation('total topics').': <b>'.$countTopics.'</b><br>'.translation('total messages').': <b>'.$countMessages.'</b><br>';?>
    </p>
</div>