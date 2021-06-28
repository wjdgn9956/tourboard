/**
* 상품 상세 
*
*/
const goodsView = {
	/**
	* 옵션 선택 
	*
	* @param Integer optNo - 옵션 선택번호
	*/
	selectOption : function(optNo) {
		if (!optNo) return;
		
		// 이미 추가된 옵션인지 체크 
		$opt = $("#opt_rows_" + optNo);
		if ($opt.length > 0) { // 이미 추가된 옵션인 경우 
			alert("이미 추가된 옵션입니다.");
			return;
		}
		
		$.ajax({
			url : "../goods/ajax", 
			type : "post", 
			data : { mode : "get_options", optNo : optNo },
			dataType : "json", 
			success : function (res) {
				if (res.error == '1' ) { // 에러가 있는 경우 
					alert(res.message);
				} else if (!res.data) { // 옵션이 존재하지 않는 경우 
					alert("존재하지않는 옵션을 선택하였습니다.");
				} else { // 에러가 없는 경우 
					let html = $("#opt_template").html();
					const data = res.data;
					
					const optPrice = Number(data.salePrice) + Number(data.addPrice);
					html = html.replace(/<%optNo%>/g, data.optNo);
					html = html.replace(/<%optItem%>/g, data.optItem);
					html = html.replace(/<%optPrice%>/g, optPrice);
					html = html.replace(/<%optPriceStr%>/g, optPrice.format());
					
					$(".selected_opts").append(html);
					
					goodsView.updateTotalPrice(); // 총 합계 갱신 
				}
			},
			error : function (err) {
				console.error(err);
			}
		});
	}, 
	/**
	* 상품 총합 합계 
	*
	*  select.options -> 옵션 있는 경우
	*	없으면 -> 단품 
	*/
	updateTotalPrice : function() {
		let totalPrice = 0;
		if ($(".goods_top .options").length > 0) { // 옵션 있는 상품 
			$list = $(".optPrice");
			$.each($list, function() {
				const optPrice = Number($(this).val()); // 수량 1개일때 판매가 + 옵션 추가금액
				
				$goodsCnt = $(this).closest(".opt_rows").find(".goodsCnt");
				const cnt = Number($goodsCnt.val());
				
				totalPrice += optPrice * cnt;
			});
			
		} else { // 옵션이 없는 단품 
			const salePrice = Number($(".salePrice").val());
			const goodsCnt = Number($(".goods_top .goodsCnt").val());
			
			totalPrice = salePrice * goodsCnt;
		}

		$(".total_price").text(totalPrice.format());
	}
};

/** swiper 추가 */
const swiperMain = new Swiper(".goods_top .swiper-container", {
	pagination : {
		el: ".goods_top .swiper-pagination",
	}
});

$(function() {
	goodsView.updateTotalPrice();
	
	/** 옵션 선택 처리 */
	$(".goods_top .options").change(function() {
		const optNo = $(this).val();
		if (optNo) {
			goodsView.selectOption(optNo);
		}
	});
	
	/** 옵션 선택 제거 */
	$("body").on("click", ".selected_opts .remove", function() {
		$(this).closest(".opt_rows").remove();
		goodsView.updateTotalPrice();
	});
	
	/** 수량 증감 처리 */
	$("body").on("click", ".goodsCnt_up, .goodsCnt_dn", function() {
		$goodsCnt = $(this).closest(".goodsCnt_wrap").find(".goodsCnt");
		let cnt = Number($goodsCnt.val());
		
		if ($(this).hasClass("goodsCnt_up")) { // 증가 처리 
			cnt++;
		} else { // 감소 처리 
			cnt--;
		}
		
		if (cnt < 1) cnt = 1;
		
		$goodsCnt.val(cnt);
		
		goodsView.updateTotalPrice(); // 총 합계 갱신 
	});
	
	$("body").on("keyup change", ".goodsCnt", function() {
		let cnt = Number($(this).val());
		if (cnt < 1) {
			$(this).val(1);
		}
		goodsView.updateTotalPrice();
	});
	
	/** 장바구니, 바로구매 클릭 처리 */
	$(".buy_btns .btns").click(function() {
		let mode = "cart";
		if ($(this).hasClass("order")) { // 바로구매 버튼 
			mode = "order";
		}
		
		//goodsFrm.mode.value= mode;
		$("#goodsFrm input[name='mode']").val(mode);
		
		$("#goodsFrm").submit();	
	});
	
});