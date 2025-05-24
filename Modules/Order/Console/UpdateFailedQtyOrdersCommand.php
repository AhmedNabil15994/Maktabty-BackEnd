<?php

namespace Modules\Order\Console;

use Illuminate\Console\Command;
use Modules\Order\Entities\OrderStatusesHistory;
use Modules\Order\Repositories\Dashboard\OrderRepository as Order;

class UpdateFailedQtyOrdersCommand extends Command
{
    protected $name = 'order:update';
    protected $description = 'Update Qty of products for failed orders';
    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
        parent::__construct();
    }

    public function handle()
    {
        if (config('setting.other.pending_orders_time.status') == 1) {
            $orders = $this->order->getOnlinePendingOrders(config('setting.other.pending_orders_time.minutes'));
            foreach ($orders as $k => $order) {

                $order->update([
                    'order_status_id' => '4', // failed
                    'payment_status_id' => '3', // failed
                    'increment_qty' => true,
                ]);

                // Add Order Status History
                OrderStatusesHistory::create([
                    'order_id' => $order->id,
                    'order_status_id' => '4', // failed
                    'user_id' => null,
                ]);

                if ($order->orderProducts) {
                    foreach ($order->orderProducts as $i => $orderProduct) {
                        if (!is_null($orderProduct->product->qty)) {
                            $orderProduct->product->increment('qty', $orderProduct->qty);
                        }
                    }
                }

                if ($order->orderVariations) {
                    foreach ($order->orderVariations as $i => $orderProduct) {
                        if (!is_null($orderProduct->variant->qty)) {
                            $orderProduct->variant->increment('qty', $orderProduct->qty);
                        }
                    }
                }

            }
            $this->info('Orders Updated Successfully.');
        }
    }

}
