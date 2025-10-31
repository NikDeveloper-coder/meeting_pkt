<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Meeting Room Availability</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Bootstrap & FullCalendar -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>

  <style>
    :root{
      --bg:#f8fafb;
      --surface:#ffffff;
      --ink:#0f172a;
      --muted:#6b7280;
      --border:#e2e8f0;
      --primary:#06b6d4;  /* TEAL AQUA */
      --primary-light:#cffafe;
      --primary-dark:#0e7490;
      --approved:#ef4444;
      --pending:#f59e0b;
    }

    body{
      background:var(--bg);
      color:var(--ink);
      font-family:'Segoe UI', system-ui, sans-serif;
    }

    /* Header */
    .page-header{
      max-width:1180px;
      margin:28px auto 12px;
      padding:18px 24px;
      background:var(--surface);
      border:1px solid var(--border);
      border-radius:14px;
      box-shadow:0 4px 18px rgba(0,0,0,.05);
    }
    .legend{
      display:flex;flex-wrap:wrap;gap:.5rem;
    }
    .chip{
      display:inline-flex;align-items:center;gap:.5rem;
      padding:.35rem .7rem;border:1px solid var(--border);
      border-radius:999px;background:#fff;color:var(--muted);
      font-size:.85rem;
    }
    .chip .dot{width:.55rem;height:.55rem;border-radius:50%}
    .dot-green{background:#10b981}
    .dot-red{background:var(--approved)}
    .dot-yellow{background:var(--pending)}

    /* Login */
    .login-btn{
      position:fixed;top:18px;right:18px;z-index:1000;
    }
    .btn-primary{
      background:var(--primary);
      border:none;
    }
    .btn-primary:hover{
      background:var(--primary-dark);
    }

    /* Calendar wrapper */
    .main{
      max-width:1180px;margin:0 auto;padding:0 16px 28px;
    }
    .card-surface{
      background:var(--surface);
      border:1px solid var(--border);
      border-radius:16px;
      box-shadow:0 8px 22px rgba(15,23,42,.06);
      padding:20px;
    }

    /* FullCalendar styles */
    .fc .fc-toolbar-title{font-size:1.25rem;font-weight:700;color:var(--primary-dark);}
    .fc .fc-button{
      border-radius:8px;
      border:1px solid var(--border);
      background:#fff;
      color:var(--primary-dark);
      transition:0.2s;
    }
    .fc .fc-button:hover{
      background:var(--primary-light);
      color:var(--ink);
    }
    .fc-theme-standard td, .fc-theme-standard th{border-color:var(--border);}
    .fc .fc-daygrid-day.fc-day-today{
      background:#f0fdfa; /* subtle aqua */
      position:relative;
    }
    .fc .fc-daygrid-day.fc-day-today::after{
      content:"";
      position:absolute;inset:3px;
      border:2px solid var(--primary);
      border-radius:10px;
      pointer-events:none;
    }

    /* Event styling */
    .fc .fc-daygrid-event{
      border-radius:10px;
      padding:2px 8px 2px 12px;
      border:1px solid #bae6fd;
      background:#e0f7fa;
      color:#075985;
      position:relative;
      overflow:hidden;
      transition:0.15s;
    }
    .fc .fc-daygrid-event:hover{transform:translateY(-1px);box-shadow:0 6px 14px rgba(0,0,0,.08);}
    .fc-event-bar::before{
      content:"";
      position:absolute;left:0;top:0;bottom:0;width:4px;
      background:var(--primary-dark);
      border-top-left-radius:10px;border-bottom-left-radius:10px;
    }
    .event-approved{background:#fee2e2 !important;border-color:#fecaca !important;color:#b91c1c !important;}
    .event-approved.fc-event-bar::before{background:var(--approved);}
    .event-pending{background:#fff7ed !important;border-color:#fed7aa !important;color:#92400e !important;}
    .event-pending.fc-event-bar::before{background:var(--pending);}

    /* Help box */
    .help{
      max-width:1180px;margin:12px auto 32px;
      padding:16px 20px;background:#fff;border:1px solid var(--border);
      border-radius:12px;
    }
    .help li{color:var(--muted)}

    /* Modal */
    .modal-content{border-radius:14px;}
    .badge-soft-red{background:rgba(239,68,68,.16);color:#b91c1c;}
    .badge-soft-yellow{background:rgba(245,158,11,.18);color:#7c4603;}
  </style>
</head>
<body>
  <!-- Login -->
  <div class="login-btn">
    <a href="<?= site_url('login') ?>" class="btn btn-primary btn-lg shadow">
      <i class="bi bi-box-arrow-in-right"></i> Login
    </a>
  </div>

  <!-- Header -->
  <header class="page-header">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
      <div>
        <h1 class="h3 mb-1 text-teal">Meeting Room Availability</h1>
        <div class="text-muted">Clean teal dashboard view with a simple large calendar</div>
      </div>
      <div class="legend">
        <span class="chip"><span class="dot dot-green"></span> Available</span>
        <span class="chip"><span class="dot dot-red"></span> Booked (Approved)</span>
        <span class="chip"><span class="dot dot-yellow"></span> Pending</span>
      </div>
    </div>
  </header>

  <!-- Calendar -->
  <main class="main">
    <div class="card-surface">
      <div id="calendar"></div>
    </div>
  </main>

  <!-- Help -->
  <section class="help">
    <h6 class="mb-2"><i class="bi bi-info-circle me-2 text-primary"></i>How to use</h6>
    <div class="row">
      <div class="col-md-6">
        <ul class="mb-0">
          <li>Click a date to list all bookings for that day.</li>
          <li>Click an event to view booking details.</li>
        </ul>
      </div>
      <div class="col-md-6">
        <ul class="mb-0">
          <li>Red = Approved (occupied), Yellow = Pending approval.</li>
          <li>“Today” outlined with teal border for easy spotting.</li>
        </ul>
      </div>
    </div>
  </section>

  <!-- Booking Details Modal -->
  <div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-calendar-event me-2 text-primary"></i>Booking Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="bookingDetails"></div>
        </div>
        <div class="modal-footer">
          <a href="<?= site_url('login') ?>" class="btn btn-primary">
            <i class="bi bi-box-arrow-in-right me-1"></i> Login to Book
          </a>
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- FullCalendar -->
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const calendarEl = document.getElementById('calendar');
      const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 'auto',
        expandRows: true,
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: <?= json_encode($calendarEvents) ?>,
        eventDisplay: 'block',
        eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: true },

        eventDidMount: function(info){
          const props = info.event.extendedProps || {};
          const s = (props.status || '').toLowerCase();
          info.el.classList.add('fc-event-bar');
          if (s === 'approved') info.el.classList.add('event-approved');
          else if (s === 'pending') info.el.classList.add('event-pending');
          info.el.title = `${info.event.title || 'Booking'} • ${props.status || 'Pending'}`;
        },

        eventClick: function(info){openEvent(info.event);},
        dateClick: function(info){openDay(info.dateStr);}
      });

      calendar.render();

      function formatTime(evt){
        const s = evt.start ? evt.start.toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'}) : '';
        const e = evt.end ? evt.end.toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'}) : '';
        return s && e ? `${s} – ${e}` : s || e || '';
      }

      function openEvent(event){
        const p = event.extendedProps || {};
        const date = event.start ? event.start.toLocaleDateString() : '-';
        const time = formatTime(event) || '-';
        const status = p.status || 'Pending';
        const badge = status === 'Approved'
          ? '<span class="badge badge-soft-red">Approved</span>'
          : '<span class="badge badge-soft-yellow">Pending</span>';
        document.getElementById('bookingDetails').innerHTML = `
          <div class="row g-3">
            <div class="col-md-4"><div class="text-muted small">Date</div><div class="fw-semibold">${date}</div></div>
            <div class="col-md-4"><div class="text-muted small">Time</div><div>${time}</div></div>
            <div class="col-md-4"><div class="text-muted small">Status</div><div>${badge}</div></div>
            <div class="col-md-6"><div class="text-muted small">Booked by</div><div>${p.user || '-'}</div></div>
            <div class="col-12"><div class="text-muted small">Reason</div><div>${p.reason || '-'}</div></div>
          </div>`;
        new bootstrap.Modal(document.getElementById('bookingModal')).show();
      }

      function openDay(dateStr){
        fetch('<?= site_url('public/getBookedDates') ?>')
          .then(r=>r.json())
          .then(data=>{
            const list = data[dateStr] || [];
            let html = `<h6 class="mb-3"><i class="bi bi-calendar-check me-2 text-primary"></i>Bookings for ${dateStr}</h6>`;
            if(!list.length){
              html += `<div class="alert alert-success"><i class="bi bi-check2-circle me-1"></i>All time slots available today!</div>
              <a href="<?= site_url('login') ?>" class="btn btn-primary">
                <i class="bi bi-box-arrow-in-right me-1"></i> Login to Book
              </a>`;
            } else {
              html += '<div class="list-group">';
              list.forEach(b=>{
                const badge = b.status==='Approved'?'text-bg-danger':'text-bg-warning';
                const icon = b.status==='Approved'?'bi-check-circle':'bi-hourglass-split';
                html += `<div class="list-group-item d-flex justify-content-between align-items-start">
                  <div class="me-3">
                    <div class="fw-semibold"><i class="bi ${icon} me-1"></i>${b.time}</div>
                    <div class="small"><strong>Booked by:</strong> ${b.user}</div>
                    <div class="small text-muted"><strong>Reason:</strong> ${b.reason}</div>
                  </div>
                  <span class="badge ${badge}">${b.status}</span>
                </div>`;
              });
              html += '</div>';
            }
            document.getElementById('bookingDetails').innerHTML = html;
            new bootstrap.Modal(document.getElementById('bookingModal')).show();
          });
      }
    });
  </script>
</body>
</html>
