<?php if ($memId) : // 아이디 찾음 ?>
<h1 style="text-align: center;">회원님의 아이디는 : <b><?=$memId?></b> 입니다.</h1>
<div class="findid_links">
    <a href="<?=siteUrl("member/login")?>">로그인 하기 /</a> 
    <a href="<?=siteUrl("member/findPw")?>">비밀번호 찾기</a>
</div>
<?php else : ?>
<form id ="join_form" method='post' action='<?=siteUrl("member/findId")?>' autocomplete='off'>
	<input type='hidden' name='isSubmitted' value='1'>
	<h1>find ID</h1>
	<dl>
		<dt>회원명</dt>
		<dd>
			<input type='text' name='memNm'>
		</dd>
	</dl>
	<dl>
		<dt>이메일</dt>
		<dd>
			<input type='email' name='email'>
		</dd>
	</dl>
	<dl>
		<dt>휴대전화</dt>
		<dd>
			<input type='text' name='cellPhone'>
		</dd>
	</dl>
	<input type='submit' value='아이디찾기' class='submit_btn'>
</form>
<?php endif; ?>
