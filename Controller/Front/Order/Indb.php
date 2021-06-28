<?php

namespace Controller\Front\Order; 

use App;
use Component\Exception\CartException;
use Component\Exception\OrderException;

/**
* 장바구니, 주문서 DB 처리 
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
			$cart = App::load(\Component\Order\Cart::class);
			$order = App::load(\Component\Order\Order::class);
			
			switch($in['mode']) {
				// 장바구니 수량 변경 
				case "update_goods_cnt" : 
					// 유효성 검사 
					if (!isset($in['cartNo']) || !$in['cartNo'] || !isset($in['goodsCnt']) || !$in['goodsCnt']) {
						throw new \Exception("잘못된 접근입니다.");
					}
					
					$result = $cart->updateGoodsCnt($in['cartNo'], $in['goodsCnt']);
					if (!$result) {
						throw new \Exception("구매수량 변경 실패하였습니다.");
					}
					
					// 수량 변경 성공
					$data = [
						'error' => 0,
					];
					
					header("Content-Type: application/json; charset=utf-8");
					echo json_encode($data);
					break;
				/** 장바구니 요약정보 */
				case "get_summary" : 
					$in['cartNo'] = $in['cartNo'] ?? [];
					$result = $cart->getGoods(0, $in['cartNo']);
					
					$data = [
						'totalGoodsPrice' => $result['totalGoodsPrice'],
						'totalDeliveryPrice' => $result['totalDeliveryPrice'],
						'totalPayPrice' => $result['totalPayPrice'],
					];
					
					header("Content-Type: application/json; charset=utf-8");
					echo json_encode($data);
					break;
				/** 장바구니 상품 삭제 */
				case "delete" : 
					if (!isset($in['cartNo']) || !$in['cartNo']) {
						throw new CartException("삭제할 상품을 선택하세요.");
					}
					
					foreach ($in['cartNo'] as $cartNo) {
						$cart->delete($cartNo);
					}
					
					// 삭제 완료 시 -> 새로고침
					reload("parent");
					break;
				/** 장바구니 상품 주문 */
				case "order" : 
					if (!isset($in['cartNo']) || !$in['cartNo']) {
						throw new CartException("주문할 상품을 선택하세요.");
					}
					
					$qs = [];
					foreach ($in['cartNo'] as $cartNo) {
						$qs[] = "cartNo[]=".$cartNo;
					}
					
					$url = "order/order?".implode("&", $qs);
					go($url, "parent");
					break;
				/** 주문하기 처리 */
				case "order_process" : 
					$orderNo = $order->data($in)
										  ->validator()
										  ->register();
										  
					if ($orderNo === false) {
						throw new OrderException("주문접수 실패");
					}
					
					$url = "order/end?orderNo=".$orderNo;
					go($url, "parent");
					break;
			}
			
			
		} catch (CartException $e) {
			echo $e;
		} catch (OrderException $e) {
			echo $e;
		} catch (\Exception $e) { // ajax 처리시 
			$data = [
				'error' => 1,
				'message' => $e->getMessage(),
			];
			
			header("Content-Type: application/json; charset=utf-8");
			echo json_encode($data);
		}
	}
}