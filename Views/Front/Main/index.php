<!-- swiper 영역 S -->
<section class="swiper">
    <div class="swiper-container">
        <div class="swiper-wrapper">
			<?php for ($i = 1; $i <= 3; $i++) : ?>
			<div class="swiper-slide banner">
				<img src="<?=siteUrl("assets/front/images/main/tour{$i}.jpg")?>">
			</div>
			<?php endfor; ?>          
        </div>
        <!-- Add Arrows -->
        <div class="swiper-pagination"></div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>
 </section>
<!-- swiper 영역 E -->

<!-- 최신 이미지 게시글 S -->
<ul class ="mainboard_image layout_width">
	<div class="main">
		<div class ="main_title">동남아시아 여행후기 게시판</div>
		<div class="bar"></div>
		<a href="<?=siteUrl("board")?>">다른 여행 게시판 보러가기</a>
	</div>
	<?php
		$list = getLatestPosts("동남아시아");
		foreach ($list as $li) :  ?>
		<li>
			<a href='<?=siteUrl("board/view?idx=".$li['idx'])?>'>
				<?php if (isset($li['attachFiles']['files'])) : ?>
				<?php foreach ($li['attachFiles']['files'] as $k => $im) :  
						if ($k > 0) continue;
				?>
					<img src='<?=$im['url']?>'>
				<?php endforeach; ?>
				<?php endif; ?>
				<span class='poster'><?=$li['poster']?></span>
				<span class='date'><?=date("Y.m.d", strtotime($li['regDt']))?></span>
				<span class='hit'><?=number_format($li['hit'])?></span>
			</a>
		</li>
	<?php endforeach; ?>
</ul>

<!-- 최신 이미지 게시글 E -->

<!-- 예약/ 1대1 문의 S -->
<div class="quick_bar">
  <ul>
  <li><a href="<?=siteUrl("board")?>" class ="qna">게시판 보기</a></li>
  <li><a class="go_reserve" onclick="layer.popup('http://yoonstour.cafe24app.com/chat?isPopup=1', 600, 700);">동행 구하기</a></li>
</ul>
</div>
<!-- 예약/ 1대1 문의 E -->

<script>
$(function() {
	  var swiper = new Swiper('.swiper-container', {
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
      pagination: {
          el: '.swiper-pagination',
          clickable : true,
        },
      loop:true,
      autoplay:{
          delay:5000,
      }
      
    });
});
</script>