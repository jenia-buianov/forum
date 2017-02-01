<div class="topic_content">
    <div class="row">
        <div>
            <div class="sheader cube-color-2">
                <h2><?=htmlspecialchars_decode($topic->title)?></h2>
            </div>
            <div class="info col-md-4">
                <a href="<?=base_url('users/view/'.$topic->userId)?>"><?=$topic->name?></a><br>
                <?=translation('last visit').': '.$topic->userStatus?><br>
                <?=translation('published').': '.date('d.m.Y ' . translation('in') . ' H:i', strtotime($topic->datetime))?><br>
                <?=translation('views').': '.$topic->views?><br>
            </div>
            <div class="message col-md-4">
                <?=htmlspecialchars_decode($topic->text);?>
            </div>
        </div>
    </div>
</div>