<?php
session_start();
if(!isset($_POST['login']) || (!isset($_POST['haslo'])) ){
    header('Location:index.php');
    exit();
}
require_once "connect.php";
mysqli_report(MYSQLI_REPORT_STRICT);
    try{
        $polaczenie = new mysqli($host,$db_user,$db_password,$db_name);
        if($polaczenie->connect_errno!=0){
            throw new Exception(mysqli_connect_errno());
            }
        else{
            $login = $_POST['login'];
            $haslo = $_POST['haslo'];
            
            $login = htmlentities($login,ENT_QUOTES,"UTF-8");

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
                    $_SESSION['imie_bibliotekarza'] = $wiersz['imie'];
                    $_SESSION['nazwisko_bibliotekarza'] = $wiersz['nazwisko'];
                    $rezultat->close();
                    unset($_SESSION['Blad']);
                    header('Location:panel.php');
                }
                else{
                    $_SESSION['Blad'] = 'Nieprawidłowe haslo';
                    header('Location:zaloguj_layout.php');
                }
            }
            else{
                    $_SESSION['Blad'] = 'Nieprawidłowy login lub hasło';
                    header('Location:zaloguj_layout.php');
                }
            $polaczenie->close();
            }
    }
        catch(Exception $error){
            echo '<span>Błąd serwera, nie można połączyć się z bazą danych';
            //echo 'Informacja dla develera'.$error;
        }
?>