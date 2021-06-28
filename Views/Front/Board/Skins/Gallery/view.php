<!-- Default 스킨 - 게시글 보기 -->
<div class='board_skin_default view'>
	<div class='subject'><?=$subject?></div>
	<div class='post_info'>
		Poster : <?=$poster?>(<?=$memNo?$memId:"비회원"?>) 
		/ Hit : <?=number_format($hit)?> 
		/ Date : <?=date("Y.m.d H:i", strtotime($regDt))?>
	</div>
	<?php if ($link) : ?>
	<div class='rows'>
		출연진 : <?=$link?>
	</div>
	<?php endif; ?>
	<?php if ($email) : ?>
	<div class='rows'>
		개봉날짜 : <?=$email?>
	</div>
	<?php endif; ?>
	
	<div class='contents'><?=$contents?></div>
	
	<a href='<?=siteUrl("board/list")?>?id=<?=$boardId?>' class='btn1'>글목록</a>
	<a href='<?=siteUrl("board/write")?>?id=<?=$boardId?>' class='btn1'>글쓰기</a>
	<?php if ($updatePossible) : ?>
	<a href='<?=siteUrl("board/update")?>?idx=<?=$idx?>' class='btn1'>글수정</a>
	<?php endif; ?>
	<?php if ($deletePossible) : ?>
	<a href='<?=siteUrl("board/indb")?>?mode=delete&idx=<?=$idx?>' onclick="return confirm('정말 삭제하시겠습니까?');" class='btn1'>글삭제</a>
	<?php endif; ?>
</div>
<!--// board_skin_default -->