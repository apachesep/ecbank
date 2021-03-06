<?php
/*
GreenWorld http://www.greenworld.com.tw
Date:2005/12/28
Version:2.22
*/



/*
  $Id: payonline.php,v 1.0.0.1 2003/07/03 12:00:03 odie Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  Copyright (c) 2002 osCommerce
  Released under the GNU General Public License

  Traditional Chinese language pack(Big5 code) for osCommerce 2.2 ms1
  Community: http://www.dcezgo.com 
  Author(s): Odie Chiou (odie@dcezgo.com)
  Released under the GNU General Public License ,too!!

*/

  class ecbank_paypal {
    var $code, $title, $description, $enabled;

// class constructor
    function ecbank_paypal() {
      global $order;

      $this->code = 'ecbank_paypal';
      $this->title = MODULE_PAYMENT_ECBANK_PAYPAL_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_ECBANK_PAYPAL_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_PAYMENT_ECBANK_PAYPAL_SORT_ORDER;
      $this->enabled = ((MODULE_PAYMENT_ECBANK_PAYPAL_STATUS == 'True') ? true : false);
      
      if ((int)MODULE_PAYMENT_ECBANK_PAYPAL_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_ECBANK_PAYPAL_ORDER_STATUS_ID;
      }

      if (is_object($order)) $this->update_status();
	
      $this->form_action_url = 'https://ecbank.com.tw/gateway.php';	//中文頁面 Chinese  PayPage
      //$this->form_action_url = 'https://ecpay.com.tw/form_Sc_to5e.php';	//英文頁面 English      Paypage
      
    }

// class methods
    function update_status() {
      global $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_ECBANK_PAYPAL_ZONE > 0) ) {
        $check_flag = false;
        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_ECBANK_PAYPAL_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
        while ($check = tep_db_fetch_array($check_query)) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          } elseif ($check['zone_id'] == $order->billing['zone_id']) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag == false) {
          $this->enabled = false;
        }
      }
    }

    function javascript_validation() {
      return false;
    }

    function selection() {
      return array('id' => $this->code,
                   'module' => $this->title);
    }

    function pre_confirmation_check() {
      return false;
    }

    function confirmation() {
       return array('title' => MODULE_PAYMENT_ECBANK_PAYPAL_TEXT_CONFIRMATION);
    }

    function process_button() {
      global $order ,$mid ;

      
	  $price = $order->info['total'];
     
	  if($price <=25) {
			echo "<Script Language=Javascript>
					alert('歐付寶 ECBank WebATM付款, 最低付款金額為 25 元,按下確定後會返回購物車');
					location(history(-1));
				  </script>";
				  exit;
		} 
	  
	$Currency =MODULE_PAYMENT_ECBANK_PAYPAL_CURRENCY;
	switch ($Currency) {
		case 	'Only TWD(台幣)':	$cur_type='TWD';	break;
		case 	'Only USD(美金)':	$cur_type='USD';	break;
		case 	'Only CAD(加幣)':	$cur_type='CAD';	break;
		case 	'Only EUR(歐元)':	$cur_type='EUR';	break;
		case 	'Only GBP(英磅)':	$cur_type='GBP';	break;
		case 	'Only JPY(日元)':	$cur_type='JPY';	break;
		
	}
	
	
      $ordnum = date('Ymdhis');
      $_SESSION['comments'] .= " ECBank 自訂訂單編號:".$ordnum."";
//      print_r(MODULE_PAYMENT_ECBANK_PAYPAL_CURRENCY); exit;
      //if ( MODULE_PAYMENT_PAYONLIE_RETUREURL != '') {$reurl = 'returl' ; $returl_var= MODULE_PAYMENT_ECBANK_PAYPAL_RETUREURL ; }  
		$roturl=tep_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL');
		$street_address = $order->customer['state'] . $order->customer['city'] .  $order->customer['street_address'] ;
      $process_button_string = 	tep_draw_hidden_field('mer_id', MODULE_PAYMENT_ECBANK_PAYPAL_ID ) .	
      			       			tep_draw_hidden_field('payment_type','paypal') .
								tep_draw_hidden_field('cur_type',$cur_type) .
                               	tep_draw_hidden_field('amt', $price) .
                               	tep_draw_hidden_field('od_sob',$ordnum).
                               	//tep_draw_hidden_field('roturl' , $returl_var ) .
								tep_draw_hidden_field('return_url', $roturl) .						//觸發訂單狀態
                               	tep_draw_hidden_field('item_name', $ordnum ) .                            
                               	tep_draw_hidden_field('Pur_Name', $order->customer['firstname']) .           
                               	tep_draw_hidden_field('Tel_Number', $order->customer['telephone']) .            
                               	tep_draw_hidden_field('ADDress', $street_address) .   
                               	tep_draw_hidden_field('Email', $order->customer['email_address']).
			       				tep_draw_hidden_field('Mobile_Number',$Mobile_Number).
			       				tep_draw_hidden_field('OSType',OSC);
			       										
			       										// tep_draw_hidden_field('LG',UTF8) .
																//tep_draw_hidden_field('You_ID', $customer_id ) . 
																//tep_draw_hidden_field('Roturl' , $returl_var ) . 
																//tep_draw_hidden_field('Invoice_Name',$Invoice_Name).
                               	//tep_draw_hidden_field('Invoice_Num',$Invoice_Num).
																
      return $process_button_string;
    }


    function before_process() {  
 	//print_r($_REQUEST); 
 	 
    $checkcode=MODULE_PAYMENT_ECBANK_PAYPAL_CHECKCODE;
	print_r(MODULE_PAYMENT_ECBANK_PAYPAL_CURRENCY); 
    $serial = trim($_REQUEST['proc_date'].$_REQUEST['proc_time'].$_REQUEST['tsr']);
	
	// 回傳的交易驗證壓碼
		$tac = trim($_REQUEST['tac']);
		// ECBank 驗證Web Service網址
		$ws_url = 'https://ecbank.com.tw/web_service/get_outmac_valid.php?key='.$checkcode.
		          '&serial='.$serial.
		          '&tac='.$tac;
				  
		// 取得驗證結果 (也可以使用curl)
		$tac_valid = file_get_contents($ws_url);		  
		
	//$TOkSi = $_POST[process_time] + $_POST[gwsr] + $_POST[amount];
    //echo "這就是我要的".$TOkSi."<br>";
	//$my_spcheck = gwSpcheck($checkcode,$TOkSi); //商店檢查碼,值
	
	//echo $my_spcheck."<br>".$_POST['spcheck']; exit;
    if($tac_valid == 'valid=1') {
    			if($_REQUEST['succ']==1) {
							return true;
						} else {
							$error = 2;
							$payment_error_return = 'payment_error=' . $this->code . '&error=' . urlencode($error) ;
							tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
							//exit;
						}
				} else {
					$error = 3;	
     		 		$payment_error_return = 'payment_error=' . $this->code . '&error=' . urlencode($error) ;
					tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));	
			//echo $error;
			//exit;
				}	 
		//echo $error;
		//exit;
    	return true;
    
 	}

    function get_error() {
	  	switch ($_GET['error']) {
		  		case 1 :
		  				$error_message = MODULE_PAYMENT_ECBANK_PAYPAL_TEXT_ERROR_1 ;
		  				break ;
		 		 case 2 :
		  				$error_message = MODULE_PAYMENT_ECBANK_PAYPAL_TEXT_ERROR_2 ;
		  				break ;
		  		case 3 :
		  				$error_message = MODULE_PAYMENT_ECBANK_PAYPAL_TEXT_ERROR_3 ;
		 					 break ;
	  	}
      $error = array('title' => MODULE_PAYMENT_ECBANK_PAYPAL_TEXT_ERROR,
                     'error' => $error_message );
      return $error;
    }


    function after_process() {
      return false;
    }

    function output_error() {
      return false;
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_ECBANK_PAYPAL_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) 														 values ('歐付寶ECbank-PayPal 付款 結帳的顯示順序',	 'MODULE_PAYMENT_ECBANK_PAYPAL_SORT_ORDER', 		 '0', 							'歐付寶 ECBank -PayPal 付款結帳的顯示順序，數字小者在前', 					 	'6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('歐付寶ECBank-PayPal 付款 結帳預設定單狀態', 'MODULE_PAYMENT_ECBANK_PAYPAL_ORDER_STATUS_ID', '0', 							'設定使用歐付寶 ECBank -PayPal 付款卡 結帳時，預設的訂單狀態',	'6', '1', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
																																																																																																																					
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('歐付寶ECBank-PayPal 付款 結帳地區', 				 'MODULE_PAYMENT_ECBANK_PAYPAL_ZONE', 					 '0', 							'如果選擇地區，則只有該地區可以使用這個結帳方式', '6', '2', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) 							 values ('啟動 歐付寶ECBank-PayPal 付款模組', 			 'MODULE_PAYMENT_ECBANK_PAYPAL_STATUS', 				 'True', 						'接受 <a target=_BLANK href=http://www.ecbank.com.tw><font color=green>歐付寶ECBank-PayPal付款</font></a> 結帳？', 											'6', '3', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) 														 values ('歐付寶ECBank-PayPal 付商家編號', 						 'MODULE_PAYMENT_ECBANK_PAYPAL_ID', 						 '歐付寶商店代號', 		'請輸入 歐付寶 ECBank -PayPal 付款 商家編號', 											'6', '4', now())");
	  tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Transaction Currency', 'MODULE_PAYMENT_ECBANK_PAYPAL_CURRENCY', '交易國家貨幣別', '這是信用卡線上支付交易', '6', '5', 'tep_cfg_select_option(array(\'請選擇國家貨幣別\',\'Only TWD(台幣)\',\'Only USD(美金)\',\'Only CAD(加幣)\',\'Only EUR(歐元)\',\'Only GBP(英磅)\',\'Only JPY(日元)\'), ', now())");
//tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) 														 values ('歐付寶 ATM 付款 完成回傳網址', 		 'MODULE_PAYMENT_ECBANK_PAYPAL_RETUREURL', 		 	 'http://OSC網站網址/checkout_process.php', 					'請輸入完成刷卡後將要回傳的網址.', 								'6', '5', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) 														 values ('回傳查核設定', 									 'MODULE_PAYMENT_ECBANK_PAYPAL_CHECKCODE',			 '商家檢查碼','填歐付寶 ECbank -PayPal付款的商家檢查碼,可避免偽冒', 							'6', '6', now())");
           
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      //return array('MODULE_PAYMENT_ECBANK_PAYPAL_STATUS', 'MODULE_PAYMENT_ECBANK_PAYPAL_ID', 'MODULE_PAYMENT_ECBANK_PAYPAL_RETUREURL', 'MODULE_PAYMENT_ECBANK_PAYPAL_ZONE', 'MODULE_PAYMENT_ECBANK_PAYPAL_ORDER_STATUS_ID', 'MODULE_PAYMENT_ECBANK_PAYPAL_SORT_ORDER','MODULE_PAYMENT_ECBANK_PAYPAL_CHECKCODE');
	  return array('MODULE_PAYMENT_ECBANK_PAYPAL_STATUS', 'MODULE_PAYMENT_ECBANK_PAYPAL_ID', 'MODULE_PAYMENT_ECBANK_PAYPAL_ZONE', 'MODULE_PAYMENT_ECBANK_PAYPAL_ORDER_STATUS_ID', 'MODULE_PAYMENT_ECBANK_PAYPAL_CURRENCY','MODULE_PAYMENT_ECBANK_PAYPAL_SORT_ORDER','MODULE_PAYMENT_ECBANK_PAYPAL_CHECKCODE');
    }
  }
?>
