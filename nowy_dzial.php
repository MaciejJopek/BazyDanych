<?php
session_start();
include_once 'unsety.php';
if(!isset($_SESSION['zalogowany'])){
    header('Location:index.php');
    exit();
}
include_once 'unsety.php';
if(isset($_POST['dzial']))  
	{  
        $wszystko_ok = true;
        include 'sanityzacja.php';
        $dzial = sanityzacja($_POST['dzial']);

        require_once "connect.php";
        mysqli_report(MYSQLI_REPORT_STRICT);
        try{
            $polaczenie = new mysqli($host,$db_user,$db_password,$db_name);
            $polaczenie->query("SET NAMES 'utf8'");
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
                            $_SESSION['Done_nowy_dział'] = 'Nowy dział został dodany';
                            header('Location:zarz_dzialami.php');
                        }
                    else{
                        throw new Exception($polaczenie->error);
                        }
                }
                catch(Exception $error){
                    $blad = $polaczenie->errno;
                    if ($blad = 1062){
                        $_SESSION['Duplicat_nowy_dział']= "Przepraszamy, podany dział istnieje już w bazie danych biblioteki";
                    }
                    else{
                        $_SESSION['Error'] = "Przepraszamy, występują chwilowe problemy z bazą danych";
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
	<link href="https://fonts.googleapis.com/css2?family=Cormorant:ital,wght@1,700&display=swap" rel="stylesheet">
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
        <h2 class="naglowek" style="display: inline;">Dodaj nowy dzał</h2>
    </div>
    <?php
        if (isset($_SESSION['Duplicat_nowy_dział']))
        {
            echo '<div class="alert alert-danger">'.$_SESSION['Duplicat_nowy_dział'].'</div>';
            unset ($_SESSION['Duplicat_nowy_dział']);

        }
        if (isset($_SESSION['Error']))
        {
            echo '<div class="alert alert-danger">'.$_SESSION['Error'].'</div>';
            unset ($_SESSION['Error']);

        }
            ?>
        <div class="wprowadzanie_danych" style="margin-top: 2%;">
            <form action=""  method="post">
                <div class="form-group">
                <label for="wydawnictwo">Podaj nowy dział:</label>
                <input type="input" class="form-control" id="dzial" placeholder="Nazwa" name="dzial" required>
                </div>
                <button type="submit" name="aktualizuj_dzial"  class="btn btn-success">Dodaj</button>
            </form>
        </div>
    </div>
</div>
<?php
include 'footer.php'
?>
</body>
</html>