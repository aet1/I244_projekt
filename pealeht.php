<?php
require_once ('funktsioonid.php');
session_start();
connect_db();

$page="login";

if (isset($_GET['page']) && $_GET['page']!=""){
    $page=htmlspecialchars($_GET['page']);

}
include_once ("views/head.html");

switch ($page) {
    case 'login':
        login();
        break;
    case 'sisselogitud':
        sisselogitud();
        break;
    case 'registreeri':
        registreeri();
        break;
    case 'trennid':
        trennid();
        break;
    case 'lisa_trenn':
        lisa_trenn();
        break;
    default:
        login();
        break;
}


include_once("views/foot.html");
?>