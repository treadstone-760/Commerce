<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateCustomerSegment implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderPaid $event): void
    {
        Log::info(["event123" => $event->order]);

        $order = Order::where('id', $event->order->id)->first();

        $user = $order->user_id;
        $getUser = User::where('id', $user)->first();
        $total_amount_spent_by_customer = Order::where('user_id', $user->id)->sum('total_amount');

        if($total_amount_spent_by_customer > 10000){
            $segment = "platinum";
        }elseif($total_amount_spent_by_customer > 5000){
            $segment = "gold";
        }elseif($total_amount_spent_by_customer > 1000){
            $segment = "silver";
            }else{
                $segment = "bronze";
            }

            $getUser->segment = $segment;
            $getUser->save();
    }
}
