<?php
session_start();
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
            $zapytanie= $_SESSION['ksiazka'];
            $rezultat = $polaczenie->query(
            sprintf("SELECT id_egzemplaz,autor,tytul,wydawnictwo,status,rok_wydania,ISBN,strony FROM egzemplarz JOIN ksiazka ON ksiazka_id=id_ksiazka WHERE id_ksiazka ='%d';",
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
if(isset($_POST['wypozycz'])){
    $_SESSION['wypozycz'] = sanityzacja($_POST['wypozycz']);
    header('Location:wypozycz.php');
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
include 'nav_index.php';
?>
<div class = "opakowanie_lista_egzemplarzy">
    <div class="container">
        <div  id="naglowek_center">
            <h2 class="naglowek" style="display: inline;">Lista egzemplarzy</h2>
        </div>
        <table class="table table-bordered table-striped" style="margin-top:5%;">
            <thead>
            <tr>
                <th>Tytuł</th>
                <th>Autor</th>
                <th>Wydawnictwo</th>
                <th>Rok wydania</th>
                <th>ISBN</th>
                <th>Liczba stron</th>
                <th>Status</th>
                <th>Akcja</th>

            </tr>
            <tbody id="myTable">
            <?php if (mysqli_num_rows($rezultat) > 0) { 
                while($row = mysqli_fetch_assoc($rezultat)) { 
            ?>
            <tr>
                    <td><?php echo $row["autor"]?> </td>
                    <td><?php echo $row["tytul"]?> </td>
                    <td><?php echo $row["wydawnictwo"]?> </td>
                    <td><?php echo $row["rok_wydania"]?> </td>
                    <td><?php echo $row["ISBN"]?> </td>
                    <td><?php echo $row["strony"]?> </td>
                    <td><?php echo $row["status"]?> </td>
                    <td>
                    <form action="" method="post">
                        <?php
                        if ($row["status"]=='wypożyczone')
                        {
                            echo '<button type="submit" style="display: none" name="wypozycz" class="btn btn-primary btn-sm" value="'. $row['id_egzemplaz'].'">wypożycz</button>';
                        }
                        else{
                            echo '<button type="submit" name="wypozycz" class="btn btn-primary btn-sm" value="'. $row['id_egzemplaz'].'">wypożycz</button>';
                        }
                        ?> 
                    </form>
                    </td>
            </tr>
            <?php }} ?>
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