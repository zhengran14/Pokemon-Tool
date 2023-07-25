<?php

function openDB()
{
    $DBurl = '127.0.0.1';
    $DBuser = 'root';
    $DBpass = '';
    $DBname = 'pokedex';
    $mysqli = new mysqli($DBurl, $DBuser, $DBpass, $DBname);
    if ($mysqli->connect_errno) {
        $returnJson['result'] = false;
        $returnJson['msg'] = 'Could not connect: ' . $mysqli->connect_error;
        die(json_encode($returnJson));
    }
    $mysqli->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
    return $mysqli;
}