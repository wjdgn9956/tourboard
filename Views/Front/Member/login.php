<form id='join_form' method='post' action='<?=siteUrl("member/loginOk")?>' target='ifrmHidden' autocomplete='off'>
	<h1>LOGIN</h1>
	<dl>
		<dt>아이디</dt>
		<dd>
			<input type='text' name='memId' placeholder='아이디'>
		</dd>
	</dl>
	<dl>
		<dt>비밀번호</dt>
		<dd>
			<input type='password' name='memPw' placeholder='비밀번호'>
		</dd>
	</dl>
	<div class='links'>
		<a href='<?=siteUrl("member/findId")?>'>아이디 찾기</a> / 
		<a href='<?=siteUrl("member/findPw")?>'>비밀번호 찾기</a>
	</div>
	<div class="login_method">	
		<input type='submit' value='로그인' class='submit_btn'>
	</div>
</form>
