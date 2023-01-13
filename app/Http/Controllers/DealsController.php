<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DealsController extends Controller
{
    function addDeals(Request $request) {
        Session::forget('deals');
        Session::forget('activeDeals');
        Session::forget('totalPriceAfterDiscount');
        $deal = $request->input('deal');

        if ($deal != null) {
            foreach ($deal as $item) {
                Session::push('deals', $item);
            }

            $this->calculateDeals($deal);
        }

        return redirect()->back()->withInput($request->input());
    }

    function updateDeals() {
        Session::forget('activeDeals');
        $deal = Session::get('deals');

        if ($deal != null) {
            $this->calculateDeals($deal);
        }

        return redirect()->back();
    }

    function calculateDeals($deal) {
        $orders = Session::get('order');
        $delivery = Session::get('delivery');

        if ($orders != null) {
            //$smallCount = $this->countSize($orders, 'small');
            $smallNamedCount = $this->countNamedSize($orders, 'small');
            //$smallOrders = $this->getSizeOrders($orders, 'small');
            $smallNamedOrders = $this->getNamedSizeOrders($orders, 'small');

            $mediumCount = $this->countSize($orders, 'medium');
            $mediumNamedCount = $this->countNamedSize($orders, 'medium');
            $mediumOrders = $this->getSizeOrders($orders, 'medium');
            $mediumNamedOrders = $this->getNamedSizeOrders($orders, 'medium');

            $largeCount = $this->countSize($orders, 'large');
            $largeNamedCount = $this->countNamedSize($orders, 'large');
            //$largeOrders = $this->getSizeOrders($orders, 'large');
            $largeNamedOrders = $this->getNamedSizeOrders($orders, 'large');
        } else {
            //$smallCount = 0;
            $mediumCount = 0;
            $largeCount = 0;
            $smallNamedCount = 0;
            $mediumNamedCount = 0;
            $largeNamedCount = 0;
            //$smallOrders = [];
            $mediumOrders = [];
            //$largeOrders = [];
            $smallNamedOrders = [];
            $mediumNamedOrders = [];
            $largeNamedOrders = [];
        }
        $totalPrice = Session::get('totalPrice');

        $activeDeals = $this->setDeals($deal, $delivery, $mediumCount, $largeCount, $mediumNamedCount, $largeNamedCount, $smallNamedCount);
        Session::put('activeDeals', $activeDeals);
        $discountPrice = $this->calculateDiscount($activeDeals, $orders, $mediumOrders, $mediumNamedOrders, $largeNamedOrders, $smallNamedOrders);

        Session::put('totalPriceAfterDiscount', ($totalPrice - $discountPrice));
    }

    function setDeals($deal, $delivery, $mediumCount, $largeCount, $mediumNamedCount, $largeNamedCount, $smallNamedCount) {
        $activeDeals = [];
        if ((in_array('Two for One Tuesdays', $deal)) && (($mediumCount + $largeCount) == 2)) {
            array_push($activeDeals, 'Two for One Tuesdays');
        }

        if ((in_array('Three for Two Thursdays', $deal)) && $mediumCount == 3) {
            array_push($activeDeals, 'Three for Two Thursdays');
        }

        if ((in_array('Family Friday', $deal)) && $mediumNamedCount == 4 && $delivery == 'Collection') {
            array_push($activeDeals, 'Family Friday');
        }

        if ((in_array('Two Large', $deal)) && $largeNamedCount == 2 && $delivery == 'Collection') {
            array_push($activeDeals, 'Two Large');
        }

        if ((in_array('Two Medium', $deal)) && $mediumNamedCount == 2 && $delivery == 'Collection') { //>=
            array_push($activeDeals, 'Two Medium');
        }

        if ((in_array('Two Small', $deal)) && $smallNamedCount == 2 && $delivery == 'Collection') {
            array_push($activeDeals, 'Two Small');
        }

        return $activeDeals;
    }

    function calculateDiscount($activeDeals, $orders, $mediumOrders, $mediumNamedOrders, $largeNamedOrders, $smallNamedOrders) {
        $discountPrice = 0;
        if (in_array('Two for One Tuesdays', $activeDeals)) {
            $mediumOrLargeOrders = [];

            for ($i = 0; count($mediumOrLargeOrders) < 2; $i++) {
                if ($orders[$i]->size == 'medium' or $orders[$i]->size == 'large') {
                    array_push($mediumOrLargeOrders, $orders[$i]);
                }
            }

            if ($mediumOrLargeOrders[0]['price'] > $mediumOrLargeOrders[1]['price']) {
                $discountPrice += $mediumOrLargeOrders[1]->price;
            } else {
                $discountPrice += $mediumOrLargeOrders[0]->price;
            }
        }

        if (in_array('Three for Two Thursdays', $activeDeals)) {
            $ordersForDeal = [];
            for ($i = 0; $i < 3; $i++) {
                array_push($ordersForDeal, $mediumOrders[$i]);
            }

            for ($j = 1; $j < 3; $j++) {
                $prevOrder = $ordersForDeal[$j];
                $k = $j - 1;

                while ($k >= 0 && $ordersForDeal[$k]->price > $prevOrder->price) {
                    $ordersForDeal[$k + 1] = $ordersForDeal[$k];
                    $k = $k - 1;
                }

                $ordersForDeal[$k + 1] = $prevOrder;
            }

            $discountPrice += $ordersForDeal[0]->price;
        }

        if (in_array('Family Friday', $activeDeals)) {
            for ($i = 0; $i < 4; $i++) {
                $discountPrice += ($mediumNamedOrders[$i]->price);
            }

            $discountPrice -= 30;
        }

        if (in_array('Two Large', $activeDeals)) {
            for ($i = 0; $i < 2; $i++) {
                $discountPrice += ($largeNamedOrders[$i]->price);
            }

            $discountPrice -= 25;
        }

        if (in_array('Two Medium', $activeDeals)) {
            for ($i = 0; $i < 2; $i++) {
                $discountPrice += ($mediumNamedOrders[$i]->price);
            }

            $discountPrice -= 18;
        }

        if (in_array('Two Small', $activeDeals)) {
            for ($i = 0; $i < 2; $i++) {
                $discountPrice += ($smallNamedOrders[$i]->price);
            }

            $discountPrice -= 12;
        }

        return $discountPrice;
    }


    function countSize($orders, $size) {
        $sizeCount = 0;

        for ($i = 0; $i < (count($orders)); $i++) {
            if ($orders[$i]->size == $size) {
                $sizeCount++;
            }
        }

        return $sizeCount;
    }

    function countNamedSize($orders, $size) {
        $sizeNamedCount = 0;
        for ($i = 0; $i < (count($orders)); $i++) {
            if ($orders[$i]->size == $size && $orders[$i]->name != 'Create Your Own') {
                $sizeNamedCount++;
            }
        }

        return $sizeNamedCount;
    }

    function getSizeOrders($orders, $size) {
        $sizeOrders = [];
        for ($i = 0; $i < (count($orders)); $i++) {
            if ($orders[$i]->size == $size) {
                array_push($sizeOrders, $orders[$i]);
            }
        }

        return $sizeOrders;
    }

    function getNamedSizeOrders($orders, $size) {
        $sizeOrders = [];
        for ($i = 0; $i < (count($orders)); $i++) {
            if ($orders[$i]->size == $size && $orders[$i]->name != 'Create Your Own') {
                array_push($sizeOrders, $orders[$i]);
            }
        }

        return $sizeOrders;
    }
}
