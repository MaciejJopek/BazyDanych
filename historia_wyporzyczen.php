<?php
session_start();
include_once 'unsety.php';
if(!isset($_SESSION['zalogowany'])){
    header('Location:index.php');
    exit();
}
if(isset($_SESSION['zmienna'])){
    $_SESSION['zmienna']=$_SESSION['zmienna']-1;
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
            sprintf("SELECT tytul,autor,data_rez,data_zamow,data_zwrotu,id_wypozyczenie,id_egzemplaz FROM wypozyczenie JOIN egzemplarz ON egzemplarz_id=id_egzemplaz JOIN ksiazka ON ksiazka_id=id_ksiazka WHERE czytelnik_id='%d' ORDER BY data_rez DESC;",
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
        $rezultat = $polaczenie->query(
            sprintf("SELECT data_zamow FROM wypozyczenie WHERE id_wypozyczenie='%d' ",
            mysqli_real_escape_string($polaczenie,$id_wypozyczenie)
            )) ;
        $wiersz = $rezultat -> fetch_assoc();

        $data_zwrotu =$wiersz['data_zamow'];
        $data_zwrotu_plus_miesiac = date('Y-m-d', strtotime($data_zwrotu. ' + 30 days'));
        $datetime_obence = new DateTime($data);
        $datetime_plus_miesiac= new DateTime($data_zwrotu);
        $interval = $datetime_plus_miesiac->diff($datetime_obence);
        $liczba_przekroczonych_dni =$interval->format('%a');
        if ($liczba_przekroczonych_dni>0)
        {
            $rezultat = $polaczenie->query(
                sprintf("SELECT kara FROM czytelnik WHERE id_czytelnik='%d' ",
                mysqli_real_escape_string($polaczenie,$kto)
                )) ;
            $wiersz = $rezultat -> fetch_assoc();
            $obecna_kara =$wiersz['kara'];
            $do_zaplaty_info = 1*$liczba_przekroczonych_dni;
            $do_zaplaty = 1*$liczba_przekroczonych_dni+$obecna_kara;
            $kto = $_SESSION['kto'];
            if($polaczenie->query(
                sprintf("UPDATE czytelnik SET kara='%d' WHERE id_czytelnik='%d'",
                mysqli_real_escape_string($polaczenie,$do_zaplaty),
                mysqli_real_escape_string($polaczenie,$kto)
            )))
            {
                $_SESSION['Naliczono_kare'] = 'Naliczono kare za przekroczonie terminu w wysokości '.$do_zaplaty_info.' zł';
                $_SESSION['zmienna'] = 2;
                echo "<meta http-equiv='refresh' content='0'>";
            }
        }
        
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
<div class = "opakowanie_historia">
    <div class="container">
        <div  id="naglowek_center">
            <h2 class="naglowek" style="display: inline;">Historia wypożyczeń</h2>
        </div>
        <input class="form-control mb-4" id="tableSearch" type="text"
            placeholder="Podaj dowolne dane ksiązki lub daty" style="margin-top:2%;" >
        <?php
            if (isset($_SESSION['Naliczono_kare']) and $_SESSION['zmienna']==1)
            {
                echo '<div class="alert alert-warning">'.$_SESSION['Naliczono_kare'].'</div>';
            }
            ?>
        <table class="table table-bordered table-striped">
            <thead>
            <?php if (mysqli_num_rows($rezultat) > 0) { 
                echo "               
                <th>Tytuł</th>
                <th>Autor</th>
                <th>Data rezerwacji</th>
                <th>Data wypożyczenia</th>
                <th>Data zwrotu</th>
                <th>Akcja</th>";
            }
            else{
                echo '<div class="alert alert-warning" style="margin-top:5%;">Historia jest pusta</div>';
            }
            ?>
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

