 <header>
 <div class="nav">
    <div class="box logo">
       <a href="<?=siteUrl("main/index")?>"><i class ="xi-hotel"></i> YOON'S TOUR</i></a>
    </div>
    <div class="box info">
    <?php if (isLogin()) : ?>
		<?=$_SESSION['member']['memId']?>님 로그인..
    
		<?php if (isAdmin()) : ?>
		<a href="<?=siteUrl("admin")?>">관리자 메뉴</a>
		<?php endif; ?>
		<a href="<?=siteUrl("board")?>"><i class="xi-paper-o"></i>게시판</a>      
		<a href="<?=siteUrl("member/update")?>"><i class ="xi-home"></i>회원정보수정</a>
		<a href="<?=siteUrl("member/logout")?>"><i class ="xi-log-out"></i>로그아웃</a>
   <?php else : ?>
		<a href="<?=siteUrl("board")?>"><i class="xi-paper-o"></i>게시판</a>
		<a href="<?=siteUrl("member/login")?>"><i class ="xi-log-in"></i>로그인</a>
		<a href="<?=siteUrl("member/join")?>"><i class ="xi-log-out"></i>회원가입</a>
  <?php endif; ?> 
   </div>
</div>  
 </header>
 <!--메인 메뉴 영역 S-->
 <nav> 
     
</nav>
