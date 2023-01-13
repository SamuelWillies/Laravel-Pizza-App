<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Pizza;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
    function addToOrder($name, $size, $toppings, $price) {
        $query = Pizza::where('name', $name);

        $toppings = $this->getToppings($name, $query, $toppings);
        $totalPrice = $this->updateTotalPrice($price);
        $pizza = $this->createPizza($name, $size, $price, $toppings);
        Session::push('order', $pizza);
        Session::put('totalPrice', $totalPrice);
        $dealsController = new DealsController();
        $dealsController->updateDeals();

        return redirect()->back();
    }

    function addToOrderInput(Request $request) {
        $name = $request->input('pizza');
        $size = $request->input('size');
        $toppings = $request->input('toppings');
        $query = Pizza::where('name', $name);
        $price = $this->calculatePrice($name, $size, $toppings, $query);

        $this->addToOrder($name, $size, $toppings, $price);

        return redirect()->back();
    }

    function createPizza($name, $size, $price, $toppings) {
        $pizza = new Order();
        $pizza->name = $name;
        $pizza->size = $size;
        $pizza->price = $price;
        $pizza->toppings = $toppings;

        return $pizza;
    }

    function getToppings($name, $query, $toppings) {
        if ($name != 'Create Your Own') {
            return $toppings = unserialize($query->value('toppings'));
        } else {
            return $toppings;
        }
    }

    function calculatePrice($name, $size, $toppings, $query) {
        $price = 0;
        $toppingCount = 0;

        if ($name != 'Create Your Own') {
            if ($size == 'small') {
                $price = $query->value('smallPrice');
            } elseif ($size == 'medium') {
                $price = $query->value('mediumPrice');
            } elseif ($size == 'large') {
                $price = $query->value('largePrice');
            }
        } else {
            if ($toppings != null) {
                $toppingCount = count($toppings);
            }
            if ($size == 'small') {
                $price = 8 + ($toppingCount * 0.90);
            } elseif ($size == 'medium') {
                $price = 9 + ($toppingCount);
            } elseif ($size == 'large') {
                $price = 11 + ($toppingCount * 1.15);
            }
        }
        return $price;
    }

    function updateTotalPrice($price) {
        if (Session::exists('totalPrice')) {
            $totalPrice = Session::get('totalPrice');
        } else {
            $totalPrice = 0;
        }

        $totalPrice += $price;

        return $totalPrice;
    }

    function selectDelivery(Request $request) {
        Session::put('delivery', $request->input('delivery'));

        $dealsController = new DealsController();
        $dealsController->updateDeals();

        return redirect()->back()->withInput($request->input());
    }

    function clearOrder() {
        Session::forget('order');
        Session::forget('totalPrice');
        Session::forget('totalPriceAfterDiscount');
        Session::forget('delivery');
        Session::forget('deals');

        $dealsController = new DealsController();
        $dealsController->updateDeals();

        return redirect()->back();
    }

    function submitOrder() {
        if (!Auth::check()) {
            return redirect('/')->with('message', 'Please login to use this feature.');
        } else {
            if (Session::has('order') && Session::has('delivery')) {
                return redirect()->back()->with('submitted', true);
            } else {
                return redirect()->back()->with('message', 'Please make an order and select a delivery method before you submit!');
            }
        }
    }
}
