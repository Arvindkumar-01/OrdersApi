<?php

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Order API",
 *      
 *      @OA\Contact(
 *          email="arvind.gupta01@nagarro.com"
 *      )    
 * )
 */
/**
 *  
 *  @OA\Server(
 *      url="/",
 *      description="L5 Swagger OpenApi Server"
 * )
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\ListOrder;
use App\Http\Requests\StoreOrder;
use App\Http\Requests\UpdateOrder;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use App\Repositories\OrderRepository;
use Exception;

class OrderController extends Controller
{
    private $order_repo;

    public function __construct(OrderRepository $orderRepo)
    {
        $this->order_repo = $orderRepo;
    }

    /**
     * @OA\Get(
     *      path="/orders",
     *      operationId="getOrdersList",
     *      tags={"Orders"},
     *      summary="Get list of Orders",
     *      description="Returns list of orders",
     *      @OA\Parameter(
     *          name="limit",         
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="page",     *         
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="[{'id':1,'distance':10,'status':'UNASSIGNED'},{'id':2,'distance':10,'status':'UNASSIGNED'}]"
     *       ), 
     *       @OA\Response(
     *          response=422,
     *          description="{'error':'The limit must be an integer.'}"
     *       ),             
     *     )
     *
     * Returns list of orders
     */
    public function list(ListOrder $request)
    {
        try {
            $orders = $this->order_repo->list($request->all());
            return response()->json($orders, Response::HTTP_OK);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
    /**
     * @OA\Post(
     *      path="/orders",
     *      operationId="StoreOrder",
     *      tags={"Orders"},
     *      summary="Place Order",
     *      description="Create a new Order",     
     *      @OA\RequestBody(
     *         required=true,
     *         description="Post object",
     *         @OA\MediaType(
     *         mediaType="application/json",
     *          @OA\Schema(
     *                 @OA\Property(
     *                     property="origin",
     *                     type="array",
     *                     @OA\Items(
     *                          type="string"
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="destination",
     *                     type="array",
     *                      @OA\Items(
     *                          type="string"
     *                     )
     *                 ),
     *                  example={"origin": {"40.6655101", "-73.89188969999998"}, "destination": {"40.6905615", "-73.9976592"}}
     *             )
     *          )
     *       ),     
     *      @OA\Response(
     *          response=200,
     *          description="{'id':17,'distance':10434,'status':'UNASSIGNED'}"
     *       ), 
     *       @OA\Response(
     *          response=422,
     *          description="{'error':'Error message'}"
     *       ),             
     *     )
     *
     * Create new order
     */
    public function store(StoreOrder $request)
    {
        try {
            $response = $this->order_repo->storeOrder($request->all());
            if ($response['status']) {
                return response()->json($response['data'], Response::HTTP_OK);
            } else {
                return response()->json($response['data'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @OA\Patch(
     *      path="/orders/{id}",
     *      operationId="UpdateOrder",
     *      tags={"Orders"},
     *      summary="Take Order",
     *      description="Take order",
     *       @OA\Parameter(
     *              name="id",
     *              description="OrderId",
     *              required=true,
     *              in="path",
     *              @OA\Schema(
     *                  type="integer"
     *              )
     *      ),
     *     @OA\RequestBody(
     *             @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="status",
     *                     type="string",
     *                 ),
     *                 example={"status": "TAKEN"}
     *             )
     *           )
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="{""status"":""SUCCESS""}"
     *       ),
     *       @OA\Response(
     *              response=422, 
     *              description="{'error':'Order status already TAKEN'}"
     *          )     *       
     *     )
     *
     */
    public function update(UpdateOrder $request, $id)
    {
        $response = $this->order_repo->updateOrder($id, $request->only(['status']));
        if ($response['status'] == true) {
            return response()->json($response['data'], Response::HTTP_OK);
        } else {
            return response()->json(['error' => $response['error']], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
