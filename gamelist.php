<?php
  include('layout/navbar.php')
?>

<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <title>Pixel Station - Gamelist Page</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">


    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-lugx-gaming.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet"href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
<!--

TemplateMo 589 lugx gaming

https://templatemo.com/tm-589-lugx-gaming

-->
  </head>

<body>


  <div class="page-heading header-text mb-5">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <h3>Game List</h3>
          <span class="breadcrumb"><a href="index.php">Home</a> > Game list</span>
        </div>
      </div>
    </div>
  </div>

  
  <div class="col-lg-12 text-center mt-5">
    <div class="section-heading">
      <h2>ALL GAME</h2>
     <h6>FIND YOUR FAVORIT GAME...</h6>
    </div>
  </div>

  <div class="section trending">
    <div class="container">
      <div class="search-input">
                  <form id="subscribe" action="#">
                    <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Cari nama game...">
                    <button type="submit">Cari</button>
                  </form>
                </div>
      <ul class="trending-filter">
      <li>
        <a class="is_active" href="#!" data-filter="*">Show All</a>
      </li>
      <li>
        <a href="#!" data-filter=".adv">Singleplayer</a>
      </li>
      <li>
        <a href="#!" data-filter=".str">Multiplayer</a>
      </li>
      </ul>
      <div class="row trending-box">
        <!-- Game 1 -->
        <div class="col-lg-3 col-md-6 align-self-center mb-30 trending-items adv">
          <div class="item">
        <div class="thumb">
          <a href="#"><img src="assets/images/trending-01.jpg" alt=""></a>
        </div>
        <div class="down-content">
          <span class="category">Adventure</span>
          <h4>Assassin Creed</h4>
          <!-- Info icon (about) -->
          <a href="#" data-toggle="modal" data-target="#gameModal0"><i class="fa fa-info-circle"></i></a>
        </div>
          </div>
        </div>
        <!-- Game 2 -->
        <div class="col-lg-3 col-md-6 align-self-center mb-30 trending-items str">
          <div class="item">
        <div class="thumb">
          <a href="#"><img src="assets/images/trending-02.jpg" alt=""></a>
        </div>
        <div class="down-content">
          <span class="category">Strategy</span>
          <h4>Civilization VI</h4>
          <a href="#" data-toggle="modal" data-target="#gameModal0"><i class="fa fa-info-circle"></i></a>
        </div>
          </div>
        </div>
        <!-- Game 3 -->
        <div class="col-lg-3 col-md-6 align-self-center mb-30 trending-items rac adv">
          <div class="item">
        <div class="thumb">
          <a href="#"><img src="assets/images/trending-03.jpg" alt=""></a>
        </div>
        <div class="down-content">
          <span class="category">Racing</span>
          <h4>Forza Horizon 4</h4>
          <a href="#" data-toggle="modal" data-target="#gameModal0"><i class="fa fa-info-circle"></i></a>
        </div>
          </div>
        </div>
        <!-- Game 4 -->
        <div class="col-lg-3 col-md-6 align-self-center mb-30 trending-items str">
          <div class="item">
        <div class="thumb">
          <a href="#"><img src="assets/images/trending-04.jpg" alt=""></a>
        </div>
        <div class="down-content">
          <span class="category">Strategy</span>
          <h4>Starcraft II</h4>
          <a href="#" data-toggle="modal" data-target="#gameModal0"><i class="fa fa-info-circle"></i></a>
        </div>
          </div>
        </div>
      </div>

      <!-- Modal Game 1 -->
      <div class="modal fade" id="gameModal0" tabindex="-1" aria-labelledby="modalgame1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content border-0 shadow-sm" style="border-radius: 18px;">
        <div class="modal-body p-4">
          <div class="row align-items-center">
            <div class="col-md-5 text-center mb-3 mb-md-0">
          <img src="assets/images/trending-01.jpg" alt="Assassin Creed" class="img-fluid" style="max-height:300px; border-radius:12px;">
            </div>
            <div class="col-md-7">
          <h5 class="fw-bold mb-2" id="modalRegularLabel">Civilization VI</h5>
          <ul class="list-unstyled text-start mb-3" style="font-size: 0.97rem;">
            <li><strong>Nama:</strong> Civilization VI</li>
            <li><strong>Tahun Rilis:</strong> 2016</li>
            <li><strong>Kategori:</strong> Strategy</li>
            <li><strong>Mode:</strong> Multiplayer</li>
          </ul>
            </div>
          </div>
        </div>
          </div>
        </div>
      </div>
          
      <!-- Modal Game 2 -->
      <div class="modal fade" id="gameModal0" tabindex="-1" aria-labelledby="modalgame1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content border-0 shadow-sm" style="border-radius: 18px;">
        <div class="modal-body p-4">
          <div class="row align-items-center">
            <div class="col-md-5 text-center mb-3 mb-md-0">
          <img src="assets/images/trending-01.jpg" alt="Assassin Creed" class="img-fluid" style="max-height:300px; border-radius:12px;">
            </div>
            <div class="col-md-7">
          <h5 class="fw-bold mb-2" id="modalRegularLabel">Civilization VI</h5>
          <ul class="list-unstyled text-start mb-3" style="font-size: 0.97rem;">
            <li><strong>Nama:</strong> Civilization VI</li>
            <li><strong>Tahun Rilis:</strong> 2016</li>
            <li><strong>Kategori:</strong> Strategy</li>
            <li><strong>Mode:</strong> Multiplayer</li>
          </ul>
            </div>
          </div>
        </div>
          </div>
        </div>
      </div>

      <!-- Modal Game 3 -->
      <div class="modal fade" id="gameModal0" tabindex="-1" aria-labelledby="modalgame1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content border-0 shadow-sm" style="border-radius: 18px;">
        <div class="modal-body p-4">
          <div class="row align-items-center">
            <div class="col-md-5 text-center mb-3 mb-md-0">
          <img src="assets/images/trending-01.jpg" alt="Assassin Creed" class="img-fluid" style="max-height:300px; border-radius:12px;">
            </div>
            <div class="col-md-7">
          <h5 class="fw-bold mb-2" id="modalRegularLabel">Civilization VI</h5>
          <ul class="list-unstyled text-start mb-3" style="font-size: 0.97rem;">
            <li><strong>Nama:</strong> Civilization VI</li>
            <li><strong>Tahun Rilis:</strong> 2016</li>
            <li><strong>Kategori:</strong> Strategy</li>
            <li><strong>Mode:</strong> Multiplayer</li>
          </ul>
            </div>
          </div>
        </div>
          </div>
        </div>
      </div>
      <!-- Modal Game 4 -->
      <div class="modal fade" id="gameModal0" tabindex="-1" aria-labelledby="modalgame1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content border-0 shadow-sm" style="border-radius: 18px;">
        <div class="modal-body p-4">
          <div class="row align-items-center">
            <div class="col-md-5 text-center mb-3 mb-md-0">
          <img src="assets/images/trending-01.jpg" alt="Assassin Creed" class="img-fluid" style="max-height:300px; border-radius:12px;">
            </div>
            <div class="col-md-7">
          <h5 class="fw-bold mb-2" id="modalRegularLabel">Civilization VI</h5>
          <ul class="list-unstyled text-start mb-3" style="font-size: 0.97rem;">
            <li><strong>Nama:</strong> Civilization VI</li>
            <li><strong>Tahun Rilis:</strong> 2016</li>
            <li><strong>Kategori:</strong> Strategy</li>
            <li><strong>Mode:</strong> Multiplayer</li>
          </ul>
            </div>
          </div>
        </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-12">
          <ul class="pagination">
        <li><a href="#"> &lt; </a></li>
        <li><a href="#">1</a></li>
        <li><a class="is_active" href="#">2</a></li>
        <li><a href="#">3</a></li>
        <li><a href="#"> &gt; </a></li>
          </ul>
        </div>
      </div>
  </div>

  <footer>
    <div class="container">
      <div class="col-lg-12">
        <p>Copyright © 2025 Pixel Station. All rights reserved.</p>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <!-- Bootstrap core JavaScript -->
  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
  <script src="assets/js/isotope.min.js"></script>
  <script src="assets/js/owl-carousel.js"></script>
  <script src="assets/js/counter.js"></script>
  <script src="assets/js/custom.js"></script>
  <script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  

  <script>
    // Ensure modals work if loaded dynamically or if there are JS conflicts
    $(document).ready(function(){
      // No additional JS needed for Bootstrap 4 modals if data attributes are correct
    });
  </script>
  </body>
</html>