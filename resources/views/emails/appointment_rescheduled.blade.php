<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; text-align: center; border-bottom: 3px solid #dc3545; }
        .content { padding: 20px; }
        .appointment-details { background-color: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .footer { font-size: 12px; color: #666; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Appointment Reschedule Notice</h2>
        </div>
        <div class="content">
            <p>Dear {{ $appointment->patient_name }},</p>
            
            <p>We regret to inform you that your upcoming appointment needs to be rescheduled due to an emergency or doctor unavailability.</p>
            
            <div class="appointment-details">
                <strong>Original Appointment:</strong><br>
                Date: {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F d, Y') }}<br>
                Time: {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}<br>
                Service: {{ $appointment->service_type }}
            </div>

            <p>Please contact us or visit the health center to schedule a new appointment at your earliest convenience. We apologize for any inconvenience this causes.</p>
            
            <p>Thank you for your understanding.</p>
        </div>
        <div class="footer">
            <p>This is an automated message. Please do not reply directly to this email.</p>
        </div>
    </div>
</body>
</html>
