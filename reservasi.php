<?php
session_start();
include('layout/navbar.php');
include 'server/connection.php';

// Ambil data ruangan dari database dengan harga
$queryRooms = "SELECT * FROM room";
$resultRooms = mysqli_query($conn, $queryRooms);

// Organisasi ruangan berdasarkan tipe dengan informasi lengkap
$rooms = [
  'reguler' => [],
  'vip' => [],
  'private' => []
];

$roomPrices = []; // Menyimpan harga per ID room untuk akses cepat

while ($row = mysqli_fetch_assoc($resultRooms)) {
  $type = strtolower($row['type_room']); // Pastikan lowercase: reguler, vip, private
  if (isset($rooms[$type])) {
    $rooms[$type][] = [
      'id' => $row['id_room'],
      'name' => $row['section_room'],
      'img' => 'assets/images/' . $row['gambar'],
      'price' => $row['harga'] // Ambil harga dari database
    ];
    
    // Simpan harga berdasarkan ID room untuk referensi JavaScript
    $roomPrices[$row['id_room']] = $row['harga'];
  }
}

// Query untuk mengambil waktu terpakai, HANYA dari reservasi dengan payment_status yang bukan 'rejected'
$reservedTimes = [];
$sqlReserved = "
    SELECT r.id_room, r.start_time, r.end_time
    FROM reservasi r
    JOIN payments p ON r.id_payments = p.id_payments
    WHERE p.payment_status IN ('pending', 'confirmed', 'paid', 'success')
      AND p.payment_status != 'rejected'
      AND r.reservation_date = CURDATE()";
    
$resultReserved = mysqli_query($conn, $sqlReserved);
while ($row = mysqli_fetch_assoc($resultReserved)) {
  $id = $row['id_room'];
  if (!isset($reservedTimes[$id])) $reservedTimes[$id] = [];
  $reservedTimes[$id][] = ['start' => $row['start_time'], 'end' => $row['end_time']];
}

$jsonReservedTimes = json_encode($reservedTimes);

// Encode untuk dikirim ke JavaScript
$jsonRooms = json_encode($rooms);
$jsonRoomPrices = json_encode($roomPrices);

// Pastikan id_user sudah ada di session jika user sudah login
if (!isset($_SESSION['id_user']) && isset($_SESSION['username'])) {
  $username = $_SESSION['username'];
  $queryUser = "SELECT id_user FROM users WHERE username = '$username' LIMIT 1";
  $resultUser = mysqli_query($conn, $queryUser);
  if ($resultUser && $userRow = mysqli_fetch_assoc($resultUser)) {
    $_SESSION['id_user'] = $userRow['id_user'];
  }
}

// Ambil deskripsi room types dari database (asumsi ada tabel room_types atau informasi di tabel room)
// Jika tidak ada tabel terpisah, kita bisa menggunakan data statis atau membuat query untuk mengambil info unik per type
$roomTypeDescriptions = [];
$queryRoomTypes = "SELECT DISTINCT type_room FROM room";
$resultRoomTypes = mysqli_query($conn, $queryRoomTypes);
while ($row = mysqli_fetch_assoc($resultRoomTypes)) {
  $type = strtolower($row['type_room']);
  // Anda bisa menyimpan deskripsi ini di database atau membuatnya dinamis
  switch($type) {
    case 'reguler':
      $roomTypeDescriptions[$type] = "Reguler - Kapasitas 4 orang. Aturan: Tidak boleh merokok, waktu maksimal 2 jam.";
      break;
    case 'vip':
      $roomTypeDescriptions[$type] = "VIP - Kapasitas 8 orang. Aturan: Boleh membawa makanan, waktu maksimal 4 jam.";
      break;
    case 'private':
      $roomTypeDescriptions[$type] = "Private - Kapasitas 2 orang. Aturan: Privasi penuh, waktu maksimal 3 jam.";
      break;
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <title>Pixel Station - Reservasi Page</title>
  <!-- Bootstrap core CSS -->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Additional CSS Files -->
  <link rel="stylesheet" href="assets/css/fontawesome.css">
  <link rel="stylesheet" href="assets/css/templatemo-lugx-gaming.css">
  <link rel="stylesheet" href="assets/css/owl.css">
  <link rel="stylesheet" href="assets/css/animate.css">
  <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
</head>

<body>
  <div class="page-heading header-text">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <h3>Reservation</h3>
          <span class="breadcrumb"><a href="index.php">Home</a>  >  Reservation</span>
        </div>
      </div>
    </div>
  </div>
  
  <div class="contact-page section mt-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="section-heading text-center">
            <h6>RESERVASI</h6>
            <h2>VISIT YOUR SITE</h2>
          </div>
          <div class="right-content">
            <div class="row justify-content-center">
              <div class="col-lg-12">
                <form id="contact-form" action="" method="post" autocomplete="off">
                  <div class="row">
                    <div class="col-lg-12">
                      <fieldset class="mb-3">
                        <div class="row">
                            <div class="col-md-6">
                            <fieldset class="mb-3">
                              <label for="name" class="form-label">Full Name</label>
                              <input type="text" name="nama" id="name" class="form-control"
                              value="<?php echo isset($_SESSION['nama']) ? htmlspecialchars($_SESSION['nama']) : ''; ?>"
                              placeholder="Enter your full name"
                              autocomplete="on" required readonly>
                            </fieldset>
                            <fieldset class="mb-3">
                              <label for="subject" class="form-label">Phone Number</label>
                              <input type="tel" name="telp" id="subject" class="form-control" value="+62" required
                              pattern="\+62[0-9\-]+"
                              autocomplete="on">
                            </fieldset>
                            </div>
                            <div class="col-md-6">
                            <fieldset class="mb-3">
                              <label for="username" class="form-label">Username</label>
                              <input type="text" name="username" id="username" class="form-control"
                              value="<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : ''; ?>"
                              placeholder="Enter username"
                              autocomplete="on" required readonly>
                            </fieldset>
                            <fieldset class="mb-3">
                              <label for="reservation-date" class="form-label">Reservation Date</label>
                              <input type="text" id="reservation-date" name="reservation_date" class="form-control datepicker" 
                                placeholder="Select date..." required>
                            </fieldset>
                            </div>
                        </div>
                        <div class="mb-3">
                          <fieldset class="mb-0">
                            <select name="room_type" id="room_type" required class="form-control">
                              <option value="" disabled selected>Pilih Tipe Ruangan</option>
                              <?php foreach($roomTypeDescriptions as $type => $description): ?>
                              <option value="<?php echo $type; ?>">
                                <?php echo $description; ?>
                              </option>
                              <?php endforeach; ?>
                            </select>
                          </fieldset>
                        </div>

                        <fieldset id="room-selection-fieldset" class="mb-3" style="display:none;">
                          <label for="room-selection" class="mb-2 fw-semibold">Pilih Ruangan:</label>
                          <div id="room-selection" class="d-flex flex-wrap gap-2">
                            <!-- Room radio/button will be rendered here -->
                          </div>
                        </fieldset>

                        <!-- Price Display -->
                        <div id="price-display" class="mb-3" style="display:none;">
                          <div class="alert alert-info">
                            <strong>Harga per Jam:</strong> <span id="room-price">Rp 0</span>
                          </div>
                        </div>

                        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
                        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
                        <script>
                          flatpickr("#reservation-date", {
                            minDate: "today",
                            dateFormat: "Y-m-d",
                            disableMobile: true
                          });

                          // Data ruangan dan harga dari database
                          const roomsData = <?php echo $jsonRooms; ?>;
                          const roomPrices = <?php echo $jsonRoomPrices; ?>;

                          const roomTypeSelect = document.getElementById('room_type');
                          const roomSelectionFieldset = document.getElementById('room-selection-fieldset');
                          const roomSelectionDiv = document.getElementById('room-selection');
                          const priceDisplay = document.getElementById('price-display');
                          const roomPriceSpan = document.getElementById('room-price');
                          let selectedRoomType = "";
                          let selectedRoomId = "";

                          roomTypeSelect.addEventListener('change', function() {
                            selectedRoomType = this.value;
                            renderRoomOptions(selectedRoomType);
                            roomSelectionFieldset.style.display = 'block';
                            priceDisplay.style.display = 'none'; // Hide price until room is selected
                          });

                          function renderRoomOptions(type) {
                            roomSelectionDiv.innerHTML = '';
                            if (!type || !roomsData[type]) return;

                            // Render radio buttons for room selection
                            roomsData[type].forEach(room => {
                              const radio = document.createElement('input');
                              radio.type = 'radio';
                              radio.name = 'id_room';
                              radio.value = room.id;
                              radio.id = 'room_' + room.id;
                              radio.className = 'btn-check';
                              radio.autocomplete = 'off';

                              const label = document.createElement('label');
                              label.className = 'btn btn-outline-primary mb-2';
                              label.htmlFor = radio.id;
                              label.innerHTML = `${room.name}<br><small>Rp ${parseInt(room.price).toLocaleString('id-ID')}/jam</small>`;

                              // Add event listener for price display
                              radio.addEventListener('change', function() {
                                if (this.checked) {
                                  selectedRoomId = this.value;
                                  const price = parseInt(room.price);
                                  roomPriceSpan.textContent = `Rp ${price.toLocaleString('id-ID')}`;
                                  priceDisplay.style.display = 'block';
                                }
                              });

                              roomSelectionDiv.appendChild(radio);
                              roomSelectionDiv.appendChild(label);
                            });
                          }

                          // Format number to Indonesian currency
                          function formatCurrency(amount) {
                            return new Intl.NumberFormat('id-ID', {
                              style: 'currency',
                              currency: 'IDR',
                              minimumFractionDigits: 0,
                              maximumFractionDigits: 0
                            }).format(amount);
                          }
                        </script>
                        <style>
                          #contact-form fieldset {
                            border: none;
                            padding: 0;
                            margin: 0;
                          }
                          #contact-form input,
                          #contact-form select {
                            min-height: 45px;
                          }
                          #room-selection label {
                            min-width: 120px;
                            text-align: center;
                          }
                          #room-selection label small {
                            color: #666;
                            font-weight: normal;
                          }
                          @media (max-width: 767.98px) {
                            .d-flex.flex-md-row {
                              flex-direction: column !important;
                            }
                          }
                        </style>
                        <div class="col-lg-12 text-center">
                            <fieldset>
                            <?php if (!isset($_SESSION['username'])): ?>
                              <button class="mt-5" type="button" id="login-submit">Login to Make Payment</button>
                                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                <script>
                                document.getElementById('login-submit').addEventListener('click', function() {
                                  Swal.fire({
                                  icon: 'warning',
                                  title: 'Belum Login',
                                  text: 'Silakan login terlebih dahulu untuk melakukan reservasi.',
                                  confirmButtonText: 'Login',
                                  allowOutsideClick: false,
                                  iconPosition: 'center',
                                  customClass: {
                                  icon: 'swal2-icon-center'
                                  }
                                  }).then((result) => {
                                  if (result.isConfirmed) {
                                  window.location.href = 'login.php';
                                  }
                                  });
                                });
                                </script>
                                <style>
                                .swal2-icon-center {
                                  margin: 1em auto !important;
                                }
                                </style>
                            <?php else: ?>
                              <button type="button" id="form-submit" class="mt-5">Make Payment</button>
                            <?php endif; ?>
                            </fieldset>
                        </div>
                      </fieldset>
                    </div>
                  </div>
                </form>

                <!-- Modal -->
                <div class="modal fade" id="roomModal" tabindex="-1" aria-labelledby="roomModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="roomModalLabel">Detail Reservasi Ruangan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body text-center">
                        <img id="modal-room-img" src="assets/images/room.png" alt="Room Image" class="img-fluid mb-3" style="max-height:200px;">
                        <div id="reserved-times" class="mb-3"></div>
                        <div class="row mb-3">
                          <div class="col-6">
                            <label for="start-time" class="form-label">Start Time</label>
                            <select id="start-time" class="form-control"></select>
                          </div>
                          <div class="col-6">
                            <label for="end-time" class="form-label">End Time</label>
                            <select id="end-time" class="form-control"></select>
                          </div>
                        </div>
                        <div class="alert alert-success" id="price-calculation" style="display:none;">
                          <strong>Estimasi Biaya:</strong> <span id="total-price">Rp 0</span>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="confirm-reservation">Confirm</button>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- End Modal -->

                <script>
                  // Data reserved times dari server (untuk tanggal hari ini saat load)
                  let currentReservedTimes = <?php echo $jsonReservedTimes; ?>;
                  let selectedDate = "";

                  // Event listener untuk perubahan tanggal
                  document.getElementById('reservation-date').addEventListener('change', function() {
                    selectedDate = this.value;
                    if (selectedDate) {
                      fetchReservedTimes(selectedDate);
                    }
                    // Tutup modal jika sedang terbuka agar user harus buka ulang dan data waktu terpakai update
                    const modalEl = document.getElementById('roomModal');
                    if (modalEl && bootstrap.Modal.getInstance(modalEl)) {
                      bootstrap.Modal.getInstance(modalEl).hide();
                    }
                  });

                  // Fungsi untuk mengambil reserved times berdasarkan tanggal (AJAX ke getReservedTimes.php)
                  function fetchReservedTimes(date) {
                    fetch(`getReservedTimes.php?date=${encodeURIComponent(date)}`)
                      .then(response => response.json())
                      .then(data => {
                        currentReservedTimes = data;
                        // Jika modal sedang terbuka, update tampilan waktu terpakai
                        // (opsional: bisa trigger ulang modal jika perlu)
                      })
                      .catch(error => console.error('Error:', error));
                  }

                  // Generate all times in 15-minute increments from 08:00 to 18:00
                  function generate15MinTimes() {
                    const times = [];
                    for (let h = 8; h <= 18; h++) {
                      for (let m = 0; m < 60; m += 15) {
                        if (h === 18 && m > 0) break;
                        const hh = h.toString().padStart(2, '0');
                        const mm = m.toString().padStart(2, '0');
                        times.push(`${hh}:${mm}`);
                      }
                    }
                    return times;
                  }
                  const allTimes = generate15MinTimes();

                  // Helper: tambah 1 jam ke waktu "HH:MM"
                  function addOneHour(timeStr) {
                    const [h, m] = timeStr.split(':').map(Number);
                    let date = new Date(2000, 0, 1, h, m);
                    date.setHours(date.getHours() + 1);
                    let hh = date.getHours().toString().padStart(2, '0');
                    let mm = date.getMinutes().toString().padStart(2, '0');
                    return `${hh}:${mm}`;
                  }

                  // Helper: cek overlap dua interval waktu [start1, end1] dan [start2, end2]
                  function isOverlap(start1, end1, start2, end2) {
                    return (start1 < end2 && start2 < end1);
                  }

                  // Function to calculate price
                  function calculatePrice(startTime, endTime, roomId) {
                    const startHour = parseInt(startTime.split(':')[0]) + parseInt(startTime.split(':')[1])/60;
                    const endHour = parseInt(endTime.split(':')[0]) + parseInt(endTime.split(':')[1])/60;
                    const duration = endHour - startHour;
                    const pricePerHour = roomPrices[roomId] || 0;
                    return pricePerHour * duration;
                  }

                  // Show modal hanya saat tombol "Make Payment" ditekan
                  <?php if (isset($_SESSION['username'])): ?>
                  document.getElementById('form-submit').addEventListener('click', function(e) {
                    e.preventDefault();

                    const roomId = document.querySelector('input[name="id_room"]:checked')?.value;
                    const date = document.getElementById('reservation-date').value;

                    if (!roomId || !date) {
                      alert("Mohon lengkapi data reservasi!");
                      return;
                    }

                    // Set gambar ruangan
                    const roomImg = roomsData[selectedRoomType].find(r => r.id == roomId)?.img;
                    document.getElementById('modal-room-img').src = roomImg;

                    // Tampilkan waktu terpakai
                    const times = currentReservedTimes[roomId] || [];
                    function formatTimeHM(timeStr) {
                      return timeStr ? timeStr.slice(0,5) : '';
                    }
                    const reservedList = times.length
                      ? times.map(t => `<span class="badge rounded-pill bg-danger mb-1" style="font-size:1em;"> ${formatTimeHM(t.start)} - ${formatTimeHM(t.end)}</span>`).join(' ')
                      : '<span class="text-muted">Belum ada reservasi</span>';
                    document.getElementById('reserved-times').innerHTML = `<strong>Waktu Terpakai:</strong><br>${reservedList}`;

                    // Isi pilihan waktu mulai (start time)
                    const startSelect = document.getElementById('start-time');
                    const endSelect = document.getElementById('end-time');
                    const priceCalculation = document.getElementById('price-calculation');
                    const totalPriceSpan = document.getElementById('total-price');
                    
                    startSelect.innerHTML = '';
                    endSelect.innerHTML = '';
                    priceCalculation.style.display = 'none';

                    // Filter start times agar tidak bentrok dengan reservasi yang sudah ada
                    function isStartTimeAvailable(start) {
                      const end = addOneHour(start);
                      const times = currentReservedTimes[roomId] || [];
                      for (const t of times) {
                        if (isOverlap(start, end, t.start, t.end)) return false;
                      }
                      return true;
                    }

                    allTimes.forEach(time => {
                      if (isStartTimeAvailable(time)) {
                        const option = new Option(time, time);
                        startSelect.add(option);
                      }
                    });

                    function updatePriceCalculation() {
                      const start = startSelect.value;
                      const end = endSelect.value;
                      if (start && end) {
                        const totalPrice = calculatePrice(start, end, roomId);
                        totalPriceSpan.textContent = formatCurrency(totalPrice);
                        priceCalculation.style.display = 'block';
                      } else {
                        priceCalculation.style.display = 'none';
                      }
                    }

                    startSelect.addEventListener('change', function () {
                      endSelect.innerHTML = '';
                      priceCalculation.style.display = 'none';
                      const startTime = this.value;
                      if (!startTime) return;

                      // Fungsi menambahkan jam ke waktu HH:MM
                      function addHours(time, hoursToAdd) {
                        const [hours, minutes] = time.split(':').map(Number);
                        const date = new Date();
                        date.setHours(hours);
                        date.setMinutes(minutes);
                        date.setSeconds(0);
                        date.setMilliseconds(0);
                        date.setHours(date.getHours() + hoursToAdd);
                        return date.toTimeString().substring(0, 5);
                      }

                      const maxTime = '18:00';

                      // Jika start time di 17:15, 17:30, atau 17:45, langsung set end time ke 18:00
                      if (['17:15', '17:30', '17:45'].includes(startTime)) {
                        // Validasi bentrok
                        let conflict = false;
                        for (const t of times) {
                          if (isOverlap(startTime, '18:00', t.start, t.end)) {
                            conflict = true;
                            break;
                          }
                        }
                        if (!conflict) {
                          const option = new Option('18:00', '18:00');
                          endSelect.add(option);
                        }
                      } else {
                        let currentHour = 1;
                        let newTime = addHours(startTime, currentHour);

                        while (newTime <= maxTime) {
                          // Validasi bentrok
                          let conflict = false;
                          for (const t of times) {
                            if (isOverlap(startTime, newTime, t.start, t.end)) {
                              conflict = true;
                              break;
                            }
                          }
                          if (!conflict) {
                            const option = new Option(newTime, newTime);
                            endSelect.add(option);
                          }
                          currentHour++;
                          newTime = addHours(startTime, currentHour);
                        }
                      }
                      updatePriceCalculation();
                    });

                    endSelect.addEventListener('change', updatePriceCalculation);

                    // Trigger change untuk populate end time awal
                    startSelect.dispatchEvent(new Event('change'));

                    // Tampilkan modal
                    const roomModal = new bootstrap.Modal(document.getElementById('roomModal'));
                    roomModal.show();
                  });
                  <?php endif; ?>

                  // Submit data ketika tombol Confirm di modal ditekan
                  document.getElementById('confirm-reservation').addEventListener('click', function() {
                    const start = document.getElementById('start-time').value;
                    const end = document.getElementById('end-time').value;

                    if (!start || !end) {
                      alert("Mohon pilih waktu mulai dan selesai!");
                      return;
                    }

                    // Final check: validasi bentrok sebelum submit
                    const roomId = document.querySelector('input[name="id_room"]:checked')?.value;
                    const times = currentReservedTimes[roomId] || [];
                    for (const t of times) {
                      if (isOverlap(start, end, t.start, t.end)) {
                        alert("Waktu yang dipilih bentrok dengan reservasi lain. Silakan pilih waktu lain.");
                        return;
                      }
                    }

                    // Create form to submit to payment.php
                    const roomType = document.getElementById('room_type').value;
                    const reservationDate = document.getElementById('reservation-date').value;
                    const roomName = roomsData[roomType].find(r => r.id == roomId)?.name || '';
                    
                    // Calculate price and duration using database values
                    const startTime = start;
                    const endTime = end;
                    
                    // Calculate duration in hours
                    const startHour = parseInt(startTime.split(':')[0]) + parseInt(startTime.split(':')[1])/60;
                    const endHour = parseInt(endTime.split(':')[0]) + parseInt(endTime.split(':')[1])/60;
                    const duration = endHour - startHour;
                    
                    // Get price from database (already loaded in roomPrices)
                    const pricePerHour = roomPrices[roomId] || 0;
                    const totalPrice = pricePerHour * duration;
                    
                    // Create form to submit to payment.php
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'payment.php';
                    
                    // Add all needed fields
                    const fields = {
                      'id_room': roomId,
                      'nama': document.querySelector('input[name="nama"]').value,
                      'username': document.querySelector('input[name="username"]').value,
                      'telp': document.querySelector('input[name="telp"]').value,
                      'reservation_date': reservationDate,
                      'start_time': startTime,
                      'end_time': endTime,
                      'room_type': roomType,
                      'room_name': roomName,
                      'duration': duration.toFixed(2),
                      'price': totalPrice.toFixed(0)
                    };
                    
                    // Add form fields
                    Object.entries(fields).forEach(([key, value]) => {
                      const input = document.createElement('input');
                      input.type = 'hidden';
                      input.name = key;
                      input.value = value;
                      form.appendChild(input);
                    });
                    
                    // Append to body and submit
                    document.body.appendChild(form);
                    form.submit();
                  });
                </script>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
<?php include('layout/footer.php')?>

  <!-- Bootstrap core JavaScript -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script> 
  <script src="assets/js/isotope.min.js"></script>
  <script src="assets/js/owl-carousel.js"></script>
  <script src="assets/js/counter.js"></script>
  <script src="assets/js/custom.js"></script>

  <script>
    // Ensure modals work if loaded dynamically or if there are JS conflicts
    $(document).ready(function(){
      // No additional JS needed for Bootstrap 4 modals if data attributes are correct
    });
  </script>
</body>
</html>