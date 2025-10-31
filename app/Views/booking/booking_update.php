<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Booking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .container { max-width: 800px; margin-top: 50px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Back to Main Page Button -->
        <div class="mb-3">
            <a href="<?= site_url('dashboard') ?>" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left"></i> Back to Main Page
            </a>
        </div>

        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0"><i class="bi bi-pencil"></i> Update Booking</h4>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class='alert alert-success'><?= session()->getFlashdata('success') ?></div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class='alert alert-danger'><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

                <?php if (isset($errors)): ?>
                    <div class='alert alert-danger'>
                        <ul class='mb-0'>
                            <?php foreach ($errors as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= site_url('booking/update/' . $booking['id']) ?>" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Booking Date</label>
                            <input type="date" name="booking_date" class="form-control" 
                                   value="<?= htmlspecialchars($booking['booking_date'] ?? '') ?>" required>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Start Time</label>
                            <input type="time" name="start_time" class="form-control" 
                                   value="<?= htmlspecialchars($booking['start_time'] ?? '') ?>" required>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label">End Time</label>
                            <input type="time" name="end_time" class="form-control" 
                                   value="<?= htmlspecialchars($booking['end_time'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea name="reason" class="form-control" rows="3" required><?= htmlspecialchars($booking['reason'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Extra Request</label>
                        <textarea name="extra_request" class="form-control" rows="2"><?= htmlspecialchars($booking['extra_request'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Current Attachment</label>
                        <div>
                            <?php if(!empty($booking['doc_Attachment'])): ?>
                                <a href="<?= base_url($booking['doc_Attachment']) ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye"></i> View Current File
                                </a>
                            <?php else: ?>
                                <span class="text-muted">No attachment</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">New Attachment (Optional - Replace current)</label>
                        <input type="file" name="doc_attachment" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <div class="form-text">Upload new file to replace current attachment (PDF, DOC, JPG, PNG)</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Update Booking
                        </button>
                        <a href="<?= site_url('dashboard') ?>" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>