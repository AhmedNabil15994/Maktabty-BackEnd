<?php

namespace Modules\Service\Repositories\Dashboard;

use Illuminate\Support\Facades\DB;
use Modules\Service\Entities\ServiceOrder;

class ServiceOrderRepository
{
    protected $order;

    public function __construct(ServiceOrder $order)
    {
        $this->order = $order;
    }

    public function getAll($order = 'id', $sort = 'desc')
    {
        $orders = $this->order->orderBy($order, $sort)->get();
        return $orders;
    }

    public function findById($id)
    {
        $order = $this->order->with(['service'])->withDeleted()->find($id);
        return $order;
    }

    public function updateUnread($id)
    {
        $order = $this->findById($id);
        if (!$order) {
            abort(404);
        }

        $order->update([
            'unread' => true,
        ]);
    }

    public function restoreSoftDelte($model)
    {
        $model->restore();
    }

    public function delete($id)
    {
        DB::beginTransaction();

        try {
            $model = $this->findById($id);

            if ($model) {
                if ($model->trashed()) {
                    // Delete order files
                    $model->forceDelete();
                } else {
                    $model->delete();
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function deleteSelected($request)
    {
        DB::beginTransaction();

        try {

            foreach ($request['ids'] as $id) {
                $model = $this->delete($id);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function queryTable($request)
    {
        $query = $this->order->with(['service', 'user']);

        if ($request->input('search.value')) {
            $term = strtolower($request->input('search.value'));
            $query = $query->where(function ($query) use ($term) {
                $query->where('id', 'like', '%' . $term . '%');
                $query->orWhere(function ($query) use ($term) {
                    $query->whereHas('service', function ($query) use ($term) {
                        $query->whereRaw('lower(title) like (?)', ["%{$term}%"]);
                    });
                });
            });
        }

        return $this->filterDataTable($query, $request);
    }

    public function filterDataTable($query, $request)
    {
        if (isset($request['req']['from']) && $request['req']['from'] != '') {
            $query->whereDate('created_at', '>=', $request['req']['from']);
        }

        if (isset($request['req']['to']) && $request['req']['to'] != '') {
            $query->whereDate('created_at', '<=', $request['req']['to']);
        }

        if (isset($request['req']['user_id']) && !empty($request['req']['user_id'])) {
            $query->where('user_id', $request['req']['user_id']);
        }

        if (isset($request['req']['service_id']) && !empty($request['req']['service_id'])) {
            $query->where('service_id', $request['req']['service_id']);
        }

        return $query;
    }
}
