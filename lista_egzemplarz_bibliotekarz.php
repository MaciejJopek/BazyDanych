<?php
session_start();
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
if(isset($_POST['Historia_ksiazki'])){
    $_SESSION['Historia_ksiazki'] = sanityzacja($_POST['Historia_ksiazki']);
    header('Location:historia_ksiazki.php');
}
if(isset($_POST['usun_egzemplarz'])){
    $id_do_usuniecia=sanityzacja($_POST['usun_egzemplarz']);
    $rezultat2 = $polaczenie->query(
        sprintf("SELECT * FROM egzemplarz JOIN wypozyczenie ON id_egzemplaz=egzemplarz_id WHERE id_egzemplaz='%d';",
        mysqli_real_escape_string($polaczenie,$id_do_usuniecia)));
    
    $walidacja = $rezultat2->num_rows;
    if ($walidacja==0){
        if ($polaczenie->query(
            sprintf("DELETE From egzemplarz WHERE id_egzemplaz='%d'",
             mysqli_real_escape_string($polaczenie,$id_do_usuniecia)
             )))
             {
                $_SESSION['Done_usuniecie_egzemplarza'] = 'Usunieto egzemplarz';
                echo "<meta http-equiv='refresh' content='0'>";
             }
    }
    else{
        $wiersz2 = $rezultat2 -> fetch_assoc();
        $status = $wiersz2['status'];
        $id_wyporzyczenia = $wiersz2['id_wypozyczenie'];
        if ($status!='wypożyczone'){
            if ($polaczenie->query(
            sprintf("DELETE From wypozyczenie WHERE egzemplarz_id='%d'",
                mysqli_real_escape_string($polaczenie,$id_do_usuniecia)
                )))
                {
                    if ($polaczenie->query(
                        sprintf("DELETE From egzemplarz WHERE id_egzemplaz='%d'",
                        mysqli_real_escape_string($polaczenie,$id_do_usuniecia)
                        )))
                        {
                            $_SESSION['Done_usuniecie_egzemplarza'] = 'Usunieto egzemplarz';
                            $_SESSION['zmienna'] = 2;
                            echo "<meta http-equiv='refresh' content='0'>";
                        }
                }
        }
        else{
            $_SESSION['BladUsuwania']= 'Nie można usunąć wypożyczonej książki';
        }
    }
}
if(isset($_POST['edytuj_ksiazke'])){
    $_SESSION['id_egzemplarza_do_aktualizacji'] = sanityzacja($_POST['edytuj_ksiazke']);
    header('Location:aktualizuj_egzemplarz.php');
}
if(isset($_POST['wypozycz_biblio'])){
    $_SESSION['wypozycz'] = sanityzacja($_POST['wypozycz_biblio']);
    header('Location:wypozycz_bibliotekarz.php');
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
<div class = "opakowanie_lista_egzemplarzy">
    <div class="container">
        <div  id="naglowek_center">
            <h2 class="naglowek" style="display: inline;">Lista egzemplarzy</h2>
        </div>
        <?php
            if (isset($_SESSION['Done_aktualizacja']))
                {
                echo '<div class="alert alert-success">'.$_SESSION['Done_aktualizacja'].'</div>'; 
                unset ($_SESSION['Done_aktualizacja']);       
                }
            if (isset($_SESSION['BladUsuwania']))
            {
                echo '<div class="alert alert-danger">'.$_SESSION['BladUsuwania'].'</div>';
                unset ($_SESSION['BladUsuwania']);
            }

            if (isset($_SESSION['Done_usuniecie_egzemplarza']) and $_SESSION['zmienna']==1)
            {
                echo '<div class="alert alert-success">'.$_SESSION['Done_usuniecie_egzemplarza'].'</div>';
            }
            if (isset($_SESSION['zrobiono_wypoz']))
            {
                echo '<div class="alert alert-success">'.$_SESSION['zrobiono_wypoz'].'</div>';
                unset ($_SESSION['zrobiono_wypoz']);
            }
            if (isset($_SESSION['Done_nowy_egzemplarz']))
            {
                echo '<div class="alert alert-success">'.$_SESSION['Done_nowy_egzemplarz'].'</div>';
                unset ($_SESSION['Done_nowy_egzemplarz']);
            }
        ?>
        <table class="table table-bordered table-striped" style="margin-top:5%;">
            <thead>
            <?php if (mysqli_num_rows($rezultat) > 0) { 
                echo "<tr>
                        <th>Tytuł</th>
                        <th>Autor</th>
                        <th>Wydawnictwo</th>
                        <th>Rok wydania</th>
                        <th>ISBN</th>
                        <th>Liczba stron</th>
                        <th>Status</th>
                        <th>Akcja</th>

                    </tr>";
            }
            else{
                echo '<div class="alert alert-warning" style="margin-top:5%;">Przepraszamy, biblioteka nie posiada egzemplarzy wybranej
                ksiażki</div>';
            }
            ?>
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
                        <button type="submit" name="Historia_ksiazki" class="btn btn-primary btn-sm"  value="<?php echo $row['id_egzemplaz']; ?>">
                            Historia
                        </button>
                        <button type="submit" name="edytuj_ksiazke" class="btn btn-primary btn-sm"  value="<?php echo $row['id_egzemplaz']; ?>">
                            Edytuj
                        </button>      
                        <button type="submit" name="usun_egzemplarz" class="btn btn-primary btn-sm"  value="<?php echo $row['id_egzemplaz']; ?>">
                            Usuń
                        </button>  
                        <form action="" method="post">
                        <?php
                        if ($row["status"]=='wypożyczone')
                        {
                            echo '<button type="submit" style="display: none" name="wypozycz_biblio" class="btn btn-primary btn-sm" value="'. $row['id_egzemplaz'].'">wypożycz</button>';
                        }
                        else{
                            echo '<button type="submit" name="wypozycz_biblio" class="btn btn-primary btn-sm" value="'. $row['id_egzemplaz'].'">wypożycz</button>';
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