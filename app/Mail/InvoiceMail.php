<?php

namespace App\Mail;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\{Content, Envelope};

class InvoiceMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * The related transaction.
     *
     * @var \App\Models\Transaction
     */
    public $transaction;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\Transaction $transaction
     * @return void
     */
    public function __construct(Transaction $transaction)
    {
        $this->afterCommit();

        $this->transaction = $transaction->load('from', 'to.userable');
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: [$this->transaction->to->email, $this->transaction->from->email],
            subject: 'Invoice',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice',
            with: ['transaction' => $this->transaction],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
