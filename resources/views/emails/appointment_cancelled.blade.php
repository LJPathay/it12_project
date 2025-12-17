<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Appointment Cancelled</title>
    <style>
        body { font-family: Arial, sans-serif; color: #222; }
        .container { max-width: 640px; margin: 0 auto; padding: 16px; }
        .card { border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; }
        .h1 { font-size: 20px; margin: 0 0 12px; }
        .muted { color: #6b7280; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 9999px; background: #ef4444; color: #fff; font-size: 12px; }
        .row { margin: 6px 0; }
        .alert { background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; padding: 12px; margin: 16px 0; }
        .btn { display: inline-block; padding: 10px 20px; background: #3b82f6; color: #fff; text-decoration: none; border-radius: 6px; margin-top: 12px; }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <p class="badge">Cancelled</p>
        <h1 class="h1">Your appointment has been cancelled</h1>
        <p>Hello {{ $appointment->patient_name ?? ($appointment->user->name ?? 'Patient') }},</p>
        
        <div class="alert">
            <p style="margin: 0;"><strong>Reason:</strong> {{ $reason }}</p>
        </div>

        <p>We're sorry, but your appointment has been automatically cancelled. Here were the details:</p>

        <div class="row"><strong>Service:</strong> {{ $appointment->service_type }}</div>
        <div class="row"><strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</div>
        <div class="row"><strong>Time:</strong> {{ $appointment->appointment_time }}</div>

        <p style="margin-top: 20px;">
            <strong>What to do next:</strong><br>
            Please book a new appointment at your earliest convenience. We apologize for any inconvenience this may have caused.
        </p>

        <a href="{{ route('patient.book-appointment') }}" class="btn">Book New Appointment</a>

        <p class="muted" style="margin-top: 20px;">If you have questions, please reply to this email or contact us directly.</p>
        <p>— {{ config('app.name') }}</p>
    </div>
</div>
</body>
</html>
