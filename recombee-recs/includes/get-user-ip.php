<?php //get user IP address
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	echo $_SERVER['HTTP_CLIENT_IP'];
}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	if( strpos( $_SERVER['HTTP_X_FORWARDED_FOR'], ',') > 0) {
		$addr = explode( ",", $_SERVER['HTTP_X_FORWARDED_FOR'] );
		$_SERVER['HTTP_X_FORWARDED_FOR'] = trim($addr[0]);
	}
	echo $_SERVER['HTTP_X_FORWARDED_FOR'];
}elseif(!empty($_SERVER['HTTP_X_FORWARDED'])) {
	echo $_SERVER['HTTP_X_FORWARDED'];
}elseif(!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
	echo $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
}elseif(!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
	echo $_SERVER['HTTP_FORWARDED_FOR'];
}elseif(!empty($_SERVER['HTTP_FORWARDED'])) {
	echo $_SERVER['HTTP_FORWARDED'];
}else{
	echo $_SERVER['REMOTE_ADDR'];
}