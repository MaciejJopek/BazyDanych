<?php
session_start();
if ((isset($_SESSION['zalogowany'])) && ($_SESSION['zalogowany']==true)){
    header('Location:panel.php');
    exit();
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
include 'nav_index.php';
?>
<div class="opakowanie_login">
    <div class="container_login" >
    <div  id="naglowek_center">
            <h2 class="naglowek" style="display: inline;">Logowanie</h2>
        </div>
    <?php
    if (isset($_SESSION['Blad']))
    {
        echo '<div class="alert alert-danger" style="text-align:center;margin-top:2%">'.$_SESSION['Blad'].'</div>';
        unset ($_SESSION['Blad']);
        }
    ?>
	<form action="zaloguj.php" method="post">
        <div class="form-group">
            <label for="imie">Login:</label>
            <input type="text" class="form-control" id="imie" placeholder="Podaj login" name="login" required>
        </div>
        <div class="form-group">
            <label for="nazwisko">Hasło:</label>
            <input type="password" class="form-control" id="nazwisko" placeholder="Podaj haslo" name="haslo" required>
        </div>
		<button type="submit" class="btn btn-success">Zaloguj</button>
    </form>

</div>
<?php
include 'footer.php'
?>
</div>
</body>
</html>