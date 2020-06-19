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
        $id_ksiazka = sanityzacja($_SESSION['id_ksiazki']);
        $rezultat = $polaczenie->query(
            sprintf("SELECT tytul,autor,nazwa FROM ksiazka join dzial on id_dzial = dzial_id WHERE id_ksiazka='%d'",
            mysqli_real_escape_string($polaczenie,$id_ksiazka)
        ));
        if(!$rezultat){
            throw new Exception($polaczenie->error);
        }
        else{
            $wiersz = $rezultat -> fetch_assoc();
            $_SESSION['autor'] = $wiersz['autor'];
            $_SESSION['tytul'] = $wiersz['tytul'];
            $_SESSION['nazwa'] = $wiersz['nazwa'];
            $rezultat->close();
        }
        $rezultat_dzial = $polaczenie->query("SELECT * FROM dzial");
        if(!$rezultat_dzial){
            throw new Exception($polaczenie->error);
        }
        $polaczenie->close();
    }
}
catch(Exception $error){
    echo '<span>Błąd serwera, nie można połaczyć się z bazą danych';
}
if(isset($_POST['tytul']) and isset($_POST['aktualizuj_ksiazke'])){
    $tytul = sanityzacja($_POST['tytul']);
    $autor = sanityzacja($_POST['autor']);
    $dzial = $_POST['dzial'];

    try{
        $polaczenie = new mysqli($host,$db_user,$db_password,$db_name);
        if($polaczenie->connect_errno!=0){
            throw new Exception(mysqli_connect_errno());
        }
        else{
            $id_ksiazka = sanityzacja($_SESSION['id_ksiazki']);
            
            if ($polaczenie->query(
                sprintf("UPDATE ksiazka SET autor='%s',tytul='%s',dzial_id='%d' WHERE id_ksiazka='%d'",
                mysqli_real_escape_string($polaczenie,$autor),
                mysqli_real_escape_string($polaczenie,$tytul),
                mysqli_real_escape_string($polaczenie,$dzial),
                mysqli_real_escape_string($polaczenie,$id_ksiazka)
                )))
                {
                    $_SESSION['Done'] = 'Informacje zostały zaktualizowane';
                    header('Location:sprawdz_ksiazke.php');

                }
                $polaczenie->close();
        }
            
        }
    
catch(Exception $error){
        echo '<span>Błąd serwera, nie można połaczyć się z bazą danych';
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
        <h2 class="naglowek">Aktualizuj dane ksiązki</h2>
        <div class="wprowadzanie_danych">
            <form action=""  method="post">
                <div class="form-group">
                <label for="imie">Tytul:</label>
                <input type="text" class="form-control" id="tytul" placeholder="Podaj tytuł" name="tytul" required>
                </div>
                <div class="form-group">
                <label for="autor">Autor:</label>
                <input type="text" class="form-control" id="autor" placeholder="Podaj nazwisko" name="autor" required>
                </div>
                <div class="form-group">
                <label for="dzial">Dzial:</label>
                <select name="dzial" class="custom-select mb-3" id="dzial2">
                            <?php if (mysqli_num_rows($rezultat_dzial) > 0) { 
                            while($row = mysqli_fetch_assoc($rezultat_dzial)) { 
                            ?>
                            <option value="<?php echo $row["id_dzial"] ?>"><?php echo $row["nazwa"] ?></option>
                            <?php }} ?>
                </select>
                </div>
                <button type="submit" name="aktualizuj_ksiazke" class="btn btn-success">Aktualizuj</button>
            </form>
        </div>
    </div>
</div>
<?php
include 'footer.php'
?>
</body>
<script>
    document.getElementById('tytul').value = "<?php echo $_SESSION['tytul']; ?>";
    document.getElementById('autor').value = "<?php echo $_SESSION['autor']; ?>";
</script>
</html>