<?php

use Tests\TestCase;
use App\Mail\InvoiceMail;
use Illuminate\Mail\SendQueuedMailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\Traits\HasDummyTransaction;

class InvoiceMailTest extends TestCase
{
    use HasDummyTransaction;

    /**
     * Test if the notification can be queued.
     *
     * @return void
     */
    public function test_if_the_notification_can_be_queued(): void
    {
        Queue::fake();

        $transaction = $this->createDummyTransaction();

        Mail::send(new InvoiceMail($transaction));

        Queue::assertPushed(SendQueuedMailable::class, function (SendQueuedMailable $job) {
            return $job->mailable::class === InvoiceMail::class;
        });
    }

    /**
     * Test invoice mail is sent with correct content and recipients.
     *
     * @return void
     */
    public function test_invoice_mail_is_sent_with_correct_content_and_recipients(): void
    {
        Mail::fake();

        $transaction = $this->createDummyTransaction();

        $mail = new InvoiceMail($transaction);

        Mail::send($mail);

        Mail::assertQueued(
            InvoiceMail::class,
            function (InvoiceMail $queuedMail) use ($transaction) {
                return $queuedMail->hasTo([$transaction->to->email, $transaction->from->email]) &&
                    $queuedMail->hasSubject('Invoice') &&
                    $queuedMail->transaction->id === $transaction->id;
            }
        );
    }
}
