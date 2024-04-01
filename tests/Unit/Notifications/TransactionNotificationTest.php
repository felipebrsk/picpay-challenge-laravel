<?php

namespace Tests\Unit\Notifications;

use Tests\TestCase;
use Tests\Traits\HasDummyUser;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TransactionNotification;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Support\Facades\Queue;

class TransactionNotificationTest extends TestCase
{
    use HasDummyUser;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private $user;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createDummyUserCustomer();
    }

    /**
     * Test if the notification can be queued.
     *
     * @return void
     */
    public function test_if_the_notification_can_be_queued(): void
    {
        Queue::fake();

        $notification = [
            'title' => fake()->title(),
            'data' => fake()->text(),
            'icon' => fake()->name(),
            'actionUrl' => fake()->url(),
        ];

        $this->user->notify(new TransactionNotification($notification));

        Queue::assertPushed(SendQueuedNotifications::class, function (SendQueuedNotifications $job) {
            return $job->notification::class === TransactionNotification::class;
        });
    }

    /**
     * Test if can send the notification.
     *
     * @return void
     */
    public function test_if_can_send_the_notification(): void
    {
        Notification::fake();

        $notification = [
            'title' => fake()->title(),
            'data' => fake()->text(),
            'icon' => fake()->name(),
            'actionUrl' => fake()->url(),
        ];

        $this->user->notify(new TransactionNotification($notification));

        Notification::assertSentTo($this->user, TransactionNotification::class, function (TransactionNotification $transactionNotification, array $channels) use ($notification) {
            $this->assertContains('database', $channels);

            $databaseNotification = (object)$transactionNotification->toArray($this->user);

            $this->assertEquals($notification['title'], $databaseNotification->title);
            $this->assertEquals($notification['actionUrl'], $databaseNotification->actionUrl);

            return true;
        });
    }

    /**
     * Test if can save the notification in database.
     *
     * @return void
     */
    public function test_if_can_save_the_notification_in_database(): void
    {
        $this->assertDatabaseCount('notifications', 0);

        $notification = [
            'title' => fake()->title(),
            'data' => fake()->text(),
            'icon' => fake()->name(),
            'actionUrl' => fake()->url(),
        ];

        $this->user->notify(new TransactionNotification($notification));

        $this->assertDatabaseCount('notifications', 1)->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->user->id,
            'notifiable_type' => $this->user::class,
            'data' => json_encode($notification),
        ]);
    }
}
