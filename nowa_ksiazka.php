<?php
session_start();
mysqli_report(MYSQLI_REPORT_STRICT);
require_once "connect.php";
try{
    $polaczenie = new mysqli($host,$db_user,$db_password,$db_name);
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
    //echo 'Informacja dla develera'.$error;
}
if(isset($_POST['autor']))  
	{  
        $wszystko_ok = true;
        $dzial = $_POST['dzial'];
        $autor= $_POST['autor'];
        $tytul = $_POST['tytul'];
        try{
            if ($wszystko_ok = true){
                if ($polaczenie->query("INSERT INTO ksiazka VALUES(NULL,'$autor','$tytul',$dzial)")){
                    echo "Baza danych została zaktualizowana";
                }
                else{
                    throw new Exception($polaczenie->error);
                }
            }
            $polaczenie->close();
        }
        catch(Exception $error){
            echo '<span>Błąd serwera, nie można połączyć się z bazą danych';
            //echo 'Informacja dla develera'.$error;
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
<h1>Dodaj nową książkę do biblioteki </h1>
    <form action="" method="post">
		<input type="text" name="autor" placeholder="Autor" required>
        <br/>
        <input type="text" name="tytul" placeholder="Tytuł" required>
        <br/>
		<select name="dzial">
			<?php if (mysqli_num_rows($rezultat_dzial) > 0) { 
			while($row = mysqli_fetch_assoc($rezultat_dzial)) { 
			?>
			<option value="<?php echo $row["id_dzial"] ?>"><?php echo $row["nazwa"] ?></option>
			<?php }} ?>
		</select>
        </br>
		<input type="submit" name="submit"/>
    </form>
</body>
</html>