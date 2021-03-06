<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of greenworld
 *
 * @author 李泓承 
 */
session_start();
define("_iMODULE_NAME_ALIPAY_","greenworld_alipay"); //設定 Class的name。
define("_iMODULE_CHINESE_NAME_ALIPAY_","綠界－支付寶繳費"); 
define("_iMODULE_DESCRUPTION_ALIPAY_","提供管理者在Prestashop網站上，使用綠界科技的支付寶繳費"); 
define("_iMODULE_UNINSTALL_MESSAGE_ALIPAY_","Are you sure you want to delete your details ?"); 
define("_iMODULE_FIRST_TITLE_ALIPAY_","使用ALIPAY_"); 
define("_iMODULE_VERSION_ALIPAY_","1.0.0"); 

class greenworld_alipay extends PaymentModule {
    private $_html='';
    private $_postErrors=array();
    private $_encryption_code;
    private $_shop_code;
    private $BaseURL;       
    private $expireDay;                                       
                              
    private $setBank;  

    function __construct(){  
        $this->name = _iMODULE_NAME_ALIPAY_;
        
        parent::__construct();
        $this->path=$this->_path;
        $this->tab = 'payments_gateways';  
        $this->version = _iMODULE_VERSION_ALIPAY_;  
        $this->displayName = $this->l(_iMODULE_CHINESE_NAME_ALIPAY_);  
        $this->description = $this->l(_iMODULE_DESCRUPTION_ALIPAY_);
        $this->confirmUninstall = $this->l(_iMODULE_UNINSTALL_MESSAGE_ALIPAY_);    }
        
    public function install(){
        //針對perstashop 的內定Hook 進行註冊 payment 跟 paymentReturn
        if (!parent::install() 
                OR !$this->registerHook('payment')
                OR !$this->registerHook('paymentReturn')
                )
            return false;

    }
    public function getEncryptionCode(){
        $encryption_code=Configuration::get('gw_alipay_encryption');
        return $encryption_code;
    }
	
    public function getAmount($order_id){
       
        $amount =    Db::getInstance()->execute('
                        select total_paid from  `'._DB_PREFIX_.'orders`
                        WHERE `id_order` = '.$order_id);
		
        
        return $amount;
                
    }
	
    public function uninstall(){
        if (!Configuration::deleteByName('gw_alipay_encryption')
            OR !Configuration::deleteByName('gw_alipay_mer_id')
            OR !parent::uninstall())
            return false;
    }
    private function _displayForm(){
        $mer_id=Configuration::get('gw_alipay_mer_id');
        $encryption_code=Configuration::get('gw_alipay_encryption');
        $this->_html .= '  
        <form action="'.$_SERVER['REQUEST_URI'].'" method="post">  
                <div class="margin-form">  
                    商店代號：<input type="text" name="shop_code" value="'.$mer_id.'" /><br />
                    商家檢查碼：<input type="text" name="encryption_code" value="'.$encryption_code.'" /><br />
                </div>  
                <input type="submit" name="submit" value="'.$this->l('Save Setting').'" class="button" />  
        </form>';
    }  
    
    public function getContent(){
        if (Tools::isSubmit('submit')){  
            if(!sizeof ($this->_postErrors)){
                $t=getdate();
                $date=$t["year"]."-".$t["mon"]."-".$t["mday"]."(".$t["hours"].":".$t["minutes"].":".$t["seconds"].")";
                Configuration::updateValue('gw_alipay_mer_id', Tools::getValue('shop_code'),true);
                Configuration::updateValue('gw_alipay_encryption', Tools::getValue('encryption_code'),true);
                $this->_html.='<div class="conf confirm">'.$this->l('Success Updata')."   ".$date.'</div>';
            }else{
                foreach ($this->_postErrors AS $err){
                    $this->_html.='<div class"alert error">'.$err."</div>";
                }
            }
        } 
        //如果Configuration資料庫已經有greenworld_vacc_encryption_code 與 greenworld_vacc_shop_code
        //的值，則直接抓取出來資料並顯示。
        if(!Configuration::get($this->name.'_encry_code')==null){
            $this->_encryption_code=Configuration::get($this->name.'_encry_code');
        }else{
            $this->_encryption_code='Enter Encryption Code';
        }
        if(!Configuration::get($this->name.'_shop_code')==null){
            $this->_shop_code=Configuration::get($this->name.'_shop_code');
        }else{
            $this->_shop_code='Enter Shop Code';
        }
        $this->_displayForm();

        return $this->_html;  
    }
     public function hookPayment($params){
        if (!$this->active)
                return;

        $this->smarty->assign(array(
                'this_path' => $this->_path,
                'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
        ));
        return $this->display(__FILE__, 'payment.tpl');

     }
     
     public function hookPaymentReturn($params){
         if (!$this->active)
            return ;
        
         global $smarty;
         $smarty->assign(array(
            'tsr' =>  Tools::getValue("tsr"),
            'mer_id'=> Configuration::get('gw_alipay_mer_id'),
            'id_order' =>  Tools::getValue("id_order"),
            'expire_date'=>Tools::getValue("expire_date"),
            'amt'=>Tools::getValue("amt"),
            'return_url'=>Tools::getValue("return_url"),
            'name'=>$_SESSION["name"],
            'qty'=>$_SESSION["quantity"],
            
          ));

          //print_r($smarty);
          return $this->display(__FILE__, 'thankyouPage.tpl');
          
          //unset($_SESSION["name"]);
          //unset($_SESSION["quantity"]);
        
     }


}  


// End of: greenworld.php  
?>  

