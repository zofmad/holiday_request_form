<?php

//
// namespace rtb;



/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//plik do laczenia z baza danych

$serverName='mysql.cba.pl';
//
$userName='zmadej';
$password='Dysonans2';
$baseName='zmadej';



$conn=new mysqli($serverName,
        $userName,$password, $baseName);

if($conn->connect_error){
    die('Błąd przy połączeniu do'
            . ' bazy danych $baseName:'.$conn->connect_error);


}
$conn->set_charset("utf8");
//echo '<br>Połączenie z bazą danych udane<br>';
