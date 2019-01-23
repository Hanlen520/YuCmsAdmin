			<ul class="nav">
				<?php foreach($nav as $key=>$val):?>
					<li><a  href="<?php  echo $key ==46 || $key ==47 ? $this->createUrl('about',array('cid'=>$key )): $this->createUrl('info',array('cid'=>$key ));?>"  ><?php echo $val;?></a></li>
				<?php endforeach;?>
				<li><a href="<?php  echo $this->createUrl('index')?>" class="current">泰策首页</a></li>
			</ul>
		</div>
	</div>
</div>
<!-- // hearder -->
<div class="ui-cont ui-banner index-switchable" id="evaluation_carousel">
	<a href="javascript:void(0)" class="ui-switchable-prev-btn" data-role="prev"><i class="prev"></i></a>
	<a href="javascript:void(0)" class="ui-switchable-next-btn" data-role="next"><i class="next"></i></a>
	<div class="evaluation-scroller">
		<ul class="evaluation-content">
			<?php foreach($ad as $key=>$val):?>
				<li><a target="_blank" href="<?php  echo $val['link'] ? $val['link'] : $this->createUrl('advert/info',array('id'=>$key ))?>"><img src="<?php echo IMAGE_URL.$val['image']; ?>"></a></li>
			<?php endforeach;?>
		</ul>
	</div>
<!-- // evaluation-switchable -->
</div>

<!-- // banner-->
<div class="ui-cont ui-tips">
	<div class="ui-subcont">
		<div class="tips-cont" id="js_scrollnews">
			<i></i>
			<ul class="ui-switchable-content">
				<?php foreach($notice as $key=>$val):?>
					<li><a target="_blank" href="<?php  echo $val['link'] ? $val['link'] : $this->createUrl('advert/info',array('id'=>$key ))?>"><?php echo $val['title']?></a></li>
				<?php endforeach;?>
			</ul>
		</div> 
	</div>
</div>
<!-- // tips -->

	<div class="ui-cont ui-about">
		<div class="ui-subcont clearfix">
			<div class="about-left">
				<h4 class="ui-title">关于泰策</h4>
				<p class="tip">
				<?php foreach($company as $key=>$val):?>
					<?php echo strip_tags($val['content'])?>
				<?php endforeach;?>
				</p>
				<div class="motto">
					<span class="fn-right">--使命 / Mission</span>
					<i class="lt"></i>
					<i class="rb"></i>
					提供有竞争力的培训咨询解决方案和服务，持续为客户创造最大价值
				</div>
				<div class="new-list">
					<i class="title"></i>
					<a href="<?php echo $this->createUrl('/sign/add')?>" class="consulting"></a>
					<a href="<?php echo $this->createUrl('/sign/add')?>" class="appointment"></a>
					<ul class="list">
						<?php foreach($trends as $key=>$val):?>
						<li><a target="_blank" href="<?php  echo $val['link'] ? $val['link'] : $this->createUrl('advert/info',array('id'=>$key ))?>"><?php echo $val['title'];?></a></li>
						<?php endforeach;?>
					</ul>
				</div>
			</div>
			<div class="about-right">
				<h4 class="ui-title">
					<a href="<?php  echo $this->createUrl('info',array('cid'=>43 ))?>" class="more">查看更多<i></i></a>
					课程介绍
				</h4>
			<?php foreach($course as $key=>$val):?>
				<div class="course">
					<a target="_blank" href="<?php  echo $this->createUrl('news',array('id'=>$key ))?>"><img src="<?php echo IMAGE_URL.$val['cover']; ?>"></a>
					<a target="_blank" href="<?php  echo $this->createUrl('news',array('id'=>$key ))?>"><span><?php echo $val['title']?></span></a>
				</div>
				<p class="course-text"><?php echo $val['intro']?></p>
			<?php endforeach;?>
			</div>
		</div>
	</div>
	<!-- //  about-->

<div class="ui-cont ui-team">
	<div class="ui-subcont">
		<h4 class="ui-title">师资团队</h4>
		<div class="ui-team-cont" id="fn_team_list">
			<div class="team-cont">
				<ul class="ui-team-list">
					<?php foreach($teacher as $key=>$val):?>
					<li>
						<a target="_blank" href="<?php  echo $this->createUrl('news',array('id'=>$key ))?>"><img src="<?php echo IMAGE_URL.$val['cover'];?>" style="height:275px;width:220px"></a>
						<div class="tips">
							<h4><?php echo $val['title']?></h4>
							<h5><?php echo mb_substr(strip_tags($val['intro']),0,15,'utf-8')?></h5>
							<p><?php echo mb_substr(strip_tags($val['content']),0,20,'utf-8')?></p>
						</div>
					</li>
					<?php endforeach;?>
				</ul>
			</div>
		</div>
	</div>
</div>
<!--// team-->

<div class="ui-cont ui-photo">
	<div class="ui-subcont">
		<h4 class="ui-title">现场风采</h4>
		<ul class="ui-photo-list">
			<?php foreach($scene as $key=>$val):?>
					<li><a href="<?php  echo $this->createUrl('news',array('id'=>$key ))?>"><img style="width:232px;height:145px"src="<?php echo IMAGE_URL.$val['cover']; ?>"><span><?php echo $val['title'];?></span></a></li>
			<?php endforeach;?>
		</ul>
	</div>
</div>
<!-- // photo -->

<div class="ui-cont ui-customer">
	<div class="ui-subcont">
		<h4 class="ui-title">合作客户</h4>
		<p><a href="#">苏泊尔家电</a><a href="#">苏泊尔电器</a><a href="#">中美华东医药</a><a href="#">顾家家居</a><a href="#">百事可乐</a><a href="#">味全生技</a>
		<a href="#">佳华木工（方太供应商）</a><a href="#">中策橡胶</a><a href="#">南大环保（统一集团供应商）</a><a href="#">伊莱克斯</a><a href="#">雷士照明</a><a href="#">惠松制药</a>
		<a href="#">杭联热电</a><a href="#">热威机电</a><a href="#">安费诺飞凤通信</a><a href="#">杭华油墨</a><a href="#">中富果蔬等</a></p>
	</div>
</div>
<!-- // customer-->

<div class="push"><!-- not put anything here --></div>
</div>