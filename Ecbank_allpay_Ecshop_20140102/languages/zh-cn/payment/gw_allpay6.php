<!DOCTYPE html>
<?php
/**
 * ECSHOP 云网支付@网关插件
 * ============================================================================
 * 版权所有 (C) 2005-2007 北京亿商互动科技发展有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com
 * ----------------------------------------------------------------------------
 * 这是一个免费开源的软件；这意味着您可以在不用于商业目的的前提下对程序代码
 * 进行修改、使用和再发布。
 * ============================================================================
 * @author:     CHNWAY <chnway@gmail.com>
 * @version:    v2.1
 * @website:	www.chnway.cn
 * ---------------------------------------------
 * $Author ID: chzfz  $
 * $Date: 2007年7月17日  7:37:16 ) $
 */

global $_LANG;
$_LANG['gw_allpay6_stage'] = '6';
$_LANG['gw_allpay6'] = '<font color=blue>歐付寶 ALLPAY 線上金流['.$_LANG['gw_allpay6_stage'].' 期分期付款]</font>';
$_LANG['gw_allpay6_desc'] = ' 歐付寶 ALLPAY 線上金流是台灣合作收單銀行最多的信用卡線上支付公司, 交易過程經過 SSL 128 bits 加密保護<br/><br/>';
$_LANG['gw_allpay6_account'] = '商店代號(必填)';
$_LANG['gw_allpay6_checkcode'] = '歐付寶 ALLPAY 檢查碼(必填)';
//$_LANG['gw_allpay6_banktype'] = '指定支付银行(选填)';
//$_LANG['gw_allpay6_banktype_desc'] = '如果你目前缺少某个银行的支付接口，可以选用此项，对应的银行代码参考云网支付网关技术手册，默认为全部银行';
$_LANG['gw_allpay6_installment'] = ''.$_LANG['gw_allpay6_stage'].'期分期手續費率';
$_LANG['gw_allpay6_installment_desc'] = '例如您跟銀行間的費率是 3% , 請填 0.03';
$_LANG['gw_allpay6_language'] = '付款界面語言';
$_LANG['gw_allpay6_language_desc'] = '出現在付款界面的語言';
$_LANG['gw_allpay6_language_range'][0] = '繁體中文';
$_LANG['gw_allpay6_language_range'][1] = 'English';
$_LANG['pay_button'] = '進行線上信用卡';
$_LANG['gw_allpay6_inv_active']='是否啟動電子發票';
$_LANG['gw_allpay6_inv_active_range'][0]='關閉';
$_LANG['gw_allpay6_inv_active_range'][1]='開啟';
$_LANG['gw_allpay6_inv_mer_id']='電子發票系統的商店代號';
?>