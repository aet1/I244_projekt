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
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-42000, '/');
    }
    session_destroy();
    header("Location: ?page=login");
}

function trennid() {
    include('views/menu.html');

    global $connection;
    if(isset($_SESSION['user'])) {
        $sql = "SELECT * FROM `audusaar_trennid`";
        $trennid = mysqli_query($connection , $sql) or die(mysqli_error($connection));
        include('views/trennid.html');
    } else {
        include('views/avaleht.html');
    }
}

function lisa_trenn()
{   include('views/menu.html');
    global $connection;
    $errors = array();
    if (!isset($_SESSION['user'])) {
        login();
    } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if ($_POST['ala'] == '' && $_POST['kuup'] == '' && $_POST['kestus'] == '' && $_POST['asukoht'] == '') {
            $errors[] = "Ala, kuupäeva, kestvuse ja asukoha väljad peavad olema täidetud";
            header("Location: ?page=lisa_trenn");
        } else {
            if (empty($_POST['ala'])) $errors[] = "Sisesta ala nimetus!";
            if (empty($_POST['kuup'])) $errors[] = "Sisesta kuupäev!";
            if (empty($_POST['kestus'])) $errors[] = "Sisesta kestus!";
            if (empty($_POST['asukoht'])) $errors[] = "Sisesta asukoht!";
            $ala = mysqli_real_escape_string($connection, $_POST['ala']);
            $kuup = $_POST['kuup'];
            $distants = $_POST['distants'];
            $kestus = $_POST['kestus'];
            $asukoht = mysqli_real_escape_string($connection, $_POST['asukoht']);
            $kommentaar = mysqli_real_escape_string($connection, $_POST['kommentaar']);
            $uus_trenn = "INSERT INTO audusaar_trennid (id, ala, kuup, distants, kestus, asukoht, kommentaar) VALUES (NULL , '$ala', '$kuup', '$distants', '$kestus', '$asukoht','$kommentaar')";

            echo mysqli_insert_id($connection);
            $result = mysqli_query($connection, $uus_trenn);
            if (!$result) {
                echo "<script> alert('Salvestamine ebaõnnestus'); </script>";
            } else {
                echo "<script> alert('Salvestatud!'); </script>";
            }
        }

    }
    include_once('views/lisa_trenn.html');

}