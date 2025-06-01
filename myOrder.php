<?php
// Dummy session data
$_SESSION['username'] = 'John Doe';
$_SESSION['email'] = 'john.doe@example.com';
$_SESSION['foto'] = 'default.jpg';
$_SESSION['status'] = 'Customer'; // Bisa diganti 'Worker' untuk testing

// Dummy counts
$counts = [
    'Order' => 2,
    'Review' => 1,
    'Rating' => 5,
    'User' => $_SESSION['status']
];

// Dummy order data
$orders = [
    [
        'Nama_Game' => 'Pixel Art Portrait',
        'Worker' => 'Jane Smith',
        'Total_Price' => 'Rp150.000',
        'Message' => 'Please make it colorful!',
        'Image' => 'game1.png'
    ],
    [
        'Nama_Game' => 'Custom Avatar',
        'Worker' => 'Alex Turner',
        'Total_Price' => 'Rp75.000',
        'Message' => 'Avatar with glasses.',
        'Image' => 'game2.png'
    ]
];

// Dummy review data (for Worker)
$reviews = [
    [
        'Customer' => 'Michael',
        'Nama_Game' => 'Pixel Art Portrait',
        'Initial_Rank' => 'Bronze',
        'Final_Rank' => 'Silver',
        'Rating' => 5,
        'Review' => 'Great job!',
        'Foto_Customer' => 'default.jpg'
    ]
];
$rating = $counts['Review'] > 0 ? $counts['Rating'] / $counts['Review'] : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Profil Pengguna</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/5f166431bc.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/myOrder.css">
</head>
<body>
<div class="container bg-dark p-0 border rounded mt-4">
    <div class="card border overflow-hidden shadow">
        <div class="card-body bg-dark p-0">
            <img src="image/crsl03.jpg" alt="" class="img-fluid">
            <div class="row align-items-center">
                <div class="col-lg-4 order-lg-1 order-2">
                    <div class="mt-n5">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <div class="linear-gradient d-flex align-items-center justify-content-center rounded-circle" style="width: 110px; height: 110px;">
                                <div class="d-flex align-items-center justify-content-center rounded-circle overflow-hidden" style="width: 100px; height: 100px;">
                                    <img src="image/<?php echo $_SESSION['foto'] ?>" alt="" class="w-100 h-100">
                                </div>
                            </div>
                        </div>
                        <div class="text-center text-light">
                            <h5 class="fs-5 mb-2 mt-3 fw-bold"><?php echo $_SESSION['username'] ?></h5>
                            <p class="mb-0 fs-4"><?php echo $_SESSION['email'] ?></p>
                        </div>
                    </div>
                </div>
                <?php if ($counts['User'] == 'Customer') { ?>
                    <div class="col-lg-4 mt-n3 order-lg-2 order-1">
                        <div class="d-flex align-items-center justify-content-around m-4">
                            <div class="text-center text-white">
                                <h4 class="mb-1 fw-semibold lh-1"><?php echo $counts['Order'] ?></h4>
                                <div class="d-flex flex-row justify-content-center gap-1">
                                    <span style="color: whitesmoke;"><i class="fa-solid fa-basket-shopping"></i></span>
                                    <p class="mb-0 fs-4">Orders</p>
                                </div>
                            </div>
                            <a href="#">
                                <button class="btn btn-primary">Join to be Worker</button>
                            </a>
                        </div>
                    </div>
                <?php } else if ($counts['User'] == 'Worker') { ?>
                    <div class="col-lg-4 mt-n3 order-lg-2 order-1 ">
                        <div class="d-flex align-items-center justify-content-around m-4">
                            <div class="text-center text-white">
                                <h4 class="mb-1 fw-semibold lh-1"><?php echo $rating ?></h4>
                                <div class="d-flex flex-row justify-content-center gap-1">
                                    <span style="color: yellow;"><i class="fa-solid fa-star"></i></span>
                                    <p class="mb-0 fs-4">Rating</p>
                                </div>
                            </div>
                            <div class="text-center text-white">
                                <h4 class="mb-1 fw-semibold lh-1"><?php echo $counts['Review'] ?></h4>
                                <div class="d-flex flex-row justify-content-center gap-1">
                                    <span style="color: whitesmoke;"><i class="fa-solid fa-eye"></i></span>
                                    <p class="mb-0 fs-4">Reviews</p>
                                </div>
                            </div>
                            <div class="text-center text-white">
                                <h4 class="mb-1 fw-semibold lh-1"><?php echo $counts['Order'] ?></h4>
                                <div class="d-flex flex-row justify-content-center gap-1">
                                    <span style="color: whitesmoke;"><i class="fa-solid fa-basket-shopping"></i></span>
                                    <p class="mb-0 fs-4">Orders</p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <div class="col-lg-4 order-first d-flex justify-content-center"></div>
            </div>
            <ul class="nav nav-pills user-profile-tab justify-content-start mt-2 rounded-2" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link position-relative rounded-0 active d-flex align-items-center justify-content-center bg-transparent fs-3 py-6" id="pills-order-tab" data-bs-toggle="pill" data-bs-target="#pills-order" type="button" role="tab" aria-controls="pills-order" aria-selected="true">
                        <i class="fa-solid fa-basket-shopping me-2 fs-6"></i>
                        <span class="d-none d-md-block">Order</span>
                    </button>
                </li>
                <?php if ($counts['User'] == 'Worker') { ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link position-relative  rounded-0 d-flex align-items-center justify-content-center bg-transparent fs-3 py-6" id="pills-review-tab" data-bs-toggle="pill" data-bs-target="#pills-review" type="button" role="tab" aria-controls="pills-review" aria-selected="false" tabindex="-1">
                        <i class="fa-solid fa-eye me-2 fs-6"></i>
                        <span class="d-none d-md-block">Review</span>
                    </button>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>

    <div class="container">
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-order" role="tabpanel" aria-labelledby="pills-order-tab" tabindex="0">
                <div class="d-sm-flex align-items-center justify-content-between mt-3 mb-4">
                    <h3 class="mb-3 mb-sm-0 fw-semibold text-white d-flex align-items-center">Order
                        <span class="badge text-bg-secondary fs-2 rounded-4 py-1 px-2 ms-2"><?php echo $counts['Order'] ?></span>
                    </h3>
                </div>
                <div class="row">
                    <?php foreach ($orders as $order) { ?>
                        <div class="col-md-6 col-xl-4">
                            <div class="card bg-secondary shadow">
                                <div class="card-body p-4 d-flex align-items-center gap-3">
                                    <img src="asset/logo_game/<?php echo $order['Image'] ?>" alt="" class="object-fit-sm-cover border rounded" width="100px" height="100px">
                                    <div>
                                        <h5 class="fw-bold mb-2 text-white"><?php echo $order['Nama_Game'] ?></h5>
                                        <p class="fw-light text-white mb-0">Worker: <?php echo $order['Worker'] ?></p>
                                        <p class="fw-light text-white mb-0">Price: <?php echo $order['Total_Price'] ?></p>
                                        <p class="fw-light text-white mb-0">Message: <?php echo $order['Message'] ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php if ($counts['User'] == 'Worker') { ?>
            <div class="tab-pane fade" id="pills-review" role="tabpanel" aria-labelledby="pills-review-tab" tabindex="0">
                <div class="d-sm-flex align-items-center justify-content-between mt-3 mb-4">
                    <h3 class="mb-3 mb-sm-0 fw-semibold text-white d-flex align-items-center">Review
                        <span class="badge text-bg-secondary fs-2 rounded-4 py-1 px-2 ms-2"><?php echo $counts['Review'] ?></span>
                    </h3>
                </div>
                <div class="row">
                    <?php foreach ($reviews as $row) { ?>
                        <div class="col-md-6 col-xl-4">
                            <div class="card bg-secondary">
                                <div class="card-body p-4 d-flex align-items-center gap-3">
                                    <div>
                                        <div class="d-flex flex-row align-items-center gap-3 mb-2">
                                            <img src="image/<?php echo $row['Foto_Customer'] ?>" alt="" class="object-fit-sm-cover border rounded" width="50px" height="50px">
                                            <h5 class="fw-bold mb-0 text-white d-flex justify-content-center"><?php echo $row['Customer'] ?></h5>
                                        </div>
                                        <p class="fw-semibold text-white mb-1"><?php echo $row['Nama_Game'] ?></p>
                                        <p class="fw-light text-white mb-0"><?php echo $row['Initial_Rank'] ?> -> <?php echo $row['Final_Rank'] ?></p>
                                        <p class="fw-light text-white mb-0">Rating: <?php echo $row['Rating'] ?></p>
                                        <p class="fw-light text-white mb-0">Review: <?php echo $row['Review'] ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
</body>
</html>