<?php
$_key = 'grre%tGRG!@#!xnew*&32!';
$password = 'testtest';
$crypted = 'ifL+Lczfdnkdcn1MaU/MW/nHdIMbZU19QBnjbipGEkk=';//mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $_key, $password, MCRYPT_MODE_ECB);
$crypted = base64_decode($crypted);
var_dump($crypted);
$decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $_key, $crypted, MCRYPT_MODE_ECB);
var_dump($decrypted);
?>
