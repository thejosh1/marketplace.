<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Product;
use App\ProductReview;

class ProductInformationFetched
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    private $product;
    private $information;
    private $review;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Product $product, array $information, ProductReview $review)
    {
        $this->product = $product;
        $this->information = $information;
        $this->review = $review;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @return [mixed]
     */

    public function getInformation(): array
    {
        return $this->information;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
