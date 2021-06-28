<div class='title1'>게시판 <?=isset($id)?"설정":"생성"?></div>
<div class='content_box'>
	<form method='post' action='indb' target='ifrmHidden' autocomplete='off'>
		<input type='hidden' name='mode' value='<?=isset($id)?"update":"register"?>_board'>
		<table class='table_cols'>
			<tr>
				<th>게시판아이디</th>
				<td>
					<?php if(isset($id)) : ?>
					<input type='hidden' name='id' value='<?=$id?>'>
					<?=$id?>
					<?php else : ?>
					<input type='text' name='id'>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<th>게시판명</th>
				<td>
					<input type='text' name='boardNm' value='<?=isset($boardNm)?$boardNm:""?>'>
				</td>
			</tr>
			<tr>
				<th>게시판분류</th>
				<td>
					<textarea name='category' placeholder='분류 여러개 입력은 엔터키로 구분해 주세요.'><?=isset($confCategory)?implode(PHP_EOL, $confCategory):""?></textarea>
				</td>
			</tr>
			<tr>
				<th>댓글</th>
				<td>
					<input type='radio' name='useReply' value='0' id='useReplay0'<?php if (isset($useReply) && !$useReply) echo " checked";?>>
					<label for='useReply0'>사용안함</label>
					<input type='radio' name='useReply' value='1' id='useReply1'<?php if ((isset($useReply) && $useReply) || !isset($useReply)) echo " checked";?>>
					<label for='useReply1'>사용함</label>
				</td>
			</tr>
			<tr>
				<th>스킨</th>
				<td>
					<select name='boardSkin'>
					<?php foreach ($skins as $skin) : ?>
						<option value='<?=$skin?>'<?php if (isset($boardSkin) && $boardSkin == $skin) echo " selected";?>><?=$skin?></option>
					<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th>노출항목</th>
				<td>
					<input type='checkbox' name='columns[]' value='file' id='columns_file'<?php if(isset($columns) && in_array("file", $columns)) echo " checked";?>>
					<label for='columns_file'>파일첨부</label>
					
					<input type='checkbox' name='columns[]' value='image' id='columns_image'<?php if(isset($columns) && in_array('image', $columns)) echo " checked";?>>
					<label for='columns_image'>이미지</label>
					
					<input type='checkbox' name='columns[]' value='link' id='columns_link'<?php if (isset($columns) && in_array('link', $columns)) echo " checked";?>>
					<label for='columns_link'>링크</label>
					
					<input type='checkbox' name='columns[]' value='email' id='columns_email'<?php if (isset($columns) && in_array("email", $columns)) echo " checked";?>>
					<label for='columns_email'>이메일</label>
				</td>
			</tr>
			<tr>
				<th>기타설정</th>
				<td>
					<input type='checkbox' name='useViewList' value='1' id='use_view_list'<?php if (isset($useViewList) && $useViewList) echo " checked";?>>
					<label for='use_view_list'>게시글 보기 하단 리스트 노출</label>
				</td>
			</tr>
		</table>
		<input type='submit' value='게시판 <?=isset($id)?"설정":"생성"?>하기' class='btn1 mt20' onclick="return confirm('정말 <?=isset($id)?"설정":"생성"?>하시겠습니까?');">
	</form>
</div>