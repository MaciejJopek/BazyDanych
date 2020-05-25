<?php
session_start();
if(isset($_POST['dzial']))  
	{  
        $wszystko_ok = true;
        $dzial = $_POST['dzial'];

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
                $czy_istnieje = $rezultat->num_rows;
                if($czy_istnieje>0){
                    $wszystko_ok=false;
                    echo "Dział istnieje już w bazie";
                }
                
                if ($wszystko_ok == true)
                {
                    if ($polaczenie->query("INSERT INTO dzial VALUES(NULL,'$dzial')")){
                        echo "Baza danych została zaktualizowana";
                    }
                    else{
                        throw new Exception($polaczenie->error);
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
	<title>Biblioteka</title>
</head>
<body>
<h1>Dodaj nowy dział do biblioteki </h1>
    <form action="" method="post">
		<input type="text" name="dzial" placeholder="Dział" required>
        <br/>
		<input type="submit" name="submit"/>
    </form>
</body>
</html>