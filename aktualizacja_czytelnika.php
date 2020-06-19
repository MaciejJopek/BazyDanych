<?php
session_start();
include_once 'unsety.php';
if(!isset($_SESSION['zalogowany'])){
    header('Location:index.php');
    exit();
}
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
            sprintf("SELECT * FROM czytelnik WHERE id_czytelnik='%d'",
            mysqli_real_escape_string($polaczenie,$kto)
        ));
        if(!$rezultat){
            throw new Exception($polaczenie->error);
        }
        else{
            $wiersz = $rezultat -> fetch_assoc();
            $_SESSION['imie'] = $wiersz['imie'];
            $_SESSION['nazwisko'] = $wiersz['nazwisko'];
            $_SESSION['miasto'] = $wiersz['miasto'];
            $_SESSION['adres'] = $wiersz['adres'];
            $_SESSION['telefon'] = $wiersz['telefon'];
            $rezultat->close();
        }
        $polaczenie->close();
    }
}
catch(Exception $error){
    echo '<span>Błąd serwera, nie można połaczyć się z bazą danych';
}

if(isset($_POST['telefon'])){
    $walidacja = true;
    include 'sanityzacja.php';
    $imie = sanityzacja($_POST['imie']);
    $nazwisko = sanityzacja($_POST['nazwisko']);
    $miasto = sanityzacja($_POST['miasto']);
    $adres= sanityzacja($_POST['adres']);
    $telefon = sanityzacja($_POST['telefon']);

    if(!ctype_digit((string)$telefon)){
        $walidacja = false;
            $_SESSION['Blad'] = "Numer telefon może zawierać tylko cyfry";

    }
    if($walidacja==true)
    {
    try{
        $polaczenie = new mysqli($host,$db_user,$db_password,$db_name);
        if($polaczenie->connect_errno!=0){
            throw new Exception(mysqli_connect_errno());
        }
    else{
        try{
            if ($polaczenie->query(
                sprintf("UPDATE czytelnik SET imie='%s',nazwisko='%s',miasto='%s',adres='%s',telefon='%d' WHERE id_czytelnik='%d'",
                mysqli_real_escape_string($polaczenie,$imie),
                mysqli_real_escape_string($polaczenie,$nazwisko),
                mysqli_real_escape_string($polaczenie,$miasto),
                mysqli_real_escape_string($polaczenie,$adres),
                mysqli_real_escape_string($polaczenie,$telefon),
                mysqli_real_escape_string($polaczenie,$kto)
                )))
                {
                    $_SESSION['Done'] = 'Dane czytelnika zostały zaktualizowane';
                }
            else{
                throw new Exception($polaczenie->error);
                }
        }
        catch(Exception $error){
            $blad = $polaczenie->errno;
            if ($blad = 1062){
                $_SESSION['Duplikat'] = 'Przepraszamy, podany telefon istnieje już w bazie danych biblioteki';
            }
            else{
                $_SESSION['Duplikat'] ='Przepraszamy, napotkano problem z bazą danych';
            }

        } 
        $polaczenie->close();
        }
    }
    catch(Exception $error){
        echo '<span>Błąd serwera, nie można połaczyć się z bazą danych';
    }}
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
        <h2 class="naglowek">Aktualizuj dane czytelnika</h2>
        <?php
        if (isset($_SESSION['Done'])){
            echo '<div class="alert alert-success">'.$_SESSION['Done'].'</div>';
            unset($_SESSION['Done']);
        }
        if (isset($_SESSION['Duplikat'])){
            echo '<div class="alert alert-danger">'.$_SESSION['Duplikat'].'</div>';
            unset($_SESSION['Duplikat']);
        }
        if (isset($_SESSION['Blad'])){
            echo '<div class="alert alert-danger">'.$_SESSION['Blad'].'</div>';
            unset($_SESSION['Blad']);
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
                <label for="miasto">Miasto:</label>
                <input type="miasto" class="form-control" id="miasto" placeholder="Podaj miasto" name="miasto" required>
                </div>
                <div class="form-group">
                <label for="adres">Adres:</label>
                <input type="adres" class="form-control" id="adres" placeholder="Podaj adres" name="adres" required>
                </div>
                <div class="form-group">
                <label for="telefon">Telefon:</label>
                <input type="telefon" class="form-control" id="telefon" placeholder="Podaj telefon" name="telefon" required>
                </div>
                <button type="submit" class="btn btn-success">Zapisz</button>
            </form>
        </div>
    </div>
</div>
<?php
include 'footer.php'
?>
</body>
</html>
<script>
    document.getElementById('imie').value = "<?php echo $_SESSION['imie']; ?>";
    document.getElementById('nazwisko').value = "<?php echo $_SESSION['nazwisko']; ?>";
    document.getElementById('miasto').value = "<?php echo $_SESSION['miasto']; ?>";
    document.getElementById('adres').value = "<?php echo $_SESSION['adres']; ?>";
    document.getElementById('telefon').value = "<?php echo $_SESSION['telefon']; ?>";
</script>