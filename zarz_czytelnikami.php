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
            $rezultat = $polaczenie->query("SELECT * FROM czytelnik");
            if(!$rezultat){
                throw new Exception($polaczenie->error);
            }
    }
    }
    catch(Exception $error){
        echo '<span>Błąd serwera, nie można połączyć się z bazą danych';
    }
if(isset($_POST['id_czytelnik'])){
    $id_czytelnik = sanityzacja($_POST['id_czytelnik']);

    try{
        if ($polaczenie->query(
        sprintf("DELETE FROM czytelnik WHERE id_czytelnik='%d'",
        mysqli_real_escape_string($polaczenie,$id_czytelnik)
        )))
        {   
            echo "<meta http-equiv='refresh' content='0'>";
        }
    else{
        throw new Exception($polaczenie->error);
        }
    }
    catch(Exception $error){
        $blad = $polaczenie->errno;
        if ($blad = 1451){
            $_SESSION['BladUsuwania']= 'Przepraszamy, nie można usunąć czytelnika, posiada on wypożyczoną książkę';
        }
            else{
                echo "Przepraszamy, napotkano problem z bazą danych";
            }
    }
    $polaczenie->close();
}
if(isset($_POST['aktualizacja'])){
    $_SESSION['kto'] = sanityzacja($_POST['aktualizacja']);
    header('Location:aktualizacja_czytelnika.php');
}
if(isset($_POST['historia'])){
    $_SESSION['kto'] = sanityzacja($_POST['historia']);
    header('Location:historia_wyporzyczen.php');
}
if(isset($_POST['zwrot'])){
    $_SESSION['kto'] = sanityzacja($_POST['zwrot']);
    header('Location:zwrot.php');
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
            <h2 class="naglowek" style="display: inline;">Zarządzanie czytelnikami</h2>
            <div id="dodaj_czytelnika" style="display: inline;" >
                <a class="btn btn-large btn-info" href="nowy_urzytkownik.php" >Nowy czytelnik</a>
            </div>
        </div>
        <input class="form-control mb-4" id="tableSearch" type="text"
            placeholder="Podaj dowolne dane czytenika" style="margin-top:2%;" >
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
                <th>Miasto</th>
                <th>Adres</th>
                <th>Kara (zł)</th>
                <th>Telefon</th>
                <th>Dodaj/Usuń</th>
            </tr>
            </thead>
            <tbody id="myTable">
            <?php if (mysqli_num_rows($rezultat) > 0) { 
                while($row = mysqli_fetch_assoc($rezultat)) { 
            ?>
            <tr>
                    <td><?php echo $row["imie"]?> </td>
                    <td><?php echo $row["nazwisko"]?> </td>
                    <td><?php echo $row["miasto"]?> </td>
                    <td><?php echo $row["adres"]?> </td>
                    <td><?php echo $row["kara"]?> </td>
                    <td><?php echo $row["telefon"]?> </td>
                    <td>
                    <form action="" method="post">
                        <button type="submit" name="aktualizacja" class="btn btn-primary btn-sm"  value="<?php echo $row['id_czytelnik']; ?>">
                            Edytuj
                        </button>    
                        <button type="submit" name="historia" class="btn btn-info btn-sm"  value="<?php echo $row['id_czytelnik']; ?>">
                            Historia
                        </button>          
                        <button type="submit" name="zwrot" class="btn btn-success btn-sm"  value="<?php echo $row['id_czytelnik']; ?>">
                            Zwróć/Potwierdz
                        </button>      
                       <!-- <button type="submit" name="id_czytelnik" class="btn btn-danger btn-sm"  value="<?php echo $row['id_czytelnik']; ?>">
                            Usuń
                        </button>-->
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