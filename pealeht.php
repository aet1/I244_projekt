<?php
require_once ("views/head.html");

if (!empty($_GET["mode"])) {
    $mode = $_GET["mode"];
} else {
    $mode = "pealeht";
}
switch ($mode) {
    case 'pealeht':
        include("pealeht.php");
        break;
    case 'login':
        include("views/login.php");
        break;
    case 'registreeri':
        include("views/registreeri.html");
        break;
    case 'trennid':
        include("views/trennid.html");
        break;
    default:
        include("pealeht.php");
        break;
}


require_once("views/foot.html");
?>