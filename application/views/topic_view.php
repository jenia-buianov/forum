<div class="topic_content">
    <div class="row">
        <div class="col-md-12" style="padding: 0px;margin:0px">
            <div class="sheader cube-color-2">
                <h2><?=htmlspecialchars_decode($topic->title)?></h2>
            </div>
            <div class="info col-md-4">
                <a href="<?=base_url('users/view/'.$topic->userId)?>"><?=$topic->name?></a><br>
                <?=translation('last visit').': '.$topic->userStatus?><br>
                <?=translation('published').': '.date('d.m.Y ' . translation('in') . ' H:i', strtotime($topic->datetime))?><br>
                <?=translation('views').': '.$topic->views?><br>
            </div>
            <div class="message col-md-8">
                <?=htmlspecialchars_decode($topic->text);?>
            </div>
        </div>

        <?php
        foreach ($topic->messages as $id=>$val){
            $lastVisit = $this->um->getStatus($val->userId);
            if ($lastVisit > time() - 900) $val->userStatus = 'online';
            else $val->userStatus = date('d.m.Y ' . translation('in') . ' H:i', $lastVisit);
            ?>
            <div class="col-md-12" style="padding: 0px;margin:0px;margin-top:1em">
                <div class="info col-md-4">
                    <a href="<?=base_url('users/view/'.$val->userId)?>"><?=$val->name?></a><br>
                    <?=translation('last visit').': '.$val->userStatus?><br>
                    <?=translation('published').': '.date('d.m.Y ' . translation('in') . ' H:i', strtotime($val->datetime))?><br>
                    <?=translation('verified'.$val->verifyed)?>
                </div>
                <div class="message col-md-8">
                    <?=htmlspecialchars_decode($val->text);?>
                </div>
            </div>
        <?
        }
        pagination(base_url('topic/view/'.$topic->url),$topic->pages,$topic->currentPage);
        ?>
    </div>
</div>