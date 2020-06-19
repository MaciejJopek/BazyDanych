<?php
session_start();
include_once 'unsety.php';
if(!isset($_SESSION['zalogowany'])){
    header('Location:index.php');
    exit();
}
require_once "connect.php";
include_once 'sanityzacja.php';
mysqli_report(MYSQLI_REPORT_STRICT);
try{
    $polaczenie = new mysqli($host,$db_user,$db_password,$db_name);
    $polaczenie->query("SET NAMES 'utf8'");
    if($polaczenie->connect_errno!=0){
        throw new Exception(mysqli_connect_errno());
    }
    else{
        $id_egzemplarz = sanityzacja( $_SESSION['id_egzemplarza_do_aktualizacji']);
        $rezultat = $polaczenie->query(
            sprintf("SELECT wydawnictwo,rok_wydania,ISBN,strony FROM egzemplarz  WHERE id_egzemplaz='%d'",
            mysqli_real_escape_string($polaczenie,$id_egzemplarz)
        ));
        if(!$rezultat){
            throw new Exception($polaczenie->error);
        }
        else{
            $wiersz = $rezultat -> fetch_assoc();
            $_SESSION['wydawnictwo'] = $wiersz['wydawnictwo'];
            $_SESSION['rok_wydania'] = $wiersz['rok_wydania'];
            $_SESSION['ISBN'] = $wiersz['ISBN'];
            $_SESSION['strony'] = $wiersz['strony'];
            $rezultat->close();
        }
        $polaczenie->close();
    }
}
catch(Exception $error){
    echo '<span>Błąd serwera, nie można połaczyć się z bazą danych';
}
if(isset($_POST['wydawnictwo']) and isset($_POST['aktualizuj_egz'])){
    $wydawnictwo = sanityzacja($_POST['wydawnictwo']);
    $rok_wydania = sanityzacja($_POST['rok_wydania']);
    $ISBN = $_POST['isbn'];
    $string=$ISBN;
    $wal = str_replace("-", "", $string);
    $strony = sanityzacja($_POST['liczba_stron']);
    $walidacja = true;  
    if(!ctype_digit((string)$rok_wydania) or !ctype_digit((string)$strony))
    {
        $walidacja = false;
        $_SESSION['Blad_cyfry'] = "Proszę podać tylko cyfry";
        }
    if(!ctype_digit((string)$wal)){
        $walidacja = false;
        $_SESSION['Blad_cyfry'] = "ISBN może zawierać tylko cyfry";

    }
    if ((strlen($wal)!=13 and strlen($wal)!=10 ))
    {
        $walidacja = false;
        $_SESSION['Blad_cyfry'] = "ISBN musi zawierać 10 lub 13 cyfr";
    }
    if($walidacja==TRUE){
    try{
        $polaczenie = new mysqli($host,$db_user,$db_password,$db_name);
        if($polaczenie->connect_errno!=0){
            throw new Exception(mysqli_connect_errno());
        }
        else{
            $id_egz = sanityzacja( $_SESSION['id_egzemplarza_do_aktualizacji']);
            
            if ($polaczenie->query(
                sprintf("UPDATE egzemplarz SET wydawnictwo='%s',rok_wydania='%d',ISBN='%s',strony='%d' WHERE id_egzemplaz='%d'",
                mysqli_real_escape_string($polaczenie,$wydawnictwo),
                mysqli_real_escape_string($polaczenie,$rok_wydania),
                mysqli_real_escape_string($polaczenie,$ISBN),
                mysqli_real_escape_string($polaczenie,$strony),
                mysqli_real_escape_string($polaczenie,$id_egz)
                )))
                {
                    $_SESSION['Done_aktualizacja'] = 'Informacje zostały zaktualizowane';
                    header('Location:lista_egzemplarz_bibliotekarz.php');

                }
            else{
                
                }
                $polaczenie->close();
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
include 'nav.php';
?>
<div class="opakowanie">
    <div class="container" >
    <div  id="naglowek_center" >
        <h2 class="naglowek" style="display: inline;">Aktualizuj egzemplarz</h2>
    </div>
    <?php
        if (isset($_SESSION['Blad_cyfry']))
        {
        echo '<div class="alert alert-danger" style="text-align:center;margin-top:2%">'.$_SESSION['Blad_cyfry'].'</div>';
        unset ($_SESSION['Blad_cyfry']);
        }
        
    ?>
        <div class="wprowadzanie_danych" style="margin-top: 2%;">
            <form action=""  method="post">
                <div class="form-group">
                <label for="wydawnictwo">Wydawnictwo:</label>
                <input type="input" class="form-control" id="wydawnictwo" placeholder="Podaj nazwę wydawnictwa" name="wydawnictwo" required>
                </div>
                <div class="form-group">
                <label for="rok_wydania">Rok wydania:</label>
                <input type="input" class="form-control" id="rok_wydania" placeholder="Podaj rok wydania" name="rok_wydania" required>
                </div>
                <div class="form-group">
                <label for="isbn">ISBN:</label>
                <input type="input" class="form-control" id="isbn" placeholder="Podaj ISBN" name="isbn" required>
                </div>
                <div class="form-group">
                <label for="liczba_stron">Liczba stron:</label>
                <input type="input" class="form-control" id="liczba_stron" placeholder="Podaj liczbę stron" name="liczba_stron" required>
                </div>
                <button type="submit" name="aktualizuj_egz" class="btn btn-success">Aktualizuj</button>
            </form>
        </div>
    </div>
</div>
<?php
include 'footer.php'
?>
<script>
    document.getElementById('wydawnictwo').value = "<?php echo $_SESSION['wydawnictwo']; ?>";
    document.getElementById('rok_wydania').value = "<?php echo $_SESSION['rok_wydania']; ?>";
    document.getElementById('isbn').value = "<?php echo $_SESSION['ISBN']; ?>";
    document.getElementById('liczba_stron').value = "<?php echo $_SESSION['strony']; ?>";
</script>
</body>
</html>