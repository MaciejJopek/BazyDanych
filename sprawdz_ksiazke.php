<?php
session_start();
if(!isset($_SESSION['zalogowany'])){
    header('Location:index.php');
    exit();
}
if(isset($_SESSION['zmienna'])){
    $_SESSION['zmienna']=$_SESSION['zmienna']-1;
}
include_once 'unsety.php';
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
		   $rezultat = $polaczenie->query("SELECT id_ksiazka,autor,tytul,nazwa FROM ksiazka join dzial on id_dzial=dzial_id");
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
if(isset($_POST['nowy_egzemplarz'])){
    $_SESSION['id_ksiazki'] = sanityzacja($_POST['nowy_egzemplarz']);
    header('Location:nowy_egzemplarz.php');
}
if(isset($_POST['aktualizacja'])){
    $_SESSION['id_ksiazki'] = sanityzacja($_POST['aktualizacja']);
    header('Location:aktualizuj_ksiazke.php');
}
if(isset($_POST['usun'])){
    $id_do_usuniecia = sanityzacja($_POST['usun']);
    $rezultat2 = $polaczenie->query(
        sprintf("SELECT * FROM ksiazka JOIN egzemplarz ON ksiazka_id=id_ksiazka WHERE id_ksiazka='%d';",
        mysqli_real_escape_string($polaczenie,$id_do_usuniecia)));
    $walidacja = $rezultat2->num_rows;
    if ($walidacja==0){
        if ($polaczenie->query(
            sprintf("DELETE From ksiazka WHERE id_ksiazka='%d'",
             mysqli_real_escape_string($polaczenie,$id_do_usuniecia)
             )))
             {
                $_SESSION['Done_usuniecie_ksiazki'] = 'Usunieto ksiazke';
                $_SESSION['zmienna'] = 2;
                echo "<meta http-equiv='refresh' content='0'>";
             }
        }
    else{
        $_SESSION['BladUsuwania']= 'Nie można usunąć ksiązki, w pierwszej kolejności usuń wszystkie 
        egzemplarze';
        }
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
<div class="opakowanie_wyszukiwarka">
    <div class="container" >
	<div  id="naglowek_center">
            <h2 class="naglowek" style="display: inline;">Wyszukiwarka</h2>
        </div>
        <input class="form-control mb-4" id="tableSearch" type="text"
            placeholder="Podaj dane książki" style="margin-top:2%;" >
            <?php
        if (isset($_SESSION['Done_nowy_egzemplarz'])){
            echo '<div class="alert alert-success" style="margin-top=2%">'.$_SESSION['Done_nowy_egzemplarz'].'</div>';
            unset($_SESSION['Done_nowy_egzemplarz']);
        }
        if (isset($_SESSION['Done_usuniecie_ksiazki']) and $_SESSION['zmienna']==1){
            echo '<div class="alert alert-success" style="margin-top=2%">'.$_SESSION['Done_usuniecie_ksiazki'].'</div>';
        }
        if (isset($_SESSION['BladUsuwania'])){
            echo '<div class="alert alert-danger" style="margin-top=2%">'.$_SESSION['BladUsuwania'].'</div>';
            unset($_SESSION['BladUsuwania']);
        }
        if (isset($_SESSION['Sukcesy'])){
            echo '<div class="alert alert-success" style="margin-top=2%">'.$_SESSION['Sukcesy'].'</div>';
            unset($_SESSION['Sukcesy']);
        }
        ?>
		<table class="table table-bordered table-striped" style="margin-top:5%;">
            <thead>
            <?php if (mysqli_num_rows($rezultat) > 0) { 
                echo
                "<tr>
                <th>Tytuł</th>
                <th>Autor</th>
                <th>Dział</th>
                <th>Akcja</th>
                </tr>";
            }
            else{
                echo '<div class="alert alert-warning" style="margin-top:5%;">Przepraszamy, biblioteka nie posiada książek pasujących do zapytania</div>';
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
                    <td><?php echo $row["nazwa"]?> </td>
                    <td>
                    <form action="" method="post">
                        <button type="submit" name="ksiazka" class="btn btn-primary btn-sm"  value="<?php echo $row['id_ksiazka']; ?>">
                            Szczegóły
                        </button>
						<button type="submit" name="aktualizacja" class="btn btn-info btn-sm"  value="<?php echo $row['id_ksiazka']; ?>">
                            Edytuj
                        </button>      
                        <button type="submit" name="nowy_egzemplarz" class="btn btn-success btn-sm"  value="<?php echo $row['id_ksiazka']; ?>">
                            Dodaj egzemplarz
                        </button>   
                        <button type="submit" name="usun" class="btn btn-danger btn-sm"  value="<?php echo $row['id_ksiazka']; ?>">
                            Usun
                        </button>   
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