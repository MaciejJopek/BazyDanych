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
		   $rezultat = $polaczenie->query("SELECT * FROM dzial ");
		   if(!$rezultat){
			   throw new Exception($polaczenie->error);
		   }
   }
   }
catch(Exception $error){
	echo '<span>Błąd serwera, nie można połączyć się z bazą danych';
}
if(isset($_POST['aktualizacja'])){
    $_SESSION['dzial'] = sanityzacja($_POST['aktualizacja']);
    header('Location:aktualizuj_dzial.php');
}
if(isset($_POST['usun'])){
    $id_dzialu = sanityzacja($_POST['usun']);
    $rezultat2 = $polaczenie->query(
        sprintf("SELECT * FROM dzial JOIN ksiazka ON dzial_id=id_dzial WHERE id_dzial='%d';",
        mysqli_real_escape_string($polaczenie,$id_dzialu)));
    
    $walidacja = $rezultat2->num_rows;
    if ($walidacja==0){
        if ($polaczenie->query(
            sprintf("DELETE From dzial WHERE id_dzial='%d'",
             mysqli_real_escape_string($polaczenie,$id_dzialu)
             )))
             {
                $_SESSION['Done_usuniecie_dzial'] = 'Usunieto dzial';
                $_SESSION['zmienna'] = 2;
                echo "<meta http-equiv='refresh' content='0'>";
             }
        }
    else{
        $_SESSION['BladUsuwania_dzalu']= 'Nie można usunąć działu gdy w bibliotece są jeszcze książki przypisane
        do tego działu';
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
<div class="opakowanie_dzial">
    <div class="container" >
	<div  id="naglowek_center">
            <h2 class="naglowek" style="display: inline;">Wyszukiwarka</h2>
            <div id="dodaj_dzial" style="display: inline;" >
                <a class="btn btn-large btn-info" href="nowy_dzial.php" >Nowy dział</a>
            </div>
        </div>
        <?php
            if (isset($_SESSION['Done_dzial']))
            {
                echo '<div class="alert alert-success">'.$_SESSION['Done_dzial'].'</div>';
                unset ($_SESSION['Done_dzial']);
            }
            if (isset($_SESSION['Done_usuniecie_dzial']) and $_SESSION['zmienna']==1)
            {
                echo '<div class="alert alert-success">'.$_SESSION['Done_usuniecie_dzial'].'</div>';
            }
            if (isset($_SESSION['BladUsuwania_dzalu']))
            {
                echo '<div class="alert alert-danger">'.$_SESSION['BladUsuwania_dzalu'].'</div>';
                unset ($_SESSION['BladUsuwania_dzalu']);
            }
            if (isset($_SESSION['Done_nowy_dział']))
            {
                echo '<div class="alert alert-success">'.$_SESSION['Done_nowy_dział'].'</div>';
                unset ($_SESSION['Done_nowy_dział']);
            }
            ?>
        <input class="form-control mb-4" id="tableSearch" type="text"
            placeholder="Podaj nazwę działu" style="margin-top:2%;" >
		<table class="table table-bordered table-striped" style="margin-top:5%;">
            <thead>
            <tr>
                <th>Nazwa działu</th>
                <th>Akcja</th>
            </tr>
			</thead>
            <tbody id="myTable">
            <?php if (mysqli_num_rows($rezultat) > 0) { 
                while($row = mysqli_fetch_assoc($rezultat)) { 
            ?>
            <tr>
                    <td><?php echo $row["nazwa"]?> </td>
                    <td>
                    <form action="" method="post">
						<button type="submit" name="aktualizacja" class="btn btn-primary btn-sm"  value="<?php echo $row['id_dzial']; ?>">
                            Edytuj
                        </button>         
                        <button type="submit" name="usun" class="btn btn-danger btn-sm"  value="<?php echo $row['id_dzial']; ?>">
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