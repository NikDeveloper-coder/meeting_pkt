<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Booking</title>
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background: linear-gradient(to right, #6a11cb, #2575fc);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .card {
      border-radius: 20px;
      box-shadow: 0px 4px 20px rgba(0,0,0,0.2);
    }
    .btn-custom {
      background: #6a11cb;
      color: white;
      border-radius: 10px;
      transition: 0.3s;
    }
    .btn-custom:hover {
      background: #2575fc;
      transform: scale(1.05);
    }
    .btn-back {
      background: #6c757d;
      color: white;
      border-radius: 10px;
      transition: 0.3s;
    }
    .btn-back:hover {
      background: #5a6268;
      transform: scale(1.05);
    }
  </style>
</head>
<body>
  <?php if (session()->getFlashdata('success')): ?>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
          icon: 'success',
          title: 'Booking Successful!',
          text: 'Your booking has been saved.',
          showConfirmButton: false,
          timer: 2000
        }).then(function() {
          window.location.href = '<?= site_url("dashboard") ?>';
        });
      });
    </script>
  <?php endif; ?>

  <?php if (session()->getFlashdata('error')): ?>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
          icon: 'error',
          title: 'Booking Failed!',
          text: 'Please try again later.'
        });
      });
    </script>
  <?php endif; ?>

  <div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 text-center" style="width: 400px;">
      <h3 class="mb-4">Meeting Room Booking</h3>
      <button class="btn btn-custom w-100 mb-3" data-bs-toggle="modal" data-bs-target="#bookingModal">Book Now</button>
      <a href="<?= site_url('dashboard') ?>" class="btn btn-back w-100">Back to Dashboard</a>
    </div>
  </div>

  <!-- Modal Booking Form -->
  <div class="modal fade" id="bookingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content p-4 rounded-4">
        <h4 class="text-center mb-3">Fill Booking Details</h4>
        <form method="POST" action="<?= site_url('booking') ?>" enctype="multipart/form-data">
          
          <div class="mb-3">
            <label class="form-label">Booking ID</label>
            <input type="text" name="booking_ref" class="form-control" value="<?= $booking_ref ?>" readonly>
          </div>

          <div class="mb-3">
            <label class="form-label">User ID</label>
            <input type="text" name="user_id" class="form-control" value="<?= $user_id ?>" readonly>
          </div>

          <div class="mb-3">
            <label class="form-label">Current Date</label>
            <input type="text" class="form-control" value="<?= $current_date ?>" readonly>
          </div>

          <div class="mb-3">
            <label class="form-label">Booking Date</label>
            <input type="date" name="booking_date" class="form-control" required>
          </div>

          <div class="row">
            <div class="col mb-3">
              <label class="form-label">Start Time</label>
              <input type="time" name="start_time" class="form-control" required>
            </div>
            <div class="col mb-3">
              <label class="form-label">End Time</label>
              <input type="time" name="end_time" class="form-control" required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Reason</label>
            <textarea name="reason" class="form-control" rows="2" required></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Attachment (optional)</label>
            <input type="file" name="attachment" class="form-control">
          </div>

          <div class="mb-3">
            <label class="form-label">Extra Request</label>
            <textarea name="extra_request" class="form-control" rows="2"></textarea>
          </div>

          <div class="text-end">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-custom">Submit Booking</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>
</html>