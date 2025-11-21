@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <!-- Dashboard Header -->
        <div class="col-12 mb-4">
            <h3>Admin Dashboard</h3>
            <p>Welcome, {{ Auth::user()->name }}</p>
        </div>

        <!-- Summary Cards -->
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5>Total Leads</h5>
                    <h3>{{ \App\Models\Lead::count() }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5>Total Services</h5>
                    <h3>{{ \App\Models\Service::count() }}</h3>
                </div>
            </div>
        </div>

        <!-- Upcoming Reminders -->
        <div class="col-12 mt-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Upcoming Reminders</h5>
                </div>
                <div class="card-body">
                    @if($upcomingReminders->isEmpty())
                        <p class="text-muted">No upcoming reminders.</p>
                    @else
                        <ul class="list-group">
                            @foreach($upcomingReminders as $reminder)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $reminder->title }}</strong>
                                        <br>
                                        <small>{{ \Carbon\Carbon::parse($reminder->remind_at)->format('d M Y, H:i') }}</small>
                                  <p class="mb-0">{{ $reminder->description }}</p>
                                    </div>
                                    @if($reminder->type === 'lead_followup')
                                        <span class="badge bg-primary">Follow-up</span>
                                    @elseif($reminder->type === 'domain_renewal')
                                        <span class="badge bg-warning">Domain</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const reminders = <?php echo json_encode($upcomingReminders); ?>;
    console.log("Reminders received:", reminders);

    if (!reminders.length) return;

    // Use a flag to avoid repeating popups
    let shown = {};

    // Check every 1 minute
    setInterval(() => {
        const now = new Date();

        reminders.forEach(reminder => {
            const remindAt = new Date(reminder.remind_at);
            const diffInMinutes = (remindAt - now) / (1000 * 60);

            if (diffInMinutes <= 5 && diffInMinutes >= 0 && !shown[reminder.id]) {
                shown[reminder.id] = true;

                // SweetAlert2 popup
                Swal.fire({
                    title: '🔔 Reminder',
                    html: `
                        <strong>${reminder.title}</strong><br>
                        <small>${remindAt.toLocaleString()}</small>
                    `,
                    icon: 'info',
                    confirmButtonText: 'Got it',
                    timer: 15000,
                    timerProgressBar: true,
                    backdrop: true,
                    allowOutsideClick: false,
                    allowEscapeKey: true,
                });
            }
        });
    }, 60000);
});
</script>
@endpush

<!--@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const reminders = ;
    console.log("Reminders received:", reminders);

    if (!reminders.length) return;

    setInterval(() => {
        const now = new Date();
        reminders.forEach(reminder => {
            const remindAt = new Date(reminder.remind_at);
            const diffInMinutes = (remindAt - now) / (1000 * 60);

            if (diffInMinutes <= 5 && diffInMinutes >= 0) {
                alert(`🔔 Reminder: ${reminder.title}\nDue at: ${remindAt.toLocaleString()}`);
            }
        });
    }, 60000);
});
</script>
@endpush-->


