<!-- Default 스킨 - 게시글 목록 -->
<div class='board_skin_default list'>
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
		<li class='list_rows'>
			<a href='<?=siteUrl("board/view")?>?idx=<?=$li['idx']?>' class='subject'>
				<?=$li['category']?"[".$li['category']."]":""?>
				<?=$li['subject']?>
			</a>
			<div class='post_info'>
				<?=$li['poster']?>
				<?=$li['memNo']?"(".$li['memId'].")":""?>
				/ <?=date("Y.m.d", strtotime($li['regDt']))?>
			</div>
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