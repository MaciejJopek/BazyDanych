<?php
session_start();
include_once 'unsety.php';
if(!isset($_SESSION['zalogowany'])){
    header('Location:index.php');
    exit();
}
include_once 'sanityzacja.php';
require_once "connect.php";
mysqli_report(MYSQLI_REPORT_STRICT);
try{
    $polaczenie = new mysqli($host,$db_user,$db_password,$db_name);
    $polaczenie->query("SET NAMES 'utf8'");
       if($polaczenie->connect_errno!=0){
           throw new Exception(mysqli_connect_errno());
       }
       else{
           $kto = $_SESSION['kto'];
           $rezultat = $polaczenie->query(
           sprintf("SELECT kara FROM czytelnik where id_czytelnik = '%d'",
           mysqli_real_escape_string($polaczenie,$kto)));
           if(!$rezultat){
               throw new Exception($polaczenie->error);
           }
           $wiersz3 = $rezultat -> fetch_assoc();
           $_SESSION['obecna_kara'] = $wiersz3['kara'];
   }
   }
   catch(Exception $error){
       echo '<span>Błąd serwera, nie można połączyć się z bazą danych';
   }
if(isset($_POST['zwroc_pieniadze'])){
    $ilosc_pieniedzy = sanityzacja($_POST['kara_do_zwrotu']);
    $walidacja = true;
    if(!ctype_digit((string)$ilosc_pieniedzy)){
        $walidacja = false;
            $_SESSION['Blad_kara_cyfry'] = "Proszę podać tylko cyfry";
        }
    if($walidacja==true)
    {
        $kto = $_SESSION['kto'];
        $roznica = $_SESSION['obecna_kara']-$ilosc_pieniedzy;
        if ($roznica<0)
        {
            $_SESSION['Blad_kara_cyfry'] = "Podałeś wartość większą od obecnej kary, operacja niedozwolina";
        }
        else
        {
            $polaczenie->query(
                sprintf("UPDATE czytelnik SET kara='%d' WHERE id_czytelnik='%d'",
                mysqli_real_escape_string($polaczenie,$roznica),
                mysqli_real_escape_string($polaczenie,$kto)
            )); 
            $_SESSION['Done_kara'] = 'Karaz została zaktualizowana';
            header('Location:zarz_czytelnikami.php');
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
        <h2 class="naglowek" style="display: inline;">Zwrot pieniędzy</h2>
    </div>
    <?php
        if (isset($_SESSION['Blad_kara_cyfry']))
            {
            echo '<div class="alert alert-danger">'.$_SESSION['Blad_kara_cyfry'].'</div>'; 
            unset ($_SESSION['Blad_kara_cyfry']);       
            }
    ?>
    <div>
        <p style="font-size: 25px;"> Obecna kara: <?php echo $_SESSION['obecna_kara'] ?> zł </p>
    </div>
        <div class="wprowadzanie_danych" style="margin-top: 2%;">
            <form action=""  method="post">
                <div class="form-group">
                <label for="wydawnictwo">Ile pieniędzy wpłaca użytkownik:</label>
                <input type="input" class="form-control" id="kara" placeholder="Podaj kwotę w złotówkach" name="kara_do_zwrotu" required>
                </div>
                <button type="submit" name="zwroc_pieniadze"  class="btn btn-success">Zapłać</button>
            </form>
        </div>
    </div>
</div>
<?php
include 'footer.php'
?>

</body>
</html>