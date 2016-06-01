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
    if(!empty($_SESSION["username"])) {
        header("Location: pealeht.php?page=sisselogitud");
    } else {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            if($_POST["password"] == '' || $_POST["username"] == '') {
                $errors = array();
                if(empty($_POST["username"])) {
                    $errors[] = "Sisesta kasutajanimi!";
                }
                if(empty($_POST["password"]))
                    $errors[] = "Sisesta parool!";
            } else {
                $kasutaja = mysqli_real_escape_string ($connection, $_POST["username"]);
                $parool = mysqli_real_escape_string ($connection, $_POST["password"]);
                $query = "SELECT id FROM audusaar_trenn_kasutajad WHERE username='$kasutaja' AND passw=SHA1('$parool')";
                $result = mysqli_query($connection, $query);
                $row = mysqli_num_rows($result);
                if($row) {
                    $_SESSION["username"] = $_POST["username"];
                    header("Location: pealeht.php?page=sisselogitud");
                } else {
                    header("Location: ?page=login");
                }
            }
        }
    }
    include_once('views/login.html');
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
            $errors[] = "Paroolid peavad olema ühesugused!";
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
                $query = mysqli_query($connection, "INSERT INTO audusaar_trenn_kasutajad (id, eesnimi, perenimi, username, passw) VALUES ('', '$eesn', '$peren', '$kasutaja', SHA1('$passw'))");
                header("Location: ?page=avaleht");
                exit(0);
            } else {
                $errors[] = "Registreerumine ebaõnnestus.";
            }
        }
    }
    include_once("views/head.html");
    include("views/registreeri.html");
    include_once("views/foot.html");
}

function sisselogitud() {
    if (!isset($_SESSION['user'])) {
        header("Location: pealeht.php?page=login");
    } else {
        include_once('views/avaleht.html');
    }
}




function logout(){
    $_SESSION=array();
    session_destroy();
    header("Location: ?page=login");
}

function trennid() {
    global $connection;
    if(isset($_SESSION['id'])) {
        $user_id = $_SESSION['id'];
        $sql_query = "SELECT * FROM 'audusaar_trennid' WHERE user_id=$user_id";
        $trennid = mysqli_query($connection , $sql_query) or die(mysqli_error($connection));
        include('views/trennid.html');
    } else {
        header("Location: ?page=login");
    }
}

function lisa_trenn() {
    global $connect;
    $uus_trenn = mysqli_real_escape_string($connect, $_POST["uus_trenn"]);
    $sql = "INSERT INTO audusaar_trennid VALUES ";
    mysqli_query($connect, $sql);

}
