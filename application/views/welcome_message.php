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
                        <dd class="topics">Topics</dd>
                        <dd class="posts">Posts</dd>
                        <dd class="lastpost"><span>Last post</span></dd>
                    </dl>
                </li>
            </ul>
        </div>
    </div>
    <?php
}
?>