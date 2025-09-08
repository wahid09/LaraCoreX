<?php
// app/Events/FeedbackUpdated.php
namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Feedback;

class FeedbackUpdated implements ShouldBroadcast
{
use Dispatchable, InteractsWithSockets, SerializesModels;

public $feedback;

public function __construct(Feedback $feedback)
{
$this->feedback = $feedback;
}

public function broadcastOn()
{
return new Channel('feedback');
}
}

// In controller after update
event(new FeedbackUpdated($feedback));