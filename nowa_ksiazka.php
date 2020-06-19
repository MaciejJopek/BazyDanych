<?php
session_start();
include_once 'unsety.php';
if(!isset($_SESSION['zalogowany'])){
    header('Location:index.php');
    exit();
}
include_once 'unsety.php';
mysqli_report(MYSQLI_REPORT_STRICT);
require_once "connect.php";
include_once 'sanityzacja.php';
try{
    $polaczenie = new mysqli($host,$db_user,$db_password,$db_name);
    $polaczenie->query("SET NAMES 'utf8'");
    if($polaczenie->connect_errno!=0){
        throw new Exception(mysqli_connect_errno());
    }
    else{
        $rezultat_dzial = $polaczenie->query("SELECT * FROM dzial");
        if(!$rezultat_dzial){
            throw new Exception($polaczenie->error);
        }
    }
}
catch(Exception $error){
    echo '<span>Błąd serwera, nie można połączyć się z bazą danych';
}
if(isset($_POST['autor']) and isset($_POST['dodaj_ksiazke']))  
	{  
        $wszystko_ok = true;
        $dzial = sanityzacja($_POST['dzial']);
        $autor= sanityzacja($_POST['autor']);
        $tytul = sanityzacja($_POST['tytul']);
        try{
            if ($wszystko_ok = true){
                if ($polaczenie->query(
                sprintf("INSERT INTO ksiazka VALUES(NULL,'%s','%s','%d')",
                mysqli_real_escape_string($polaczenie,$autor),
                mysqli_real_escape_string($polaczenie,$tytul),
                mysqli_real_escape_string($polaczenie,$dzial)
                )))
                {
                    $_SESSION['Sukcesy'] = 'Książka została pomyślnie dodana do biblioteki';
                    header('Location:sprawdz_ksiazke.php');
                }
                else{
                    throw new Exception($polaczenie->error);
                }
            }
            $polaczenie->close();
        }
        catch(Exception $error){
            echo '<span>Błąd serwera, nie można połączyć się z bazą danych';
        }
    }
if(isset($_POST['dzial']) and isset($_POST['dodaj_dzial']))  
	{  
        $wszystko_ok = true;
        $dzial = sanityzacja($_POST['dzial']);

        try{
            $polaczenie = new mysqli($host,$db_user,$db_password,$db_name);
            if($polaczenie->connect_errno!=0){
                throw new Exception(mysqli_connect_errno());
            }
            else{
                $rezultat = $polaczenie->query("SELECT id_dzial FROM dzial WHERE nazwa='$dzial'");
                if(!$rezultat){
                    throw new Exception($polaczenie->error);
                }
                try{
                    if ($polaczenie->query(
                        sprintf("INSERT INTO dzial VALUES(NULL,'%s')",
                        mysqli_real_escape_string($polaczenie,$dzial)
                        )))
                        {
                            unset($_SESSION['Blad']);
                            echo "<meta http-equiv='refresh' content='0'>";
                        }
                    else{
                        throw new Exception($polaczenie->error);
                        }
                }
                catch(Exception $error){
                    unset($_SESSION['Sukces']);
                    $blad = $polaczenie->errno;
                    if ($blad = 1062){
                        $_SESSION['Blad'] = '   Przepraszamy, podany dział istnieje już w bazie danych biblioteki';
                    }
                    else{
                        $_SESSION['Blad'] ="   Przepraszamy, napotkano problem z bazą danych";
                    }
        
                } 
                $polaczenie->close();
        }
        }
        catch(Exception $error){
            echo '<span>Błąd serwera, nie można połączyć się z bazą danych';
            echo 'Informacja dla develera'.$error;
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
    <div  id="naglowek_center">
            <h2 class="naglowek" style="display: inline;">Dodaj nową książkę do biblioteki</h2>
    </div>
    <?php
        if (isset($_SESSION['Blad']))
        {
        echo '<div class="alert alert-danger" style="text-align:center;margin-top:2%">'.$_SESSION['Blad'].'</div>';
        unset ($_SESSION['Blad']);
        }
    ?>
        <div class="wprowadzanie_danych">
            <form action=""  method="post">
                <div class="form-group">
                <label for="autor">Autor:</label>
                <input type="autor" class="form-control" id="autor" placeholder="Podaj autora" name="autor" required>
                </div>
                <div class="form-group">
                <label for="tytul">Tytył:</label>
                <input type="tytul" class="form-control" id="tytul" placeholder="Podaj tytuł" name="tytul" required>
                </div>
                <div class="form-group">
                    <label for="dzial">Dział:</label>
                    <div class="input-group mb-3">
                        <select name="dzial" class="custom-select mb-3">
                            <?php if (mysqli_num_rows($rezultat_dzial) > 0) { 
                            while($row = mysqli_fetch_assoc($rezultat_dzial)) { 
                            ?>
                            <option value="<?php echo $row["id_dzial"] ?>"><?php echo $row["nazwa"] ?></option>
                            <?php }} ?>
                        </select>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                                Dodaj
                            </button>
                        </div>
                    </div>
                </div>
                <button type="submit" name="dodaj_ksiazke" class="btn btn-success">Dodaj</button>
            </form>
        </div>
    </div>
</div>
<!-- The Modal -->
<div class="modal" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">
                            
      <div class="modal-header">
        <h4 class="modal-title">Dodaj nowy dział do biblioteki</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form action=""  method="post">
      <div class="modal-body">
            <div class="form-group">
                <label for="dzial">Nazwa działu:</label>
                <input type="dzial" class="form-control" id="dzial" placeholder="Dział" name="dzial" required>
            </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="dodaj_dzial" class="btn btn-success">Submit</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </form>
      </div>

    </div>
  </div>
</div>

<?php
include 'footer.php'
?>
</body>
</html>