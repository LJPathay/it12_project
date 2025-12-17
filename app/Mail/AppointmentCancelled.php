<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentCancelled extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Appointment $appointment;
    public string $reason;

    public function __construct(Appointment $appointment, ?string $reason = null)
    {
        $this->appointment = $appointment->loadMissing('user');
        $this->reason = $reason ?? 'Time slot was filled by another patient';
    }

    public function build(): self
    {
        return $this
            ->subject('Appointment Cancelled - Please Reschedule')
            ->view('emails.appointment_cancelled')
            ->with([
                'appointment' => $this->appointment,
                'reason' => $this->reason,
            ]);
    }
}
