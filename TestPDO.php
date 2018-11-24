<?php
/**
 * @author liaosp.top
 * @Time: 2018/11/24 -9:20
 * @Version 1.0
 * @Describe:
 * 1:
 * 2:
 * ...
 */
include_once("PDOs.php");


$PDO = PDOs::getInstance($dbHost='localhost', $dbUser ='root', $dbPasswd  ='root', $dbName ='outh2', $dbCharset='');

$data =$PDO->table('oauth_clients')->where("client_id != 'admin'")->get();

print_r($data);

