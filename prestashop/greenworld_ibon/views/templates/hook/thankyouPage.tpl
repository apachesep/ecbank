
<form action="" method="post">
	<input type="hidden" name="confirm" value="1" />
	
	
	
	<table width=100% border=1 align=center cellpadding=3 cellspacing=1>
                   <tr>
                     <td align=center bgcolor=#FFFF99>付款方式</td>
                     <td width=235 bgcolor=#FFFF99>便利超商代碼繳費</td>
                   </tr><tr>
                   </tr><tr>
                     <td align=center>超商繳費代碼</td><td>{$payno}</td>
                   </tr><tr>
                     <td align=center>繳費金額</td><td>{intval($amt)}元</td>
                   </tr><tr>
                     <td align=center>訂單編號</td><td>{$od_sob}</td>
                   </tr>
                   <tr>
                     <td colspan=2 align=center><a href=http://www.ecbank.com.tw/expenses-ibon.htm target=_blank>統一超商 ibon 門市操作步驟</a></td>
                   </tr>
                   <tr>
                     <td colspan=2>請記下上列超商繳費代碼,至最近之統一超商便利商店,操作代碼繳費機台, <br>
                                   於列印出有條碼之繳款單後,至櫃台支付,便可完成繳費,繳費之收據請留存以供 <br>
                                   備核,繳費之後才算完成購物流程</td>
                   </tr>
         </table>
</form>