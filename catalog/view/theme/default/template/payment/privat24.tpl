<form method="POST" action="<?php echo $action; ?>">
	<input type="hidden" name="amt" value="<?php echo $amount; ?>" />
	<input type="hidden" name="ccy" value="<?php echo $ccy; ?>" />
	<input type="hidden" name="merchant" value="<?php echo $merchant; ?>" />
	<input type="hidden" name="order" value="<?php echo $order ?>" />
	<input type="hidden" name="details" value="<?php print_r($details); ?>" />
	<input type="hidden" name="ext_details" value="<?php echo $ext_details; ?>" />
	<input type="hidden" name="pay_way" value="privat24" />
	<input type="hidden" name="return_url" value="<?php echo $return_url; ?>" />
	<input type="hidden" name="server_url" value="<?php echo $server_url; ?>" />
	<input type="submit" value="<?php echo $button_confirm; ?>" class="button" />
</form>