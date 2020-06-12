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
                        echo "Baza danych została zaktualizowana";
                        }
                    else{
                        throw new Exception($polaczenie->error);
                        }
                }
                catch(Exception $error){
                    $blad = $polaczenie->errno;
                    if ($blad = 1062){
                        echo "Przepraszamy, podany dział istnieje już w bazie danych biblioteki";
                    }
                    else{
                        echo "Przepraszamy, napotkano problem z bazą danych";
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
    <style>
        <?php include 'styl.css'; ?>
    </style>
	<title>Biblioteka</title>
</head>
<body>
<h1>Dodaj nowy dział do biblioteki </h1>
    <form action="" method="post">
		<input type="text" name="dzial" placeholder="Dział" required>
        <br/>
		<input type="submit" name="submit"/>
    </form>
<form action="panel.php">
    <button type="submit" formaction="panel.php">Wróć do panelu zarządzania</button>
</form>
<?php
include 'footer.php'
?>
</body>
</html>