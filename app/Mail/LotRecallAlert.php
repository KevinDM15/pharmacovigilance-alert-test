<?php

namespace App\Mail;

use App\Models\Customer;
use App\Models\Medication;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LotRecallAlert extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Customer $customer,
        public Order $order,
        public Medication $medication,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Important Recall Notice: {$this->medication->name} (Lot {$this->medication->lot_number})",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.lot-recall-alert',
        );
    }
}
