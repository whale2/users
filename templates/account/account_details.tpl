<html>
<body>
  <div>
    <p>Account Name: <b>{$account_name}</b></p>
    <p>Account Role: <b>{if $account_role == constant("Account::ROLE_ADMIN")}Account Administrator{else}User{/if}</b></p>
    <p>Account Status: <b>{if $account_isActive}Active{else}Suspended{/if}</b></p>
    <p>Payment Engine used: <b>{if $account_engine}{$account_engine}{else}None{/if}</b></p>
  </div>

  <div>
    <p>Plan Name: <b>{$plan_name}</b></p>
    <p>Plan Description: {$plan_description}</p>
    <p>Plan Details: <a href="{$plan_details_url}">{$plan_details_url}</a></p>
    {if $plan_downgrade_to}
    <p>Plan automatically downgrades to: <b>{$plan_downgrade_to}</b> 
    	if payment is due more than {$plan_grace_period} day(s)</p>
    {/if}
		{if $schedule_name}
    <p>Payment Schedule: <b>{$schedule_name}</b></p>
    <p>Payment Schedule description: {$schedule_description}</p>
    <p>Charge Amount: <b>${$schedule_charge_amount}</b></p>
    <p>Charge Period: <b>{$schedule_charge_period}</b> days</p>
    {/if}
  </div>
  
  {if count($charges)}
  <div>
  	<p>Account Debts</p>
  	<ul>
  	{$total = 0}
  	{foreach from=$charges item=c}
  		<li>{$c.datetime} ${$c.amount}</li>
  		{$total = $total + $c.amount}
  	{/foreach}
  	</ul>
  	<p>Total debt: <b>${$total}</b></p>
  </div>
  {/if}
</body>
</html>
