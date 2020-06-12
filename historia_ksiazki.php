<?php
session_start();
include_once 'unsety.php';
if(!isset($_SESSION['zalogowany'])){
    header('Location:index.php');
    exit();
}
include_once 'sanityzacja.php';
require_once "connect.php";
mysqli_report(MYSQLI_REPORT_STRICT);

try{
    $polaczenie = new mysqli($host,$db_user,$db_password,$db_name);
        if($polaczenie->connect_errno!=0){
            throw new Exception(mysqli_connect_errno());
        }
        else{
            $jaka_ksiazka= $_SESSION['Historia_ksiazki'];
            $rezultat = $polaczenie->query(
            sprintf("SELECT imie,nazwisko,data_rez,telefon,data_zamow,data_zwrotu FROM wypozyczenie JOIN egzemplarz ON egzemplarz_id=id_egzemplaz JOIN czytelnik ON czytelnik_id=id_czytelnik WHERE egzemplarz_id='%d';",
            mysqli_real_escape_string($polaczenie,$jaka_ksiazka)
        ));
            if(!$rezultat){
                throw new Exception($polaczenie->error);
            }
    }

}
catch(Exception $error){
    echo '<span>Błąd serwera, nie można połączyć się z bazą danych';
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
            <h2 class="naglowek" style="display: inline;">Historia wypożyczeń</h2>
        </div>
        <input class="form-control mb-4" id="tableSearch" type="text"
            placeholder="Podaj dowolne dane ksiązki lub daty" style="margin-top:2%;" >
        <?php
            if (isset($_SESSION['BladUsuwania']))
            {
                echo '<div class="alert alert-danger">'.$_SESSION['BladUsuwania'].'</div>';
                unset ($_SESSION['BladUsuwania']);
            }
            ?>
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>Imie</th>
                <th>Nazwisko</th>
                <th>Telefon</th>
                <th>Data rezerwacji</th>
                <th>Data wypożyczenia</th>
                <th>Data zwrotu</th>
            </tr>
            </thead>
            <tbody id="myTable">
            <?php if (mysqli_num_rows($rezultat) > 0) { 
                while($row = mysqli_fetch_assoc($rezultat)) { 
            ?>
            <tr>
                <td><?php echo $row["imie"]?> </td>
                <td><?php echo $row["nazwisko"]?> </td>
                <td><?php echo $row["telefon"]?> </td>
                <td><?php echo $row["data_rez"]?> </td>
                <td><?php echo $row["data_zamow"]?> </td>
                <td><?php echo $row["data_zwrotu"]?> </td>
            </tr>
            <?php }} ?>
            </tbody>
        </table>
    </div>
    <?php
    include 'footer.php'
    ?>
</div>
<script>
$(document).ready(function(){
  $("#tableSearch").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#myTable tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});
</script>
</body>
</html>

