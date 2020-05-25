<?php
session_start();
if(!isset($_SESSION['zalogowany'])){
    header('Location:index.php');
    exit();
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
<?php
echo "<p>Zalogowałeś się jako: ".$_SESSION['imie']." ".$_SESSION['nazwisko'].'<a href="wyloguj.php">Wyloguj się</a></p>';
?>
<form action="nowy_dzial.php">
    <input type="submit" value="Dodaj nową kategorię" />
</form>
<form action="nowa_ksiazka.php">
    <input type="submit" value="Dodaj nową książkę" />
</form><form action="nowy_urzytkownik.php">
    <input type="submit" value="Dodaj nowego urzytkownika" />
</form>
</body>
</html>