<?php
session_start();
include_once 'unsety.php';
if(!isset($_SESSION['zalogowany'])){
    header('Location:index.php');
    exit();
}
include_once 'unsety.php';
include_once 'sanityzacja.php';
require_once "connect.php";
mysqli_report(MYSQLI_REPORT_STRICT);
try{
     $polaczenie = new mysqli($host,$db_user,$db_password,$db_name);
        if($polaczenie->connect_errno!=0){
            throw new Exception(mysqli_connect_errno());
        }
        else{
            $zapytanie= $_SESSION['zapytanie'];
            $rezultat = $polaczenie->query(
            sprintf("SELECT id_ksiazka,tytul,autor,nazwa FROM ksiazka JOIN dzial ON dzial_id=id_dzial WHERE tytul ='%s' or autor ='%s' or nazwa ='%s';",
            mysqli_real_escape_string($polaczenie,$zapytanie),
            mysqli_real_escape_string($polaczenie,$zapytanie),
            mysqli_real_escape_string($polaczenie,$zapytanie)
        ));
            if(!$rezultat){
                throw new Exception($polaczenie->error);
            }
    }
    }
    catch(Exception $error){
        echo '<span>Błąd serwera, nie można połączyć się z bazą danych';
}
if(isset($_POST['ksiazka'])){
    $_SESSION['ksiazka'] = sanityzacja($_POST['ksiazka']);
    header('Location:lista_egzemplarz_bibliotekarz.php');
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
?>
<?php 
include 'nav.php';
?>
<div class = "opakowanie">
    <div class="container">
        <div  id="naglowek_center">
            <h2 class="naglowek" style="display: inline;">Wynik wyszukiwania</h2>
        </div>
        <table class="table table-bordered table-striped" style="margin-top:5%;">
            <thead>
            <tr>
                <th>Tytuł</th>
                <th>Autor</th>
                <th>Dział</th>
                <th>Akcja</th>

            </tr>
            <tbody id="myTable">
            <?php if (mysqli_num_rows($rezultat) > 0) { 
                while($row = mysqli_fetch_assoc($rezultat)) { 
            ?>
            <tr>
                    <td><?php echo $row["tytul"]?> </td>
                    <td><?php echo $row["autor"]?> </td>
                    <td><?php echo $row["nazwa"]?> </td>
                    <td>
                    <form action="" method="post">
                        <button type="submit" name="ksiazka" class="btn btn-primary btn-sm"  value="<?php echo $row['id_ksiazka']; ?>">
                            Szczegóły
                        </button>    
                    </form>
                    </td>
            </tr>
            <?php }}
            else{
                $brak_egz = "Niestety, biblioteka nie posiada obecnie egzemplarza szukanej
                książki";
                echo '<div style="margin-top:2%; text-align:center" class="alert alert-warning">'.$brak_egz.'</div>';
            }
            ?>
            </tbody>
            </thead>
        </table>
    </div>
    <?php
    include 'footer.php'
    ?>
</div>
</body>
</html>