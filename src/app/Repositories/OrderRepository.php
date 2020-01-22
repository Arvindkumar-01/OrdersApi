<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Services\GoogleRoutes;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderRepository extends BaseRepository
{
    private $perPage = 10;
    private $currentPage = 1;

    const UNASSIGNED = 'UNASSIGNED';
    const TAKEN = 'TAKEN';

    public function entity(): string
    {
        return Order::class;
    }

    public function list($options = [])
    {
        if (isset($options['limit'])) {
            $this->perPage = $options['limit'];
        }
        if (isset($options['page'])) {
            $this->currentPage = $options['page'];
        }
        $offset = ($this->currentPage - 1) * $this->perPage;
        return $this->entity->offset($offset)
            ->limit($this->perPage)
            ->select(['id', 'distance', 'status'])
            ->get();
    }
    /**
     *  Store new orders
     */
    public function storeOrder($request_data)
    {
        $postdata['origin'] = json_encode($request_data['origin']);
        $postdata['destination'] = json_encode($request_data['destination']);
        $postdata['status'] = self::UNASSIGNED;
        // Calculate total distance from given latitude and longitude 
        $distance_data =  $this->getDistance($request_data);
        if ($distance_data['status'] == false) {
            return ['status' => false, 'data' => ['error' => $distance_data['error']]];
        }
        $postdata['distance'] =  $distance_data['total_distance'];
        $order = $this->entity->create($postdata);
        return [
            'status' => true,
            'data' => [
                'id' => $order->id,
                'distance' => $order->distance,
                'status' => $order->status
            ]
        ];
    }
    /**
     *  function for update order status
     */
    public function updateOrder($order_id, $data)
    {
        DB::beginTransaction();
        try {
            $order = $this->entity->lockForUpdate()->findorFail($order_id);
            if ($order->status == self::TAKEN) {
                return ['status' => false, 'error' => __('message.status_already_taken')];
            }
            if ($order->update($data)) {
                DB::commit();
                return ['status' => true, 'data' => ['status' => 'SUCCESS']];
            }
            DB::commit();
            return ['status' => false, 'error' => __('message.status_update_fail')];
        } catch (\Exception $ex) {
            DB::rollBack();
            \Log::error($ex->getMessage());
            return ['status' => false, 'error' => __('message.order_not_found')];
        }
    }

    private function getDistance(array $data)
    {
        $g_routes = new GoogleRoutes();
        return $g_routes->calculate_distance($data);
    }
}
