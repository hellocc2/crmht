<?php
for ($i = 0; $i < 3000000000000000000000; $i++) {
	$time = date("Y:H:i:s", strtotime("+1 seconds"));
	$day = date("d", time());
	//echo $time;exit;
	//07/Dec/2011:06:35:17
	sleep(2);
	for ($i = 1; $i < 27; $i++) {
		$rd0 = rand('1', '9');
		$rd1 = rand('1', '9');
		$rd2 = rand('1', '9');
		if (strlen($i) == 1) {
			$i = '0' . $i;
		}
		exec("echo 8".$rd0.".3".$rd1.".128.20".$rd2." ^-^ es-sp ^-^ 2".$rd0."17FE7".$rd1."934CD74EBB0D96".$rd2."302A7F003 ^-^ ".$day."/Dec/" . $time . " +0800 ^-^ http:\/\/www.milanoo.com\/es\/c2288 ^-^ ref=http%3A%2F%2Fwww.milanoo.com ^-^ es ^-^ Mozilla\/4.0 \(compatible: MSIE 8.0\; Windows NT 6.1\; Trident\/4.0\; GTB7.2\; SLCC2\; .NET CLR 2.0.50727\; .NET CLR 3.5.30729\; .NET CLR 3.0.30729\; Media Center PC 6.0\; InfoPath.2\; AskTbATU3/5.9.1.14019\) ^-^ ES ^-^ d\/4XIk7TErhdewF3BAVdAg== ^-^ 0 >>fx.log");
	}
}
