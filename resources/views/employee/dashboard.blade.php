@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <!-- Dashboard Header -->
        <div class="col-12 mb-4">
            <h3>Employee Dashboard</h3>
            <p>Welcome, {{ Auth::user()->name }}</p>
        </div>

        <!-- Upcoming Reminders -->
        <div class="col-12 mt-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Your Upcoming Reminders</h5>
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

@section('scripts')
<script>
    // Use PHP to generate JSON safely for JS
    var upcomingReminders = <?php echo json_encode($upcomingReminders); ?>;

    document.addEventListener('DOMContentLoaded', function () {
        if (upcomingReminders && upcomingReminders.length) {
            upcomingReminders.forEach(function(reminder) {
                var now = new Date();
                var remindAt = new Date(reminder.remind_at);

                // Trigger pop-up if reminder is due within 5 minutes
                if (remindAt - now <= 5*60*1000 && remindAt - now >= 0) {
                    alert("Reminder: " + reminder.title + "\n" + reminder.description);
                }
            });
        }
    });
</script>
@endsection
