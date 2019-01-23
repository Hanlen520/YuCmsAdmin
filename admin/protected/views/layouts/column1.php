<?php $this->beginContent('application.views.layouts.main'); ?>
<aside class="Hui-aside">
	<input runat="server" id="divScrollValue" type="hidden" value="" />
	<div class="menu_dropdown bk_2">
        <?php foreach ($this->subMenu as $m) :?>
            <dl id="menu-article">
                <dt><i class="Hui-iconfont"></i><?php echo $m['menu_name']?><i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
                <?php if ($m['child']) :?>
                    <dd>
                        <ul>
                            <?php foreach ($m['child'] as $n) :?>
                                <li><a _href="<?php echo $this->createUrl($n['url']);?>" href="javascript:void(0)"><?php echo $n['menu_name'];?></a></li>
                            <?php endforeach;?>
                        </ul>
                    </dd>
                <?php endif;?>
            </dl>
        <?php endforeach;?>
	</div>
</aside>
<div class="dislpayArrow"><a class="pngfix" href="javascript:void(0);" onClick="displaynavbar(this)"></a></div>
<section class="Hui-article-box">
	<div id="Hui-tabNav" class="Hui-tabNav">
		<div class="Hui-tabNav-wp">
			<ul id="min_title_list" class="acrossTab cl">
				<li class="active"><span title="我的桌面" data-href="<?php echo $this->createUrl('border/index');?>">我的桌面</span><em></em></li>
			</ul>
		</div>
		<div class="Hui-tabNav-more btn-group"><a id="js-tabNav-prev" class="btn radius btn-default size-S" href="javascript:;"><i class="Hui-iconfont">&#xe6d4;</i></a><a id="js-tabNav-next" class="btn radius btn-default size-S" href="javascript:;"><i class="Hui-iconfont">&#xe6d7;</i></a></div>
	</div>
	<div id="iframe_box" class="Hui-article">
		<div class="show_iframe">
			<div style="display:none" class="loading"></div>
			<iframe scrolling="yes" frameborder="0" src="<?php echo $this->createUrl($this->firstUrl);?>"></iframe>
		</div>
	</div>
</section>
<?php $this->endContent(); ?>