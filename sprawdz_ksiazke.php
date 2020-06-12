<?php
session_start();
include_once 'unsety.php';
if(!isset($_SESSION['zalogowany'])){
    header('Location:index.php');
    exit();
}
include_once 'sanityzacja.php';
if(isset($_POST['wyszukanie2'])){
    $_SESSION['zapytanie'] = sanityzacja($_POST['wyszukanie2']);
    header('Location:wynik_bibliotekarz.php');
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
		<div>
			<h5 class="napis_biblioteka">Wyszukiwarka </h5>
		</div>
		<form action="" method="post">
			<div class="p-1 bg-light rounded rounded-pill shadow-sm mb-4" style="margin-top:5%">
				<div class="input-group">
				<input type="search" name="wyszukanie2" placeholder="Czego szukasz?" aria-describedby="button-addon1" class="form-control border-0 bg-light">
				<div class="input-group-append">
					<button id="button-addon1" type="submit" class="btn btn-link text-primary"><i class="fa fa-search"></i></button>
				</div>
				</div>
			</div>
		</form>
	</div>
<?php
include 'footer.php'
?>
</div>
</body>
</html>