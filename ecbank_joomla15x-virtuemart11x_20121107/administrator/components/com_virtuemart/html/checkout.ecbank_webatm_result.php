<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/**
* PayPal IPN Result Checker
*
* @version $Id: checkout.result.php 1394 2008-05-04 19:05:15Z soeren_nb $
* @package VirtueMart
* @subpackage html
* @copyright Copyright (C) 2004-2007 soeren - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/

mm_showMyFileName( __FILE__ );

if( !isset( $_REQUEST["order_id"] ) || empty( $_REQUEST["order_id"] )) {
	echo $VM_LANG->_('VM_CHECKOUT_ORDERIDNOTSET');
	
}
else {
	include( CLASSPATH. "payment/ps_ecbank_webatm.cfg.php" );
	$proc_date='';
	$order_id = intval( vmGet( $_REQUEST, "order_id" ));

	$q = "SELECT order_status FROM #__{vm}_orders WHERE ";
	$q .= "#__{vm}_orders.user_id= " . $auth["user_id"] . " ";
	$q .= "AND #__{vm}_orders.order_id= $order_id ";
	$db->query($q);
	if ($db->next_record()) {
		$order_status = $db->f("order_status");
		
		if($order_status == 'C' || $order_status == 'P') {   
        
    	
		//print_r($_REQUEST);
		$proc_date  = trim(stripslashes($_REQUEST['proc_date'])); 
		$proc_time  = trim(stripslashes($_REQUEST['proc_time'])); 
		$tac = trim(stripslashes($_REQUEST['tac']));	
		$tsr = trim(stripslashes($_REQUEST['tsr'])); 	
		$serial = trim($proc_date.$proc_time.$tsr);	
		$checkcode = ECBANK_WEBATM_CHECKCODE;

		$ecbank_gateway = 'https://ecbank.com.tw/web_service/get_outmac_valid.php';
		$post_parm	=	'key='.$checkcode.
						'&serial='.$serial.
						'&tac='.$tac;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$ecbank_gateway);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$post_parm);
		$strAuth = curl_exec($ch);
		if (curl_errno($ch)) $strAuth = false;
		curl_close($ch);

		// 組合字串
 		$serial = trim($proc_date.$proc_time.$tsr);


		$d['order_id'] = $_REQUEST["order_id"];

			if($strAuth == 'valid=1') {
		   	 	$d['order_status'] = 'C';
				echo '<img src="'.VM_THEMEURL.'images/button_ok.png"     align="middle" alt="'.$VM_LANG->_('VM_CHECKOUT_SUCCESS').'" border="0" />';
				echo "付款已經完成";
				require_once ( CLASSPATH . 'ps_order.php' );
			  	$ps_order= new ps_order;
	   			$ps_order->order_status_update($d);
			} else {
		    	$d['order_status'] = 'X';
				echo '<img src="'.VM_THEMEURL.'images/button_cancel.png" align="middle" alt="'.$VM_LANG->_('VM_CHECKOUT_FAILURE').'" border="0" />';
				echo "付款失敗";
        	}
		


			
   
    
    	echo '<br /><p><a href="index.php?option=com_virtuemart&page=account.order_details&order_id='.$order_id.'">'.$VM_LANG->_('PHPSHOP_ORDER_LINK').'</a></p>';
   
	}	else {
		echo $VM_LANG->_('VM_CHECKOUT_ORDERNOTFOUND') . '!';
		
	}
	}
}
?>
