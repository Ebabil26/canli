<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Token;
use Davibennun\LaravelPushNotification\Facades\PushNotification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPushNotification extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $device;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Token $device)
    {
        //
        $this->device = $device;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        PushNotification::app($this->device->type)
            ->to($this->device->token)
            ->send('Объявление: ' . $this->device->message);

    }
}
