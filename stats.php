<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>ZENonVR Control Center</title>

  <!-- Custom fonts for this template-->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="css/sb-admin-2.min.css" rel="stylesheet">

  <!-- Custom styles for this page -->
  <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">
        
        <!-- MENU -->
        <?php
          session_start();
          include "include/php/main.php";
          if(!login_by_session()['status']) {
            header("location: login.php");
          }
          else if(login_by_session()['data']['isAdmin'] == 0) {
            header("location: no_permissions.php");
          }
          else {
            $user = login_by_session()['data'];
          }
          include "include/menu.php";
        ?>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

        <!-- TOP_MENU -->
        <?php
          $top_menu = file_get_contents("include/top_menu.html");
          $top_menu = str_replace("%%USER%%", $user['Name'], $top_menu);
          $top_menu = str_replace("%%PAGE%%", "Statystyki", $top_menu);
          echo $top_menu;
        ?>
      
        <?php
          include "pages/stats.php";
          //include "pages/tokens_modals.php";
        ?>

      </div>
      <!-- End of Main Content -->

      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; ZENonVR Control Center 2019</span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>


  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>


  <?php
    include "pages/dashboard_modals.php";
  ?>

  <!-- Bootstrap core JavaScript-->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>
  

  <!-- Core plugin JavaScript-->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="js/sb-admin-2.min.js"></script>

  <!-- Page level custom scripts -->
  <script>
    $(document).ready(function() {
      $.urlParam = function(name){
        var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
        if(results == null) {
          return 0;
        }
        else {
          return results[1] || 0;
        }
      }
      if($.urlParam('date') == 0) {
        var d = new Date();
        var currMonth = d.getMonth();
        var currYear = d.getFullYear();
        var startDate = new Date(currYear, currMonth, 1);
      }
      else {
        var startDate = new Date($.urlParam('date'));
      }
      

      $('#dataTable').DataTable();
      $.fn.datepicker.dates['pl'] = {
          days: ["Niedziela", "Poniedziałek", "Wtorek", "Środa", "Czwartek", "Piątek", "Sobota", "Niedziela"],
          daysShort: ["Niedz", "Pon", "Wt", "Śr", "Czw", "Pt", "Sob", "Niedz"],
          daysMin: ["Nd", "Pn", "Wt", "Śr", "Czw", "Pt", "Sb", "Nd"],
          months: ["Styczeń", "Luty", "Marzec", "Kwiecień", "Maj", "Czerwiec", "Lipiec", "Sierpień", "Wrzesień", "Październik", "Listopad", "Grudzień"],
          monthsShort: ["STY", "LUT", "MAR", "KWI", "MAJ", "CZE", "LIP", "SIE", "WRZ", "PAŹ", "LIS", "GRU"]
      };
      $('#date').datepicker({
        language: 'pl',
        format: "yyyy-mm-01",
        minViewMode: "months"
      })
      .on('changeDate', function(ev){
        $(location).attr('href','?date='+ev.format());
        //alert(ev.format());
      });
      $('#date').datepicker('update', startDate);
    });
  </script>

</body>

</html>
