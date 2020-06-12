<nav class="navbar navbar-expand-lg bg-dark navbar-dark justify-content-center">
    <a class="navbar-brand justify-content-center" href="panel.php">Panel główny</a>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
      <li class="nav-item active">
          <a class="nav-link" href="sprawdz_ksiazke.php">Sprawdz książkę<span class="sr-only">(current)</span></a>
        </li>
          <li class="nav-item active">
          <a class="nav-link" href="nowa_ksiazka.php">Nowa ksiązka<span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="nowy_egzemplarz.php">Nowy egzemplarz<span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="zarz_czytelnikami.php">Zarządzanie czytelnikami</a>
        </li>
      </ul>
      <ul class="navbar-nav ml-auto">
        <li class="nav-item active">
          <a class="nav-link" href=""><?php
          echo $_SESSION['imie_bibliotekarza']." ".$_SESSION['nazwisko_bibliotekarza'];
          ?></a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="wyloguj.php">Wyloguj się</a>
        </li>
      </ul>
    </div>
</nav>