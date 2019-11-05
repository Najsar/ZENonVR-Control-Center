

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

      <!-- Sidebar - Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <img src="img/logo2.png" width="200px" height="50px">
      </a>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

      <!-- Nav Item - Dashboard -->
      <li class="nav-item">
        <a class="nav-link" href="index.php">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Panel kontrolny</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="reservations.php">
          <i class="fas fa-fw fa-calendar"></i>
          <span>Rezerwacje</span></a>
      </li>
      <li class="nav-item">
          <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#bon" aria-expanded="true">
            <i class="fas fa-ticket-alt"></i>
            <span>Karnety i bony</span>
          </a>
          <div id="bon" class="collapse">
            <div class="bg-white py-2 collapse-inner rounded">
              <a class="collapse-item" href="pass.php">Karnety</a>
              <a class="collapse-item" href="tokens.php">Bony</a>
            </div>
          </div>
        </li>

      <!--
      
      <hr class="sidebar-divider">

      
      <div class="sidebar-heading">
        Inne funkcje
      </div>
      -->

      <!-- Divider -->
      <hr class="sidebar-divider">
      <?php
        if(login_by_session()['data']['isAdmin'] != '0') { 
      ?>
      <!-- Heading -->
      <div class="sidebar-heading">
        Funkcje Admina
      </div>

      <li class="nav-item">
        <a class="nav-link" href="reports.php">
          <i class="fas fa-fw fa-clipboard"></i>
          <span>Raporty</span></a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="stats.php">
          <i class="fas fa-fw fa-chart-line"></i>
          <span>Statystyki</span></a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="day_report.php">
          <i class="fas fa-fw fa-chart-line"></i>
          <span>Statystyki dzienne</span></a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#edit" aria-expanded="true">
          <i class="fas fa-fw fa-edit"></i>
          <span>Edycja</span>
        </a>
        <div id="edit" class="collapse">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Edycja:</h6>
            <a class="collapse-item" href="edit_products.php">Produkty</a>
            <!--<a class="collapse-item" href="edit_reports.php">Korekta rozliczeń</a>-->
            <!--<a class="collapse-item" href="adv_stats.php">Zaawansowane statystyki</a>-->
          </div>
        </div>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider">

      <?php
        }
      ?>

      <div class="sidebar-heading">
        Zarządzaj
      </div>

      <li class="nav-item">
        <a class="nav-link" href="logout.php">
          <i class="fas fa-fw fa-power-off"></i>
          <span>Wyloguj się</span></a>
      </li>


      

    </ul>
    <!-- End of Sidebar -->
