<!-- Gallery 스킨 - 게시글 목록 -->
<div class='board_skin_default board_skin_gallery list'>
	<!-- 게시판 분류 S -->
	<?php if ($confCategory) : ?>
	<ul class='category_tab'>
		<li class='tab<?php if (!$category) echo " on";?>'>
			<a href='<?=siteUrl("board/list")?>?id=<?=$id?>'>전체</a>
		</li>
	<?php foreach ($confCategory as $cate) : ?>
		<li class='tab<?php if ($category == $cate) echo " on";?>'>
			<a href='<?=siteUrl("board/list")?>?id=<?=$id?>&category=<?=$cate?>'><?=$cate?></a>
		</li>
	<?php endforeach; ?>
	</ul>
	<?php endif; ?>
	<!-- 게시판 분류 E -->
	<ul>
	<?php if ($list) : ?>
	<?php foreach ($list as $li) :  ?>
		<li class='list_box'>
			<a href='<?=siteUrl("board/view")?>?idx=<?=$li['idx']?>'>
				<div class='photo'>
				<?php if (isset($li['attachFiles']['files'])) : ?>
				<?php foreach ($li['attachFiles']['files'] as $k => $im) :  
						if ($k > 0) continue;
				?>
					<div class='inner' style="background:url('<?=$im['url']?>') no-repeat center center; background-size: cover;"></div>

				<?php endforeach; ?>
				<?php endif; ?>
				</div>
				<div class='title'><?=$li['subject']?></div>
				<div class='post_info'>
					<span class='poster'><?=$li['poster']?><?php if ($li['memId']) echo "(".$li['memId'].")";?></span>
					
					<span class='date'><?=date("Y.m.d", strtotime($li['regDt']))?></span>
					<span class='hit'>조회수 : <?=number_format($li['hit'])?> / </span>
				</div>	
			</a>
		</li>
	<?php endforeach; ?>
	<?php else : // 게시글이 없는 경우 ?>
		<li class='no_data'>게시글이 없습니다.</li>
	<?php endif; ?>
	</ul>
	<?=$pagination?>
	<div class='ar mt20'>
		<a href='<?=siteUrl('board/write?id=')?><?=$id?>' class='btn1'>글쓰기</a>
	</div>
</div>
<!--// board_skin_default -->