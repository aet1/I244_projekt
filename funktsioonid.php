<?php


function connect_db(){
    global $connection;
    $host="localhost";
    $user="test";
    $pass="t3st3r123";
    $db="test";
    $connection = mysqli_connect($host, $user, $pass, $db) or die("ei saa ühendust mootoriga- ".mysqli_error());
    mysqli_query($connection, "SET CHARACTER SET UTF8") or die("Ei saanud baasi utf-8-sse - ".mysqli_error($connection));
}

function login(){
    global $connection;
    $errors = array();
    if (isset($_SESSION['user'])) {
        sisselogitud();
    }
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        //Kui meetodiks oli POST, kontrollida kas vormiväljad olid täidetud. Vastavalt vajadusele tekitada veateateid (massiiv $errors)
        if (isset($_POST['user']) && isset($_POST['pass'])) {
            if ($_POST['user'] == "" && $_POST['pass'] == "") {
                $errors[] = "Sisesta kasutajanimi ja salasõna";
            }
            if ($_POST['user'] == "") {
                $errors[] = "Sisesta kasutajanimi";
            }
            if ($_POST['pass'] == "") {
                $errors[] = "Sisesta salasõna";
            }else {
                global $connection;
                $user = mysqli_real_escape_string($connection,htmlspecialchars($_POST['user']));
                $pass = mysqli_real_escape_string($connection,htmlspecialchars($_POST['pass']));
                $sql = "SELECT * FROM audusaar_trenn_kasutajad WHERE username='$user' AND passw=SHA1('$pass')";
                $result = mysqli_query($connection, $sql) or die("Viga");
                if (mysqli_num_rows($result) >= 1) {
                    $row = mysqli_fetch_assoc($result);
                    $_SESSION['user'] = $user;
                    header("Location: ?page=sisselogitud");
                } else {
                    $errors[] = "Kasutajanimi või parool on vale";
                }
            }
        }
    }
    include_once('views/login.html');

}


function sisselogitud() {
    global $connection;
    if (!isset($_SESSION['user']))
        header("Location: ?page=login"); else {

        include ('views/menu.html');
        include('views/avaleht.html');

    }
}




function logout(){
    $_SESSION=array();
    session_destroy();
    header("Location: ?page=login");
}

function trennid() {
    global $connection;
    if(isset($_SESSION['user'])) {
        $sql = "SELECT * FROM `audusaar_trennid`";
        $trennid = mysqli_query($connection , $sql) or die(mysqli_error($connection));
        include('views/menu.html');
        include('views/trennid.html');
    } else {
        header("Location: ?page=trennid");
    }
}

function lisa_trenn() {
    global $connection;
    include('views/menu.html');
    include('views/lisa_trenn.html');
    if (!isset($_SESSION['user']))
        header("Location: ?page=login");
     else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (empty($_POST['ala'])) $errors[] = "Sisesta ala nimetus!";
        if (empty($_POST['kuup'])) $errors[] = "Sisesta kuupäev!";
         if (empty($_POST['distants'])) $errors[] = "Sisesta distants!";
         if (empty($_POST['kestus'])) $errors[] = "Sisesta kestus!";
         if (empty($_POST['asukoht'])) $errors[] = "Sisesta asukoht!";
         if (empty($_POST['kommentaar'])) $errors[] = "Sisesta kommentaar!";
        $ala = mysqli_real_escape_string($connection, $_POST['ala']);
        $kuup = mysqli_real_escape_string($connection, $_POST['kuup']);
        $distants = mysqli_real_escape_string($connection, $_POST['distants']);
        $kestus = mysqli_real_escape_string($connection, $_POST['kestus']);
        $asukoht = mysqli_real_escape_string($connection, $_POST['asukoht']);
        $kommentaar = mysqli_real_escape_string($connection, $_POST['kommentaar']);

        $uus_trenn = "INSERT INTO audusaar_trennid (ala, kuup, distants, kestus, asukoht, kommentaar) VALUES ('$ala', '$kuup', '$distants''$kestus','$asukoht','$kommentaar',)";
        echo mysqli_insert_id($connection);
        $result = mysqli_query($connection, $uus_trenn);
        if (!$result) {
            echo "ebaõnnestus.";
        } else {
            trennid();
        }

        include_once('views/lisa_trenn.html');


    }

    include_once('views/lisa_trenn.html');
}
