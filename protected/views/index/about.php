				<ul class="nav">
					<?php foreach($nav as $key=>$val):?>
					<li><a   href="<?php  echo $key ==46 || $key ==47 ? $this->createUrl('about',array('cid'=>$key )): $this->createUrl('info',array('cid'=>$key ));?>" ><?php echo $val;?></a></li>
					<?php endforeach;?>
					<li><a href="<?php  echo $this->createUrl('index')?>">泰策首页</a></li> 
				</ul>
			</div>
		</div>
	</div>
	<!-- // hearder -->
	
	<div class="ui-cont">
		<p class="subbanner"></p>
	</div>

	<div class="ui-cont">
		<div class="ui-subcont sublayout">

			<div style="  width:1005px; height:116px; ">
						<h3 style="border-bottom:#cccccc 1px solid;height:58px;"></h3>
						<h3 style=" text-align:center; font-size:26px; margin:0 auto; margin-top:-30px; background:#ffffff;  padding:5px;width:400px; font-weight:normal;"><?php  echo $title;?></h3>
			</div>

<!-- 			<ul class="actual-nav"> -->
<!-- 				<li><a href="#" class="cur">妙味讲堂风采</a></li> -->
<!-- 				<li><a href="#">老余讲堂风采</a></li> -->
<!-- 				<li><a href="#">老余讲堂风采</a></li> -->
<!-- 				<li><a href="#">老余讲堂风采</a></li> -->
<!-- 				<li><a href="#">老余讲堂风采</a></li> -->
<!-- 			</ul> -->

			<ul class="actual-main">
			<?php foreach($about as $key=>$val):?>
					<li style=" text-align:center; font-size:14px;"><a href="<?php  echo $this->createUrl('news',array('id'=>$key ))?>">
					<img style="height:240px;width:215px" src="<?php echo IMAGE_URL.$val['cover']; ?>" >
					</a>
					<span><?php echo $val['title'];?></span>
					</li>
			<?php endforeach;?>
			</ul>
		</div>
	</div>

	<div class="push"><!-- not put anything here --></div>
</div>