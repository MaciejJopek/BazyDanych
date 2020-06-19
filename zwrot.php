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
    $polaczenie->query("SET NAMES 'utf8'");
        if($polaczenie->connect_errno!=0){
            throw new Exception(mysqli_connect_errno());
        }
        else{
            $kto = $_SESSION['kto'];
            $rezultat = $polaczenie->query(
            sprintf("SELECT tytul,autor,data_rez,data_zamow,data_zwrotu,id_wypozyczenie,id_egzemplaz FROM wypozyczenie JOIN egzemplarz ON egzemplarz_id=id_egzemplaz JOIN ksiazka ON ksiazka_id=id_ksiazka WHERE czytelnik_id='%d' and (data_zamow is NULL or data_zwrotu is NULL) ORDER BY data_zamow DESC;",
            mysqli_real_escape_string($polaczenie,$kto)
        ));
            if(!$rezultat){
                throw new Exception($polaczenie->error);
            }
    }
}
catch(Exception $error){
    echo '<span>Błąd serwera, nie można połączyć się z bazą danych';
}
if(isset($_POST['zatwierdz_wyp'])){
    $ktora_rezerwacja = sanityzacja($_POST['zatwierdz_wyp']);
    try{
        $data = date("Y-m-d");
        if ($polaczenie->query(
        sprintf("UPDATE wypozyczenie SET data_zamow='%s' WHERE id_wypozyczenie='%d' ",
        mysqli_real_escape_string($polaczenie,$data),
        mysqli_real_escape_string($polaczenie,$ktora_rezerwacja)
        )))
        {   
            echo "<meta http-equiv='refresh' content='0'>";
        }
    else{
        throw new Exception($polaczenie->error);
        }
    }
    catch(Exception $error){
        echo '<span>Błąd serwera, nie można połączyć się z bazą danych';
    }
    $polaczenie->close();
}
if(isset($_POST['zwrot'])){
    $arr = explode('|', $_POST['zwrot']);

    if( count($arr) == 2 ) {
        $id_egzemplarz = sanityzacja($arr[0]);
        $id_wypozyczenie = sanityzacja($arr[1]);
    }
    try{
        $data = date("Y-m-d");
        if ($polaczenie->query(
        sprintf("UPDATE wypozyczenie SET data_zwrotu='%s' WHERE id_wypozyczenie='%d' ",
        mysqli_real_escape_string($polaczenie,$data),
        mysqli_real_escape_string($polaczenie,$id_wypozyczenie)
        )))
        {   
            $status_eg = 'dostępne';
            if($polaczenie->query(
                sprintf("UPDATE egzemplarz SET status='%s' WHERE id_egzemplaz='%d'",
                mysqli_real_escape_string($polaczenie,$status_eg),
                mysqli_real_escape_string($polaczenie,$id_egzemplarz)
            )))
            {
                echo "<meta http-equiv='refresh' content='0'>";
            }
       }
    else{
        throw new Exception($polaczenie->error);
        }
    }
    catch(Exception $error){
        echo '<span>Błąd serwera, nie można połączyć się z bazą danych';
    }
    $polaczenie->close();
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
<div class = "opakowanie_czytelnicy">
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
                <th>Tytuł</th>
                <th>Autor</th>
                <th>Data rezerwacji</th>
                <th>Data wypożyczenia</th>
                <th>Data zwrotu</th>
                <th>Akcja</th>
            </tr>
            </thead>
            <tbody id="myTable">
            <?php if (mysqli_num_rows($rezultat) > 0) { 
                while($row = mysqli_fetch_assoc($rezultat)) { 
            ?>
            <tr>
                <td><?php echo $row["tytul"]?> </td>
                <td><?php echo $row["autor"]?> </td>
                <td><?php echo $row["data_rez"]?> </td>
                <td><?php echo $row["data_zamow"]?> </td>
                <td><?php echo $row["data_zwrotu"]?> </td>

                <td>
                    <form action="" method="post">         
                    <?php
                        if ($row["data_zamow"]!=NULL)
                        {
                            echo '<button type="submit" style="display: none" name="zatwierdz_wyp" class="btn btn-primary btn-sm" value="'.$row['id_wypozyczenie'].'">Zatwierdz</button>';
                        }
                        else{
                            echo '<button type="submit" name="zatwierdz_wyp" class="btn btn-primary btn-sm" value="'.$row['id_wypozyczenie'].'">Zatwierdz</button>';
                        }
                        ?> 
                     <?php
                        if ($row["data_zwrotu"]!=NULL)
                        {
                            echo '<button type="submit" style="display: none" name="zwrot" class="btn btn-primary btn-sm" value="'.$row['id_egzemplaz'].'">Zwrot</button>';
                        }
                        else{
                            echo '<button type="submit" name="zwrot" class="btn btn-primary btn-sm" value="'.$row['id_egzemplaz'].'|'.$row['id_wypozyczenie'].'">Zwrot</button>';
                        }
                        ?> 
                    </form> 
                </td>
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

