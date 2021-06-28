
$(function() {
	// 주문서의 주소 검색 
	$(".search_receiver_address").click(function() {
		juso.popup(function(data) {
			if (data) {
				$("input[name='zipcode']").val(data.zonecode);
				$("input[name='receiverAddress']").val(data.address);
				
				$("input[name='receiverAddressSub']").focus();
			}
		});
	});
	
	// 주문자 정보와 동일
	$("#same_with_order_info").click(function() {
		// 체크 되어 있을때 
		if ($(this).prop("checked")) {
			frmOrder.receiverName.value = frmOrder.nameOrder.value;
			frmOrder.receiverCellphone.value = frmOrder.cellPhoneOrder.value;
			frmOrder.zipcode.value = $(".zipcode").val();
			frmOrder.receiverAddress.value = $(".address").val();
			frmOrder.receiverAddressSub.value = $(".addressSub").val();
		}
	});
	
	/** 결제 수단 선택 처리 */
	$(".settleKind").click(function() {
		$(".bank_transfer_pay").removeClass("dn").addClass("dn");
		if ($(this).val() == '무통장입금') {
			$(".bank_transfer_pay").removeClass("dn");
		}
	});
});