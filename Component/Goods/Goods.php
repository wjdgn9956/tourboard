<?php

namespace Component\Goods;

use App;
use Component\Exception\GoodsAdminException;
use Component\Exception\GoodsFrontException;

/**
* 상품 Component
*
*/
class Goods
{
	private $params = []; // 처리 데이터 
	private $requiredColumns = [  // 필수 컬럼 
		'goodsNm' => '상품명',
	];
	
	private $divisionStr = "||";
	
	private $addWhere = []; // 추가 검색조건 
	
	
	/** 
	* 추가 검색조건 설정 
	*
	* @param Array $where 검색 조건 
	* @return $this
	*/
	public function addWhere($where = []) 
	{
		$this->addWhere = $where;
		
		return $this;
	}
	/**
	* 처리데이터 설정 
	* 
	* @param Array $params - 처리 데이터 
	* @return $this
	*/
	public function data($params)
	{
		$this->params = $params;
		
		return $this;
	}
	
	/**
	* 상품 등록, 수정 유효성 검사 
	*
	* @param String $mode 등록(register), 수정(update)
	* @return $this
	* @throw GoodsAdminException 
	*/
	public function validator($mode = "register")
	{
		
		if ($mode == 'update') { // 상품 정보 수정 
			$this->requiredColumns['goodsNo'] = '상품번호';
		}
		
		/** 필수 데이터 체크 */
		$missing = [];
		foreach($this->requiredColumns as $column => $colNm) {
			if (!$this->params[$column]) {
				$missing[] = $colNm;
			}
		}
		
		if ($missing) {
			throw new GoodsAdminException("필수 입력 항목 누락 - ".implode(",", $missing));
		}
		
		/**
			판매가, 소비자가 데이터 형식(숫자) 체크 
			숫자가 아니면 0
		*/
		if (!is_numeric($this->params['salePrice'])) $this->params['salePrice'] = 0;
		if (!is_numeric($this->params['consumerPrice'])) $this->params['consumerPrice'] = 0;
		
		return $this;
	}
	
	/**
	* 상품 등록 
	*
	* @return Integer|Boolean 등록 성공 - 상품번호 반환(goodsNo), 실패 - false
	*/
	public function register()
	{
		$inData = $this->getCommonColumns($this->params);
		
		$goodsNo = db()->table("goods")->data($inData)->insert();
		
		if ($goodsNo) {
			// 옵션 처리
			$this->updateOption($goodsNo);
		}
		
		return $goodsNo;
	}
	
	/**
	* 상품 수정 
	*
	* @return Boolean 
	*/
	public function update()
	{
		$upData = $this->getCommonColumns($this->params);
		$upData['modDt'] = date("Y-m-d H:i:s");
		
		$result = db()->table("goods")
						  ->data($upData)
						  ->where(["goodsNo" => $this->params['goodsNo']])
						  ->update();
		
		if ($result !== false) {
			$this->updateOption($this->params['goodsNo']);
		}
		
		return $result !== false;
	}
	
	/**
	* 상품 등록, 수정 공통 컬럼 
	*
	* @param Array $params 
	* @return Array
	*/
	public function getCommonColumns($params)
	{
		$columns = [
			'gid' => isset($params['gid'])?$params['gid']:gid(),
			'goodsNm' => isset($params['goodsNm'])?$params['goodsNm']:"",
			'shortDescription' => isset($params['shortDescription'])?$params['shortDescription']:"",
			'salePrice' => isset($params['salePrice'])?$params['salePrice']:0,
			'consumerPrice' => isset($params['consumerPrice'])?$params['consumerPrice']:0,
			'description' => isset($params['description'])?$params['description']:"",
			'totalStock' => isset($params['totalStock'])?$params['totalStock']:0,
			'stockOut' => isset($params['stockOut'])?$params['stockOut']:0,
			'isDisplay' => isset($params['isDisplay'])?$params['isDisplay']:1,
			'deliveryNo' => isset($params['deliveryNo'])?$params['deliveryNo']:0,
			'cateCd' => isset($params['cateCd'])?$params['cateCd']:"",
		];
		
		return $columns;
	}
	
	/**
	* 상품 정보 조회 
	*
	* @param Integer $goodsNo 상품 등록번호 '
	* @return Array
	*/
	public function get($goodsNo)
	{
		$data = db()->table("goods")
						->where(["goodsNo" => $goodsNo])
						->row();
		
		if ($data) {
			$delivery = App::load(\Component\Goods\Delivery::class);
			$data['delivery'] = $delivery->get($data['deliveryNo']); // 배송비 정책
			$data['images'] = $this->getImages($data['gid']); //  이미지 
			$data['options'] = $this->getOptions($goodsNo); // 옵션 
		}
		
		return $data;
	}
	
	/**
	* 상품 목록 
	*
	* @param Integer $page 페이지번호
	* @param Integer $limit - 1페이지당 출력 레코드 수 
	* @parma String $qs 페이지 링크 추가용 검색 쿼리 스트링
	*
	* @return Array
	*					list - 상품목록
	*					pagination - 페이지번호
	*					total - 전체 레코드 수 
	*					offset - 현재 레코드 시작점 
	*/
	public function getList($page = 1, $limit = 20, $qs = "")
	{
		$page = $page?$page:1;
		$offset = ($page - 1) * $limit;
		
		// 총 레코드 갯수 
		$table = db()->table("goods");
		if ($this->addWhere) {
			$table->where($this->addWhere);
		}
		$total = $table->count();
				
		$table = db()->table("goods");
		if ($this->addWhere) {
			$table->where($this->addWhere);
		}
		$list = $table 
					  ->orderBy([["regDt", "desc"]])
					  ->limit($limit, $offset)
					  ->rows();
		
		foreach ($list as $k => $li) {
			// 상품 이미지 
			$li['images'] = $this->getImages($li['goodsNo']);
			
			$list[$k] = $li;
		}
		
		$paginator = App::load(\Component\Pagination::class, $page, $limit, $total, $qs);
		
		$pagination = $paginator->getPages();
		
		$data = [
			'list' => $list,
			'pagination' => $pagination,
			'total' => $total,
			'offset' => $offset,
		];
		
		return $data;
	}
	
	/**
	* 상품 삭제 
	*
	* @param Integer $goodsNo 상품 번호 
	* @return Boolean 
	*/
	public function delete($goodsNo)
	{
		/**
			1. 상품 데이터 가져온 후 - O 
			2. 업로드된 이미지 파일 삭제(gid) - O 
			3. 상품 데이터를 데이터베이스에서 삭제 - O
		*/
		
		$data = $this->get($goodsNo);
		if (!$data) return false; // 상품이 존재 X -> false
		
		// 파일 삭제 gid 
		$file = App::load(\Component\File::class);
		$file->deleteByGid($data['gid']);
		
		$result = db()->table("goods")->where(["goodsNo" => $goodsNo])->delete();
		
		return $result;
	}
	
	/**
	* 상품 이미지 추출 
	* 
	* @param Integer|String $goodsNo
	*					숫자 -> 상품번호(goodsNo)
	*					문자 -> 그룹 ID(gid)
	* @return Array
	*/
	public function getImages($goodsNo)
	{
		if (is_numeric($goodsNo)) { // 숫자이면 상품번호
			// goodsNo -> gid 
			$row = db()->table("goods")
						   ->select("gid")
						   ->where(['goodsNo' => $goodsNo])
						   ->row();
			
			if (!$row) return [];
			$gid = $row['gid'];
			
		} else { // goodsNo가 gid인 경우 
			$gid = $goodsNo;
		}
		
		$file = App::load(\Component\File::class);
		$description = $file->getGroupFiles($gid, 'description');
		$main = $file->getGroupFiles($gid, 'main');
		$list = $file->getGroupFiles($gid, 'list');
		
			
		$images = [
			'description' => isset($description['images'])?$description['images']:[],
			'main' => isset($main['images'])?$main['images']:[],
			'list' => isset($list['images'])?$list['images']:[],
		];
		
		return $images;
	}
	
	/**
	* 옵션 저장, 수정 처리 
	*
	* @param Integer $goodsNo 상품번호
	*/
	public function updateOption($goodsNo)
	{
		/**
			1. yh_goods - optNames - 옵션명 update - O 
			2. yh_goodsOption
				- 데이터를 테이블에 맞게 가공 - O 
				
				옵션 항목의 추가/수정/삭제
				  기존에 등록된 옵션을 조회
				  -> 비교 -> 동일 -> 데이터 수정 
				  -> 비교 -> 없으면 -> 데이터 추가
				  -> form에서 넘어온 옵션명명과 DB를 비교 해서 form에 없는 
				      DB 데이터는 삭제 
		*/
		
		$data = $this->params;
		
		// 옵션명이 있는 경우만 처리 
		
		/** 옵션명 yh_goods에 업데이트 S */
		if (!isset($data['optNames']) || !$data['optNames'])
			return false;
		
		$optNames = [];
		foreach ($data['optNames'] as $v) {
			$v = trim($v);
			$optNames[] = $v;
		}
		
		$optNames = array_unique($optNames);
		
		$result = db()->table("goods")
							->data(["optNames" => implode($this->divisionStr, $optNames)])
							->where(["goodsNo" => $goodsNo])
							->update();
		/** 옵션명 yh_goods에 업데이트 E */
		
		/** yh_goodsOption의 테이블에 맞게 데이터 가공 */
		$list = [];
		foreach ($optNames as $index => $optName) {
			foreach ($data['opt_optItem'][$index] as $k => $optItem) {
				$addPrice = $data['opt_addPrice'][$index][$k];
				$stock = $data['opt_stock'][$index][$k];
				$stockOut = $data['opt_stockOut'][$index][$k];
				$isDisplay = $data['opt_isDisplay'][$index][$k];
				
				$d = [
					'goodsNo' => $goodsNo,
					'optName' => $optName,
					'optItem' => $optItem,
					'addPrice' => $addPrice?$addPrice:0,
					'stock' => $stock?$stock:0,
					'stockOut' => $stockOut?1:0,
					'isDisplay' => $isDisplay?1:0,
				];
				
				$list[] = $d;
			}
		}
		
		/** 기존에 등록된 옵션을 조회 S */
		$opts = db()->table("goodsOption")
						->select("optNo, optName, optItem")
						->where(["goodsNo" => $goodsNo])
						->rows();
		/** 기존에 등록된 옵션을 조회 E */
		/** 기존 등록 옵션 비교 -> 추가, 수정 S */
		foreach ($list as $li) {
			$optNo = 0; // 0 이면 추가, 번호가 있으면 수정 
			foreach ($opts as $o) {
				if ($li['optName'] == $o['optName'] && $li['optItem'] == $o['optItem']) { // 이미 존재하는 옵션 
					$optNo = $o['optNo']; // 수정을 위한 optNo를 대입
					break;
				}
			}
			
			$proc = db()->table("goodsOption")->data($li);
			if ($optNo) { // 수정 
				$proc->where(["optNo" => $optNo])->update(); 
			} else { // 추가 
				$proc->insert();
			}
		} // endforeach 
		/** 기존 등록 옵션 비교 -> 추가, 수정 E */
		
		/** 기존 등록 옵션 비교 -> 삭제 S */
		foreach ($opts as $o) {
			$isExists = false;
			foreach ($list as $li) {
				if ($o['optName'] == $li['optName'] && $o['optItem'] == $li['optItem']) {
					$isExists = true; 
					break;
				}
			}
			
			if (!$isExists) { // DB에는 있으나 Form는 없는 데이터 -> 삭제 
				db()->table("goodsOption")->where(["optNo" => $o['optNo']])->delete();
			}
		} // endforeach 
		
		/** 기존 등록 옵션 비교 -> 삭제 E */
	}
	
	/**
	* 상품별 옵션 목록 
	*
	* @param Integer $goodsNo 상품번호
	* @return Array
	*/
	public function getOptions($goodsNo)
	{
		/**
		1. yh_goods -> optNames -> 옵션명 추출  - O 
		
		2. yh_goodsOption - 상품번호로 옵션항목 추출 - O 
		
		3. optName 인덱스로 묶어서 옵션을 배열에 담아서 정리 
		*/
		
		/** 옵션명 추출 S */
		$row = db()->table("goods")
						->select("optNames")
						->where(["goodsNo" => $goodsNo])
						->row();
		// 상품이 없거나, 옵션이 지정되지 않은 단품 상품인 경우는 처리 X 
		if (!$row || !$row['optNames']) return [];
		
		$optNames = explode($this->divisionStr, $row['optNames']);
		/** 옵션명 추출 E */
		
		/** 옵션 항목 추출 S */
		$list = db()->table("goodsOption")
					 ->where(["goodsNo" => $goodsNo])
					 ->orderBy([["regDt", "asc"]])
					 ->rows();		
		/** 옵션 항목 추출 E */
		
		$opts = [];
		foreach ($optNames as $k => $optName) {
			foreach ($list as $li) {
				if ($optName == $li['optName']) { // 같은 옵션명으로 묶어서 담기 
					$opts[$k][] = $li;
				}
			}
		} // endforeach 
		
		$result =  [
			'optNames' => $optNames,
			'opts' => $opts,
		];
		
		return $result;
	}
	
	/**
	* 개별 옵션 정보 추출 
	*
	* @param Integer $optNo 옵션번호
	* @return Array
	*/
	public function getOption($optNo)
	{
		/**
			옵션 테이블 정보
			상품 테이블 판매가
			yh_goodsOption, yh_goods 
		*/
		$config = getConfig();
		$px = $config['prefix'];
		
		$joinTable = [
			'goods' => ["{$px}goodsOption.goodsNo", "{$px}goods.goodsNo", "inner"],
		];
		
		$data = db()->table("goodsOption", $joinTable)
						->select("{$px}goodsOption.*, {$px}goods.salePrice")
						 ->where(["{$px}goodsOption.optNo" => $optNo])
						 ->row();
		return $data;
	}
	
	/**
	* 옵션 전체 삭제 
	*
	* @param Integer $goodsNo 상품번호 
	* @return Boolean 성공 true, 실패 false
	*/
	public function deleteOptions($goodsNo)
	{
		/** 
			1. yh_goods - optNames -> 빈값으로 update 
			2. yh_goodsOptions - goodsNo를 가지고 있는 옵션 삭제 
			
			2번이 실패 -> 1번 이미 실행
			
			SQL 실행 쌓아놓고 
			1, 2  -> 실행 -> 실패 -> 원상태로 rollback
			트랜잭션
		*/
		try {
			db()->beginTransaction();
			
			db()->table("goods")
				->data(["optNames" => ""])
				 ->where(["goodsNo" => $goodsNo])
				 ->update();
			
			db()->table("goodsOption")
				->where(["goodsNo" => $goodsNo])
				->delete();
			
			db()->commit(); // SQL 실행 
			
			return true;
		} catch (\PDOException $e) { // SQL 연속 실행이 실패 
			db()->rollBack(); // 원래 상태로 되돌리기
		}
		
		return false;
	}
	
	/**
	* 분류등록 
	* 
	* @param String $cateCd 분류코드
	* @param String $cateNm 분류명
	*
	* @return Boolean 
	* @throw GoodsAdminException 
	*/
	public function registerCategory($cateCd, $cateNm) 
	{
		/**
		0. 필수 컬럼 체크(빈값이 들어오지 못하도록) - O
		1. 분류코드의 중복 여부 체크  - O 
		2. 분류 코드 양식  - O 
			- 최대 20자 까지만 가능 
			 - 소문자 알파벳 양식 -> 소문자 알파벳 이외의 문자가 들어오면 안되는 패턴 
			
			 
		3. 분류 등록 - O
		*/
		if (!$cateCd) {
			throw new GoodsAdminException("분류코드를 입력하세요.");
		}
		
		if (!$cateNm) {
			throw new GoodsAdminException("분류명을 입력해 주세요.");
		}
		
		// 중복체크 
		$cnt = db()->table("category")->where(["cateCd" => $cateCd])->count();
		if ($cnt > 0) {
			throw new GoodsAdminException("이미 등록된 분류코드 입니다 - ".$cateCd);
		}
		
		// 20자 초과 또는 소문자 알파벳이 아닌 문자가 섞여 있는 경우 
		if (strlen($cateCd) > 20 || preg_match("/[^a-z]/", $cateCd)) {
			throw new GoodsAdminException("분류코드는 20자 이하의 소문자 알파벳으로 입력해 주세요.");
		}
		
		// 분류 등록
		$inData = [
			'cateCd' => $cateCd,
			'cateNm' => $cateNm,
		];
		$result = db()->table("category")->data($inData)->insert();
		
		return $result !== false;
	}
	
	/**
	* 분류 설정 수정 
	*
	* @return Boolean
	* @throw GoodsAdminException
	*/
	public function updateCategory()
	{
		/**
		1. 필수 데이터(분류코드, 분류명) - O 
		2. DB 업데이트  - O
		*/
		if (!isset($this->params['cateCd']) || !$this->params['cateCd']) {
			throw new GoodsAdminException("잘못된 접근입니다.");
		}
		
		if (!isset($this->params['cateNm']) || !$this->params['cateNm']) {
			throw new GoodsAdminException("분류명을 입력해 주세요.");
		}
		
		$upData = [
			'cateNm' => $this->params['cateNm'],
			'isDisplay' => isset($this->params['isDisplay'])?$this->params['isDisplay']:1,
			'listOrder' => isset($this->params['listOrder'])?$this->params['listOrder']:0,
			'modDt' =>date("Y-m-d H:i:s"),
		];
		
		$result = db()->table("category")
						->data($upData)
						->where(["cateCd" => $this->params['cateCd']])
						->update();
		
		return $result !== false;
	}
	
	/**
	* 분류 삭제 
	*
	* @param String $cateCd 분류 코드 
	* @return Boolean
	*/
	public function deleteCategory($cateCd)
	{
		$result = db()->table("category")->where(["cateCd" => $cateCd])->delete();
		
		return $result !== false;
	}
	
	/**
	* 분류 목록 
	*
	* @return Array
	*/
	public function getCategories()
	{
		$list = db()->table("category")
						->orderBy([["listOrder", "desc"], ["regdt", "desc"]])
						->rows();
						
		return $list;
	}
}