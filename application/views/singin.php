<div class="page_content">
    <div class="row">
        <h2 style="margin-bottom: 1em"><?=translation('sing in')?></h2>
        <form action="<?=base_url('auth/login')?>" method="post" onsubmit="return Send(this)" id="loginForm">
            <label class="col-xs-12 col-sm-12 col-md-4 col-lg-2"><?=translation('email')?></label>
            <div class="col-xs-12 col-sm-12 col-md-8 col-lg-10">
                <input type="email" class="form-control" name="email" placeholder="<?=translation('email')?>" must="1">
            </div>
            <label class="col-xs-12 col-sm-12 col-md-4 col-lg-2"><?=translation('password')?></label>
            <div class="col-xs-12 col-sm-12 col-md-8 col-lg-10">
                <input type="password" class="form-control" name="password" placeholder="<?=translation('password')?>" must="1">
            </div>
            <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12" style="text-align: center">
                <p id="alerts" style="text-align: center"></p>
                <button class="btn btn-success" type="submit"><?=mb_strtoupper(translation('enter'))?></button>
            </div>
        </form>
    </div>
</div>