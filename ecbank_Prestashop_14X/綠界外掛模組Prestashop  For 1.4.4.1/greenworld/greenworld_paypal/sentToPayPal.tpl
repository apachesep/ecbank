{capture name=path}{l s='Shipping' mod='greenworld'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h2>{l s='Order summation' mod='greenworld'}</h2>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

<form action="{$URL}" method="post">
	<p>
                您在{$shop_name}訂單已經成立。<BR/>
                訂單編號：<font color="red">{$od_sob}</font>。<BR/>
                金額：<font color="red">{$inttotal}</font>元整。<BR /><BR/>

                請按下方<font color="red">繼續</font>，完成付款程序。<BR /><BR/>

                如果您有相關問題想與系統管理者聯繫，請按 <a href="{$link->getPageLink('contact-form.php', true)}" color="blue">聯繫管理人員</a>。</p>

	</p>
	<p class="cart_navigation">
		<input type="submit" name="submit" value="{l s='繼續' mod='greenworld'}" class="exclusive_large" />
                <input type="hidden" name="mer_id" value="{$mer_id}" class="exclusive_large" />
                <input type="hidden" name="payment_type" value="{$payment_type}" class="exclusive_large" />
                <input type="hidden" name="amt" value="{$amt}" class="exclusive_large" />
                <input type="hidden" name="od_sob" value="{$od_sob}" class="exclusive_large" />
                <input type="hidden" name="return_url" value="{$return_url}" class="exclusive_large" />
                <input type="hidden" name="cancel_url" value="{$cancel_url}" class="exclusive_large" />
                <input type="hidden" name="item_name" value="{$item_name}" class="exclusive_large" />
                <input type="hidden" name="cur_type" value="{$cur_type}" class="exclusive_large" /> 
	</p>
</form>