<?php
session_start();
include_once 'sanityzacja.php';
unset($_SESSION['Sukces']);
if(isset($_SESSION['zmienna'])){
    $_SESSION['zmienna']=$_SESSION['zmienna']-1;
}
if(isset($_POST['telefon'])){
    $walidacja = true;
    $imie = sanityzacja($_POST['imie']);
    $nazwisko = sanityzacja($_POST['nazwisko']);
    $telefon = sanityzacja($_POST['telefon']);
    if(!ctype_digit((string)$telefon)){
        $walidacja = false;
        $_SESSION['Blad_cyfr'] = "Numer telefon może zawierać tylko cyfry";
        $_SESSION['zmienna'] = 2;
        echo "<meta http-equiv='refresh' content='0'>";

    }
    if($walidacja==true)
    {
        require_once "connect.php";
        mysqli_report(MYSQLI_REPORT_STRICT);
        try{
            $polaczenie = new mysqli($host,$db_user,$db_password,$db_name);
            $polaczenie->query("SET NAMES 'utf8'");
            if($polaczenie->connect_errno!=0){
                throw new Exception(mysqli_connect_errno());
            }
            else{
                try{
                if($rezultat = $polaczenie->query(
                sprintf("SELECT id_czytelnik,imie,nazwisko FROM czytelnik  WHERE telefon ='%d' and imie = '%s' and nazwisko = '%s';",
                mysqli_real_escape_string($polaczenie,$telefon),
                mysqli_real_escape_string($polaczenie,$imie),
                mysqli_real_escape_string($polaczenie,$nazwisko)
                )))
                {
                    //pobralismy dane z bazy dla danego telefonu, ale jezeli fetch assoc = 0 to error
                    //i nie wykonujemy więcej kodu
                    $wiersz = $rezultat -> fetch_assoc();
                    if ($wiersz == 0){
                        $_SESSION['brak_osoby'] = "Dane nie pasują do żadnego użytkownika biblioteki. Proszę wpisać poprawne dane
                        lub udać się do biblioteki w celu założenia konta";
                    }
                    else{
                        $id_czyte = $wiersz['id_czytelnik'];
                        $id_eg=$_SESSION['wypozycz'];
                        $data = date("Y-m-d");
                        if ($polaczenie->query(
                            sprintf("INSERT INTO wypozyczenie VALUES(NULL,NULL,NULL,'%s','%d','%d')",
                            mysqli_real_escape_string($polaczenie,$data),
                            mysqli_real_escape_string($polaczenie,$id_czyte),
                            mysqli_real_escape_string($polaczenie,$id_eg)
                            )))
                            {
                                $status_eg = 'wypożyczone';
                                if($polaczenie->query(
                                    sprintf("UPDATE egzemplarz SET status='%s' WHERE id_egzemplaz='%d'",
                                    mysqli_real_escape_string($polaczenie,$status_eg),
                                    mysqli_real_escape_string($polaczenie,$id_eg)
                                )))
                                {
                                    $_SESSION['zrobiono_wypoz']="Zamowienie zostało złożone";
                                    header('Location:lista_egzemplarz_bibliotekarz.php');
                                }
                            }
                        else{
                            throw new Exception($polaczenie->error);
                            }
                    }
                }
                else{
                    throw new Exception($polaczenie->error);
                }
                $polaczenie->close();
                }
                catch(Exception $error){
                    echo '<span>Przepraszamy, występują problemy z bazą danych biblioteki';
                }
            }
        }
        catch(Exception $error){
            echo '<span>Błąd serwera, nie można połaczyć się z bazą danych';
        }
    }
}

?>
<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <style>
        <?php include 'styl.css'; ?>
    </style>
	<title>Biblioteka</title>
</head>
<body>
<?php
?>
<?php 
include 'nav.php';
?>
<div class = "opakowanie">
    <div class="container">
        <div  id="naglowek_center">
            <h2 class="naglowek" style="display: inline;">Formularz wypożyczenia ksiązki</h2>
        </div>
        <?php
            if (isset($_SESSION['brak_osoby']))
            {
                echo '<div style="margin-top:2%" class="alert alert-danger">'.$_SESSION['brak_osoby'].'</div>';
                unset ($_SESSION['brak_osoby']);
            }
            if (isset($_SESSION['Blad_cyfr']) and $_SESSION['zmienna']==1)
            {
                echo '<div style="margin-top:2%;text-align:center;" class="alert alert-danger">'.$_SESSION['Blad_cyfr'].'</div>';
                unset ($_SESSION['Blad_cyfr']);
            }
            ?>
        <div class="wprowadzanie_danych">
            <form action=""  method="post">
                <div class="form-group">
                <label for="imie">Imię:</label>
                <input type="imie" class="form-control" id="imie" placeholder="Podaj imię" name="imie" required>
                </div>
                <div class="form-group">
                <label for="nazwisko">Nazwisko:</label>
                <input type="nazwisko" class="form-control" id="nazwisko" placeholder="Podaj nazwisko" name="nazwisko" required>
                </div>
                <div class="form-group">
                <label for="telefon">Telefon:</label>
                <input type="telefon" class="form-control" id="telefon" placeholder="Podaj telefon" name="telefon" required>
                </div>
                <button type="submit" class="btn btn-success">Wypożycz</button>
        </div>
    </div>
    <?php
    include 'footer.php'
    ?>
</div>
</body>
</html>