<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AggregatedCharacterOrder;
use App\Models\AggregatedProfit;
use App\Models\AggregatedStockedItem;
use App\Models\CachedTransaction;
use App\Models\Character;
use App\Models\DeliveredItem;
use App\Models\Delivery;
use App\Models\Manual\Transaction;
use App\Models\SDE\Inventory\Type;
use App\Services;
use Illuminate\Http\Request;

class TradingController extends Controller
{

    private $_sdeRepository;

    private $_tradingRepository;

    private $_locationKeeper;

    public function __construct(Services\Locations\Keeper $locationKeeper) {
        $this->_sdeRepository = new Services\SdeRepository;
        $this->_tradingRepository = new Services\TradingRepository;

        $this->_locationKeeper = $locationKeeper;
    }

    public function createManualPurchase(Request $request) {
        $request->validate([
            'type_id' => 'required|integer|exists:App\Models\SDE\Inventory\Type,typeID',
            'location_id' => 'required|integer',
            'price' => 'required|integer|min:1',
            'quantity' => 'required|integer|min:1',
        ]);

        $transaction = new Transaction;
        $transaction->is_buy = true;
        $transaction->location_id = $request->location_id;
        $transaction->quantity = $request->quantity;
        $transaction->type_id = $request->type_id;
        $transaction->unit_price = $request->price;

        $transaction->save();

        return ['status' => 'success'];
    }

    public function getProfits(Request $request) {
        $request->validate([
           'per_page' => 'required|integer|min:10|max:100',
           'page' => 'required|integer|min:1',
        ]);

        $perPage = $request->per_page;
        $page = $request->page;

        $records = AggregatedProfit::with(['type', 'sellTransaction', 'buyTransaction'])
                                   ->orderBy('date', 'desc')
                                   ->offset(($page - 1) * $perPage)
                                   ->limit($perPage)
                                   ->get();

        return [
            'items' => $records,
            'total' => AggregatedProfit::count(),
        ];
    }

    public function getDailyProfitStatistics(Request $request) {
        $request->validate([
           'days' => 'required|integer|min:1',
        ]);

        $endDate = now()->modify('tomorrow midnight')->modify('-1 second')->format('Y-m-d');
        $startDate = now()->modify('tomorrow midnight')->modify("-{$request->days} days")->format('Y-m-d');

        $data = AggregatedProfit::selectRaw("SUM(profit) as profit, DATE_FORMAT(date, '%Y-%m-%d') as day")
                               ->whereRaw("DATE_FORMAT(date, '%Y-%m-%d') BETWEEN '{$startDate}' AND '{$endDate}'")
                               ->groupBy('day')
                               ->get();

        return $data->map(function($item) {
            return [
                'day' => $item->day,
                'profit' => (int)$item->profit,
            ];
        });
    }

    public function getOrders(Request $request) {
        $request->validate([
            'character_id' => 'required|integer',
            'location_id' => 'required|integer',
        ]);

        $location = $this->_locationKeeper->getById($request->location_id);
        return AggregatedCharacterOrder::selectRaw('type_id, MIN(price) as price, SUM(volume_remain) as volume_remain, SUM(volume_total) as volume_total, MIN(IFNULL(outbid, 0)) as outbid')
            ->where('character_id', $request->character_id)
            ->where('location_id', $request->location_id)
            ->groupBy('type_id')
            ->get()
            ->map(function ($order) use ($location) {
                return $this->_convertOrderToApi($order, $location);
            })
            ->values();
    }

    public function getProfitableItems(Request $request) {
        $request->validate([
            'location_id' => 'required|integer',
        ]);

        $location = $this->_locationKeeper->getById($request->location_id);

        return $this->_tradingRepository
            ->getProfitableMarketItems($location)
            ->map(function ($type) use ($location) {
                return $this->_convertTypeToApi($type, $location);
            })
            ->values();
    }

    public function searchModules(Request $request) {
        $request->validate([
            'location_id' => 'required|integer',
            'search_query' => 'required|string|min:4',
        ]);

        $location = $this->_locationKeeper->getById($request->location_id);

        return $this->_sdeRepository
            ->searchTypes($request->search_query)
            ->map(function ($type) use ($location) {
                return $this->_convertTypeToApi($type, $location);
            })
            ->values();
    }

    public function getFavorites(Request $request) {
        $request->validate([
            'location_id' => 'required|integer',
        ]);

        $location = $this->_locationKeeper->getById($request->location_id);
        $favorites = $this->_tradingRepository->getFavorites();
        $types = $this->_sdeRepository->getTypesByIds($favorites->map->type_id->toArray());

        return $types
            ->sortBy('typeName')
            ->map(function ($type) use ($location) {
                return $this->_convertTypeToApi($type, $location);
            })
            ->values();
    }

    public function addFavorite(Request $request) {
        $request->validate([
            'location_id' => 'required|integer',
            'type_id'     => 'required|integer|exists:App\Models\SDE\Inventory\Type,typeID',
        ]);

        $location = $this->_locationKeeper->getById($request->location_id);
        $type = $this->_sdeRepository->getTypeById($request->type_id);

        try {
            $this->_tradingRepository->addFavorite($request->type_id);
        } catch (\Exception $e) {
            return response('Already exists', 400);
        }

        return $this->_convertTypeToApi($type, $location);
    }

    public function deleteFavorite(Request $request) {
        $request->validate([
            'type_id' => 'required|integer',
        ]);

        $this->_tradingRepository->deleteFavorite($request->type_id);

        return ['status' => 'success'];
    }

    public function openMarketDetails(Request $request) {
        $request->validate([
            'character_id' => 'required|integer',
            'type_id' => 'required|integer',
        ]);

        $character = Character::find($request->character_id);
        $esi = new Services\ESI($character);
        $esi->openMarketDetailsWindow($request->type_id);

        return ['status' => 'success'];
    }

    public function getMoneyFlowStatistics(Request $request) {
        $builder = $request->type_id
            ? CachedTransaction::selectRaw("date_format(date - interval minute(date)%60 minute, '%H:%i') as x, sum(quantity) as y")->where('type_id', $request->type_id)
            : CachedTransaction::selectRaw("date_format(date - interval minute(date)%60 minute, '%H:%i') as x, sum(unit_price * quantity) as y");

        $builder->where('is_buy', 0);

        $data = $builder->groupBy('x')->get()->mapWithKeys(function ($val) {
           $val['x'] = (new \DateTime($val['x']))->modify('+3 hours')->format('H:i');
           return [$val['x'] => $val];
        });

        for ($time = new \DateTime('00:00'); $time < (new \DateTime('00:00'))->modify('+1 day'); $time->modify('+60 minutes')) {
            $timeString = $time->format('H:i');
            $data[$timeString] = $data[$timeString] ?? ['x' => $timeString, 'y' => 0];
        }

        return $data->sortBy('x')->values();
    }

    public function saveDeliveredItems(Request $request) {
        $request->validate([
            'location_id' => 'required|integer',
            'items'       => 'required|array',
        ]);

        $delivery = Delivery::create([
            'destination_id' => $request->location_id,
        ]);

        $deliveredItems = collect();
        foreach ($request->items as $item) {
            $type = Type::where('typeName', $item['name'])->first();
            if (!$type) {
                continue;
            }

            $deliveredItems->push(new DeliveredItem([
                'delivery_id' => $delivery->id,
                'type_id' => $type->typeID,
                'quantity' => $item['quantity'],
                'volume' => $item['volume'],
            ]));
        }

        $deliveredItems->each->save();

        return ['status' => 'success'];
    }

    public function finishDelivery(Request $request) {
        $request->validate([
            'location_id' => 'required|integer',
            'delivery_id' => 'required|integer',
        ]);

        Delivery::where('id', $request->delivery_id)
            ->whereNull('finished_at')
            ->update([
                'finished_at' => now()->format('Y-m-d H:i:s'),
            ]);

        return ['status' => 'success'];
    }

    public function getDeliveredItems(Request $request) {
        $request->validate([
            'location_id' => 'required|integer',
        ]);

        $deliveries = Delivery::where('destination_id', $request->location_id)
                              ->whereNull('finished_at')
                              ->get();

        $apiDeliveries = [];
        foreach ($deliveries as $delivery) {
            $items = DeliveredItem::selectRaw('type_id, SUM(quantity) as quantity, SUM(volume) as volume')
                                  ->with('type')
                                  ->where('delivery_id', $delivery->id)
                                  ->groupBy('type_id')
                                  ->get();

            $apiItems = $items->map(function (DeliveredItem $item) {
                return $this->_convertDeliveredItemToApi($item);
            });

            $apiDeliveries[] = [
                'id'    => $delivery->id,
                'items' => $apiItems,
            ];
        }

        return $apiDeliveries;
    }

    public function getUnlistedItems(Request $request) {
        $request->validate([
            'location_id' => 'required|integer',
        ]);

        $location = $this->_locationKeeper->getById($request->location_id);
        $unlistedTypes = AggregatedStockedItem::where('location_id', $request->location_id)
            ->with('type')
            ->where('in_hangar', '>', 0)
            ->where('in_market', 0)
            ->get()
            ->map->type;

        return $unlistedTypes->map(function (Type $type) use ($location) {
            return $this->_convertTypeToApi($type, $location);
        });
    }

    public function getItemHistory(Request $request) {
        $request->validate([
            'location_id' => 'required|integer',
            'type_id' => 'required|integer',
        ]);

        $location = $this->_locationKeeper->getById($request->location_id);
        $unprocessedBuyTransactions = CachedTransaction::with(['type'])
                                                       ->buy()
                                                       ->unprocessed()
                                                       ->where('type_id', $request->type_id)
                                                       ->get();

        $unprocessedManualBuyTransactions = Transaction::with(['type'])
                                                       ->buy()
                                                       ->unprocessed()
                                                       ->where('type_id', $request->type_id)
                                                       ->get();

        return $unprocessedBuyTransactions
            ->merge($unprocessedManualBuyTransactions)
            ->sortBy(fn($transaction) => new \DateTime($transaction->date))
            ->map(fn($transaction) => [
                'type_id'        => $transaction->type->id,
                'icon'           => $transaction->type->icon,
                'name'           => $transaction->type->name,
                'buy'            => $transaction->unit_price,
                'quantity'       => $transaction->quantityToProcess,
                'date'           => $transaction->date,
                'margin'         => $transaction->type->getMargin($location, $transaction->unit_price),
                'margin_percent' => $transaction->type->getMarginPercent($location, $transaction->unit_price),
            ]);
    }

    private function _convertDeliveredItemToApi(DeliveredItem $item) {
        return [
            'type_id'  => $item->type->typeID,
            'name'     => $item->type->typeName,
            'icon'     => $item->type->icon,
            'quantity' => $item->quantity,
            'volume'   => (float)$item->volume,
        ];
    }

    private function _convertTypeToApi(Type $type, Services\Locations\Location $location) {
        return [
            'type_id'     => $type->typeID,
            'name'        => $type->typeName,
            'icon'        => $type->icon,
            'volume'      => $type->volume, // TODO: use volume for ships from invVolumes
            'prices'      => [
                'buy'                    => $type->getBuyPrice(),
                'sell'                   => $type->getSellPrice($location),
                'total_cost'             => $type->getTotalCost($location),
                'margin'                 => $type->getMargin($location),
                'margin_percent'         => $type->getMarginPercent($location),
                'monthly_volume'         => $type->getMonthlyVolume($location),
                'weekly_volume'          => $type->getWeeklyVolume($location),
                'average_daily_volume'   => $type->getAverageDailyVolume($location),
                'potential_daily_profit' => $type->getPotentialDailyProfit($location),
            ],
            'in_stock'    => $type->getStockedQuantity($location, 2117638152),
            'in_delivery' => $type->getDeliveredQuantity($location),
        ];
    }

    private function _convertOrderToApi($order, Services\Locations\Location $location) {
        return [
          'order_id'              => $order->order_id,
          'price'                 => $order->price,
          'volume_remain'         => $order->volume_remain,
          'volume_total'          => $order->volume_total,
          'type'                  => $this->_convertTypeToApi($order->type, $location),
          'outbid_margin'         => $order->outbid ? -$order->outbid : null,
          'outbid_margin_percent' => $order->outbid ? -round($order->outbid / $order->price * 100, 2) : null,
        ];
    }
}
