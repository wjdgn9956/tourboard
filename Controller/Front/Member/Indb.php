<?php

namespace Controller\Front\Member;

use App;
use Component\Exception\Member\MemberRegisterException;
use Component\Exception\Member\MemberUpdateException;
use Component\Exception\Member\MemberException;

/**
* 회원 관련 DB 처리 
*
*/
class IndbController extends \Controller\Front\Controller
{
	public function __construct()
	{
		$this->layoutBlank = true;
	}
	
	public function index()
	{
		try {
			$in = request()->all();
			$member = App::load(\Component\Member\Member::class);
			switch ($in['mode']) {
				/** 회원 가입 */
				case "register" :
					/** 약관 동의 체크 */
					if (!isset($in['agree'])) {
						throw new MemberRegisterException("약관에 동의해 주세요!");
					}
					
					$result = $member->data($in)
											->validator("register")
											->register();
											
					if ($result === false) { // 회원가입 실패
						throw new MemberRegisterException("회원가입 실패!");
					}
					
					// 회원 가입 성공 -> 로그인 페이지로 이동 
					go("member/login", "parent");
					break;
				/** 회원 정보 수정 */
				case "update" :
					
					// 로그인이 안되어 있는 경우는 처리 불가 
					if (!isLogin()) {
						throw new MemberUpdateException("정보 수정 권한이 없습니다.");
					}
					
					
					$in['level'] = $_SESSION['member']['level'];
					$in['memNo'] = $_SESSION['memNo'];
					
					$result = $member->data($in)
											  ->validator("update")
											  ->update();
									
					
					// 수정 실패 
					if ($result === false) {
						throw new MemberUpdateException("회원정보 수정 실패!");
					}
					
					// 수정 성공
					msg("회원정보가 수정되었습니다.", "member/update", "parent");
					
					break;
				/** 비밀번호 찾기 */
				case "find_pw" : 
					/** 필수 항목 체크 S */
					if (!$in['memId']) {
						throw new MemberException("아이디를 입력하세요.");
					}
					
					if (!$in['email']) {
						throw new MemberException("이메일을 입력하세요.");
					}
					
					if (!$in['cellPhone']) {
						throw new MemberException("휴대전화번호를 입력하세요.");
					}
					/** 필수 항목 체크 E */
					
					$result = $member->findMemPw($in['memId'], $in['email'], $in['cellPhone']);
					if ($result) {
						$msg = "비밀번호 초기화를 위한 정보를 메일로 전송하였습니다. 메일을 확인해 주세요";
					} else {
						$msg = "비밀번호 찾기에 실패하였습니다.";
					}
					
					msg($msg);
					
					break;
				/** 비밀번호 변경 */
				case "change_pw" :
					if (!isset($_SESSION['changePw_memId']) || !$_SESSION['changePw_memId']) {
						throw new MemberException("잘못된 접근입니다.");
					}
					
					$result = $member
										->data($in)
										->validator("change_pw")
										->changeMemPw($_SESSION['changePw_memId'], $in['memPw']);
					if ($result === false) { // 변경 실패 
						throw new MemberException("비밀번호 변경에 실패하였습니다.");
					}
					
					// 변경 성공 -> 로그인 페이지 이동 
					go("member/login", "parent");
					break;
			}
			
		} catch (MemberRegisterException $e) {
			echo $e;
		} catch (MemberUpdateException $e) {
			echo $e;
		} catch (MemberException $e) {
			echo $e;
		}
	}
}