<div class="container-fluid">

    <div class="row">
        <div class="col-xl-12 col-lg-8">
            <div class="card shadow">
                <div class="card-header">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <h5 class="m-0 font-weight-bold text-primary">Wybierz miesiąc</h6>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <div id="datetimepicker12"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-xl-12 col-lg-8">
            <div class="card shadow">
                <div class="card-header">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <h5 class="m-0 font-weight-bold text-primary">Statystyki</h6>
                            <h6 class="m-0 font-weight-bold text-primary">Aktualnie wyświetlane są dane z danego miesiąca, zakres ten będzie można zmienić powyżej</h6>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            
                            <?php
                                include_once "include/php/main.php";
                                //echo get_stats('2019-10-01');
                            ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
<!-- /.container-fluid -->