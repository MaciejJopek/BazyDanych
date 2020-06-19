<?php
session_start();
include_once 'unsety.php';
if(!isset($_SESSION['zalogowany'])){
    header('Location:index.php');
    exit();
}
include_once 'unsety.php';
if(isset($_POST['wydawnictwo']))
{
    $wszystko_ok = true;
    include 'sanityzacja.php';
    $wydawnictwo = sanityzacja($_POST['wydawnictwo']);
    $rok_wydania=sanityzacja($_POST['rok_wydania']);
    $strony=sanityzacja($_POST['liczba_stron']);
    $status='dostępne';
    $isbn = $_POST['isbn'];
    $string=$isbn;
    $wal = str_replace("-", "", $string);
    if(!ctype_digit((string)$rok_wydania)){
        $wszystko_ok = false;
        $_SESSION['Blad_cyfry'] = "Rok wydania może zawierać tylko cyfry";
    }
    if(!ctype_digit((string)$strony) ){
        $wszystko_ok = false;
        $_SESSION['Blad_cyfry'] = "Liczba stron może zawierać tylko cyfry";
    }
    if(!ctype_digit((string)$wal)){
        $wszystko_ok = false;
        $_SESSION['Blad_cyfry_isbn_2'] = "ISBN może zawierać tylko cyfry";

    }
    if ((strlen($wal)!=13 and strlen($wal)!=10 ))
    {
        $wszystko_ok = false;
        $_SESSION['Blad_cyfry_isbn'] = "ISBN musi zawierać 10 lub 13 cyfr";
    }
    if($wszystko_ok==true)
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
                $id_ksiazka = sanityzacja($_SESSION['id_ksiazki']);
                if ($polaczenie->query(
                    sprintf("INSERT INTO egzemplarz VALUES(NULL,'%s','%d','%d','%s','%d','%s')",
                    mysqli_real_escape_string($polaczenie,$wydawnictwo),
                    mysqli_real_escape_string($polaczenie,$rok_wydania),
                    mysqli_real_escape_string($polaczenie,$id_ksiazka),
                    mysqli_real_escape_string($polaczenie,$isbn),
                    mysqli_real_escape_string($polaczenie,$strony),
                    mysqli_real_escape_string($polaczenie,$status)
                    ))){
                        $_SESSION['Done_nowy_egzemplarz'] = 'Nowy egzemplarz został dodany do biblioteki';
                        header('Location:sprawdz_ksiazke.php');
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
        if (isset($_SESSION['Blad_cyfry']))
        {
        echo '<div class="alert alert-danger" style="text-align:center;margin-top:2%">'.$_SESSION['Blad_cyfry'].'</div>';
        unset ($_SESSION['Blad_cyfry']);
        }
        if (isset($_SESSION['Blad_cyfry_isbn_2']))
        {
        echo '<div class="alert alert-danger" style="text-align:center;margin-top:2%">'.$_SESSION['Blad_cyfry_isbn_2'].'</div>';
        unset ($_SESSION['Blad_cyfry_isbn_2']);
        }
        if (isset($_SESSION['Blad_cyfry_isbn_2']))
        {
        echo '<div class="alert alert-danger" style="text-align:center;margin-top:2%">'.$_SESSION['Blad_cyfry_isbn_2'].'</div>';
        unset ($_SESSION['Blad_cyfry_isbn_2']);
        }

    ?>
        <div class="wprowadzanie_danych" style="margin-top: 2%;">
            <form action=""  method="post">
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