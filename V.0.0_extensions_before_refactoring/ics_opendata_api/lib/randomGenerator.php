<?php
/**
 * Generates a random password
 *
 * @param $passwordSize integer, length of password
 * @return string, the password
 */
function pwdGenerator( $passwordSize = 10) {

	//-- list of possible characters in the password
	$charList = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	//-- initializes the generator of random values
	mt_srand((double)microtime()*1000000);
	$password="";
	while( strlen( $password )< $passwordSize ) {
		$password .= $charList[mt_rand(0, strlen($charList)-1)];
	}
	return $password;
}
?>