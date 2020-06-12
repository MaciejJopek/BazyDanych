<?php
session_start();
include_once 'unsety.php';
if(!isset($_SESSION['zalogowany'])){
    header('Location:index.php');
    exit();
}
include_once 'unsety.php';
if(isset($_POST['tytul']))
{
    $wszystko_ok = true;
    include 'sanityzacja.php';
    $tytul = sanityzacja($_POST['tytul']);
    $wydawnictwo = sanityzacja($_POST['wydawnictwo']);
    $rok_wydania=sanityzacja($_POST['rok_wydania']);
    $strony=sanityzacja($_POST['liczba_stron']);
    $status='dostępne';
    $isbn = $_POST['isbn'];
    if(!ctype_digit((string)$rok_wydania)){
        $wszystko_ok = false;
        $_SESSION['Blad'] = "Rok wydania może zawierać tylko cyfry";
    }
    if(!ctype_digit((string)$strony) ){
        $wszystko_ok = false;
        $_SESSION['Blad'] = "Liczba stron może zawierać tylko cyfry";
    }
    if($wszystko_ok==true)
    {
        require_once "connect.php";
        mysqli_report(MYSQLI_REPORT_STRICT);
        try{
            $polaczenie = new mysqli($host,$db_user,$db_password,$db_name);
            if($polaczenie->connect_errno!=0){
                throw new Exception(mysqli_connect_errno());
            }
            else{
                $rezultat = $polaczenie->query(
                    sprintf("SELECT id_ksiazka FROM ksiazka WHERE tytul='$tytul'",
                    mysqli_real_escape_string($polaczenie,$tytul)
                ));
                $walidacja = $rezultat->num_rows;
                if ($walidacja>0)
                {
                    $wiersz = $rezultat -> fetch_assoc();
                    $id_ksiazka = $wiersz['id_ksiazka'];
                    if ($polaczenie->query(
                        sprintf("INSERT INTO egzemplarz VALUES(NULL,'%s','%d','%d','%s','%d','%s')",
                        mysqli_real_escape_string($polaczenie,$wydawnictwo),
                        mysqli_real_escape_string($polaczenie,$rok_wydania),
                        mysqli_real_escape_string($polaczenie,$id_ksiazka),
                        mysqli_real_escape_string($polaczenie,$isbn),
                        mysqli_real_escape_string($polaczenie,$strony),
                        mysqli_real_escape_string($polaczenie,$status)
                        ))){
                            $_SESSION['Sukces']="Dodano egzemplarz do biblioteki.";
                        }
                }
                else{
                    $_SESSION['Blad']="W bibliotece nie znajduję się książka o podanym tytule,
                    prosimy w pierwszej kolejności dodać książkę, a następnie nowy egzemplarz.";
                }
            }
            $polaczenie->close();

        }
        catch(Exception $error){
            $_SESSION['Blad']="Przepraszamy, występują chwilowe problemy z bazą danych.";
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
include 'nav.php';
?>
<div class="opakowanie">
    <div class="container" >
    <div  id="naglowek_center" >
        <h2 class="naglowek" style="display: inline;">Dodaj nowy egzemplarz ksiązki do biblioteki</h2>
    </div>
    <?php
        if (isset($_SESSION['Blad']))
        {
        echo '<div class="alert alert-danger" style="text-align:center;margin-top:2%">'.$_SESSION['Blad'].'</div>';
        unset ($_SESSION['Blad']);
        }
        if (isset($_SESSION['Sukces']))
        {
        echo '<div class="alert alert-success" style="text-align:center;margin-top:2%">'.$_SESSION['Sukces'].'</div>';
        unset ($_SESSION['Sukces']);
        }
    ?>
        <div class="wprowadzanie_danych" style="margin-top: 2%;">
            <form action=""  method="post">
                <div class="form-group">
                <label for="tytul">Tytuł:</label>
                <input type="imie" class="form-control" id="tytul" placeholder="Podaj tytuł" name="tytul" required>
                </div>
                <div class="form-group">
                <label for="wydawnictwo">Wydawnictwo:</label>
                <input type="nazwisko" class="form-control" id="wydawnictwo" placeholder="Podaj nazwę wydawnictwa" name="wydawnictwo" required>
                </div>
                <div class="form-group">
                <label for="rok_wydania">Rok wydania:</label>
                <input type="miasto" class="form-control" id="rok_wydania" placeholder="Podaj rok wydania" name="rok_wydania" required>
                </div>
                <div class="form-group">
                <label for="isbn">ISBN:</label>
                <input type="adres" class="form-control" id="isbn" placeholder="Podaj ISBN" name="isbn" required>
                </div>
                <div class="form-group">
                <label for="liczba_stron">Liczba stron:</label>
                <input type="telefon" class="form-control" id="liczba_stron" placeholder="Podaj liczbę stron" name="liczba_stron" required>
                </div>
                <button type="submit" class="btn btn-success">Dodaj</button>
            </form>
        </div>
    </div>
</div>
<?php
include 'footer.php'
?>
</body>
</html>