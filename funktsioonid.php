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
    } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (empty($_POST['user'])) $errors['no_username'] = "Sisesta kasutajanimi!";
        if (empty($_POST['pass'])) $errors['no_password'] = "Sisesta parool!";
        $username = mysqli_real_escape_string($connection, $_POST['user']);
        $password = mysqli_real_escape_string($connection, $_POST['pass']);
        $query_user = "SELECT username FROM audusaar_trenn_kasutajad WHERE username = '".$username."' AND passw = SHA1('".$password."')";
        $result = mysqli_query($connection, $query_user);
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $_SESSION['user'] = $row['username'];
            header("Location: ?page=sisselogitud");
        } else $errors['vale'] = "Vale kasutajanimi v�i parool";
    }

}

function registreeri() {
    global $connection;

    if (!empty($_POST)) {
        $errors = array();
        if (empty($_POST['eesnimi'])) {
            $errors[] = "Eesnimi sisestamata!";
        }
        if (empty($_POST['perenimi'])) {
            $errors[] = "Perekonnanimi sisestamata!";
        }
        if (empty($_POST['kasutajanimi'])) {
            $errors[] = "Kasutajanimi sisestamata!";
        }
        if (empty($_POST['parool'])) {
            $errors[] = "Parool sisestamata!";
        }
        if (empty($_POST['parool2'])) {
            $errors[] = "Palun korda parooli!";
        }
        if (!empty($_POST['parool']) && !empty($_POST['parool2']) && $_POST['parool'] != $_POST['parool2']) {
            $errors[] = "Paroolid peavad olema �hesugused!";
        }
        if (empty($errors)) {
            $eesn = mysqli_real_escape_string($connection, $_POST['eesnimi']);
            $peren = mysqli_real_escape_string($connection, $_POST['perenimi']);
            $kasutaja = mysqli_real_escape_string($connection, $_POST['kasutajanimi']);
            $passw = mysqli_real_escape_string($connection, $_POST['parool']);
            $query = mysqli_query($connection, "SELECT count(*) AS count_rows FROM audusaar_trenn_kasutajad WHERE username='$kasutaja'");
            $row = mysqli_fetch_assoc($query);
            if ($row['count_rows'] == 1) {
                $errors[] = "Selline kasutaja on juba olemas.";
            }
            if (empty($errors)) {
                $query = mysqli_query($connection, "INSERT INTO audusaar_trenn_kasutajad (id, eesnimi, perenimi, username, passw) VALUES ('', '$kasutaja', SHA1('$passw'), '$eesn', '$peren')");
                header("Location: ?page=avaleht");
                exit(0);
            } else {
                $errors[] = "Registreerumine eba�nnestus.";
            }
        }
    }

    include("views/registreeri.html");

}


function sisselogitud() {
    global $connection;
    if (!isset($_SESSION['user']))
        header("Location: ?page=login"); else {
        include('views/head.html');
        include('views/avaleht.html');
        include('views/foot.html');

    }
}




function logout(){
    $_SESSION=array();
    session_destroy();
    header("Location: ?page=login");
}

function trennid() {
    include_once("views/head.html");
    global $connection;
    if(isset($_SESSION['user'])) {
        $kasutaja = $_SESSION['user'];
        $sql_query = "SELECT * FROM 'audusaar_trennid' WHERE user=$kasutaja";
        $trennid = mysqli_query($connection , $sql_query) or die(mysqli_error($connection));
        include('views/trennid.html');
    } else {
        header("Location: ?page=login");
    }
    include_once("views/foot.html");
}

function lisa_trenn() {
    global $connection;
    include_once("views/head.html");
    include('views/lisa_trenn.html');
    include_once("views/foot.html");
    if (!isset($_SESSION['user']))
        header("Location: ?page=login");
     else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (empty($_POST['nimi'])) $errors['no_name'] = "Sisesta nimi!";
        if (empty($_POST['puur'])) $errors['no_cage'] = "Sisesta puuri number!";
        if (empty($_FILES['liik']['name'])) $errors['no_picture'] = "Sisesta pilt!";
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
            echo "eba�nnestus.";
        } else {
            trennid();
        }
        include_once('views/lisa_trenn.html');

    }
    include_once('views/lisa_trenn.html');
}
