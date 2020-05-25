<?php
session_start();
if(!isset($_POST['login']) || (!isset($_POST['haslo'])) ){
    header('Location:index.php');
    exit();
}
require_once "connect.php";

$polaczenie = new mysqli($host,$db_user,$db_password,$db_name);

$login = $_POST['login'];
$haslo = $_POST['haslo'];

$login = htmlentities($login,ENT_QUOTES,"UTF-8");

$sql = "SELECT * from bibliotekarz WHERE login='$login' and haslo = '$haslo'";

$rezultat = $polaczenie->query(
    sprintf("SELECT * from bibliotekarz WHERE login='%s'",
    mysqli_real_escape_string($polaczenie,$login)));

$walidacja = $rezultat->num_rows; // metoda ta zwraca nam liczba zwroconych wierszy

if ($walidacja>0){
    $wiersz = $rezultat -> fetch_assoc();
    if (md5($haslo)== $wiersz['haslo'])
    {
        $_SESSION["zalogowany"] = true;
        $_SESSION['id'] = $wiersz['id'];
        $_SESSION['imie'] = $wiersz['imie'];
        $_SESSION['nazwisko'] = $wiersz['nazwisko'];
        $rezultat->close();
        unset($_SESSION['Blad']);
        header('Location:panel.php');
    }
    else{
        $_SESSION['Blad'] = 'Nieprawidłowe haslo';
        header('Location:index.php');
    }
}
else{
    $_SESSION['Blad'] = 'Nieprawidłowy login lub hasło';
    header('Location:index.php');
}
$polaczenie->close();
?>