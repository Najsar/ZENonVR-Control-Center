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
          $top_menu = str_replace("%%PAGE%%", "Zaawansowane Statystyki ( SVR )", $top_menu);
          echo $top_menu;
        ?>
      
        <?php
          include "pages/adv_stats.php";
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

  <!-- Core plugin JavaScript-->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="js/sb-admin-2.min.js"></script>

  <!-- Page level custom scripts -->
  <script>
  $(document).ready(function() {
    $.ajax({
      url : "http://api.synthesisvr.com/data/sessions/9e6e8030-1b13-50e4-9f68-f84759a4769d/json/1%20month/date=2019-09-01",
      dataType : "json"
    })
    .done(function(res){
        //var array = $.makeArray( res );
        var array = Object.values(res['sessions']);
        console.log(array);
        $('#dataTable').DataTable({
          data: array,
          columns: [
            { data: "experience" },
            { data: "cost" },
            { data: 'duration' },
            { data: 'games' },
            { data: 'stations' },
            { data: 'start_time' },
            { data: 'end_time' }
          ]
        });
    
    });
  });
  </script>

</body>

</html>
