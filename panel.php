<?php
session_start();
include_once 'unsety.php';
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

    <div class="uklad1">
        <div class="wybor">
            <form action="sprawdz_ksiazke.php">
                <img src="book_search.png"  style="width:50%"> 
                <input type="submit" value="Sprawdź ksiązkę" />
            </form>
        </div>
        <div class="wybor">
            <form action="nowa_ksiazka.php">
                <img src="books.png"  style="width:50%">   
                <input type="submit" value="Dodaj nową książkę" />
            </form>
        </div>
        <div class="wybor">
            <form action="nowy_egzemplarz.php">
                <img src="open-book.png"  style="width:50%">
                <input type="submit" value="Dodaj nowy egzemplarz książki" />
            </form>
        </div>
        <div class="wybor">
            <form action="zarz_czytelnikami.php">
                <img src="student.png"  style="width:50%"> 
                <input type="submit" value="Zarządzenie czytelnikiem" />
            </form>
        </div>
    </div>

<?php
include 'footer.php'
?>
</body>
</html>