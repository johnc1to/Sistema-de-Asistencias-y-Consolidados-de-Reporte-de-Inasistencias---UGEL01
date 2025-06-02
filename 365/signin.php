<?php
session_start();

require "vendor/autoload.php";

use myPHPnotes\Microsoft\Auth;

$tenant = "common";
$cliente_id = "ebad6bc2-cc1e-4327-ba89-f62228eb1d0a";
$cliente_secret = "vbE8Q~d.jHYpsNkWTDzTi21xRn.DbPzqNhw2Fbag";
$callback = "https://aplicacion.ugel01.gob.pe/365/callback.php";
$scropes = ["User.Read"];

$microsoft = new Auth($tenant, $cliente_id, $cliente_secret, $callback, $scropes);

header("location: ".$microsoft->getAuthUrl());