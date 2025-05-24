<?php

namespace Modules\Service\Repositories\WebService;

use Illuminate\Support\Facades\DB;
use Modules\Core\Traits\CoreTrait;
use Modules\Service\Entities\ServiceOrder;

class ServiceOrderRepository
{
    use CoreTrait;

    protected $order;

    public function __construct(ServiceOrder $order)
    {
        $this->order = $order;
    }

    public function getAllByUser($userId, $userColumn = 'user_id', $order = 'id', $sort = 'desc')
    {
        $orders = $this->order->with(['service', 'user'])->where($userColumn, $userId)->orderBy($order, $sort)->get();
        return $orders;
    }

    public function findById($id)
    {
        $order = $this->order->with(['service', 'user'])->find($id);
        return $order;
    }

    public function findByIdWithUserId($id)
    {
        $order = $this->order->where('user_id', auth()->id())->find($id);
        return $order;
    }

    public function create($request, $userToken = null)
    {
        DB::beginTransaction();

        try {

            $userId = auth('api')->check() ? auth('api')->id() : null;
            $data = [
                'user_id' => $userId,
                'service_id' => $request->service_id,
                'user_token' => auth('api')->guest() ? $request->user_token : null,
                'description' => $request['description'] ?? null,
                'contact_info' => $request['contact_info'] ?? null,
            ];

            $files = [];
            if (!empty($request->file('files'))) {
                foreach ($request->file('files') as $key => $file) {
                    $fileName = $this->uploadImage(public_path(config('core.config.service_orders_img_path')), $file);
                    $files[] = config('core.config.service_orders_img_path') . '/' . $fileName;
                }
            }

            $data['files'] = !empty($files) ? $files : null;
            $orderCreated = $this->order->create($data);

            DB::commit();
            return $orderCreated;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

}
