<?php

namespace App\Http\Controllers;

use App\Models\Favourite;
use App\Models\FavouriteInfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class FavouritesController extends Controller
{
    function saveFavourite() {
        if (!Auth::check()) {
            return redirect('/')->with('message', 'Please login to use this feature.');
        }
        $order = Session::get('order');
        $deals = Session::get('deals');
        $delivery = Session::get('delivery');
        Favourite::where('userId', Auth::id())->delete();
        FavouriteInfo::where('userId', Auth::id())->delete();

        if ($order != null) {
            foreach ($order as $item) {
                if ($item->toppings != null) {
                    $toppings = serialize($item->toppings);
                } else {
                    $toppings = null;
                }

                Favourite::create([
                    'pizzaName' => $item->name,
                    'toppings' => $toppings,
                    'size' => $item->size,
                    'price' => $item->price,
                    'userId' => Auth::id(),
                ]);
            }
        } else {
            return redirect()->back()->with('message', 'There is no order to save!');
        }

        FavouriteInfo::create([
            'deals' => serialize($deals),
            'delivery' => $delivery,
            'userId' => Auth::id()
        ]);

        return redirect()->back()->with('message', 'Order saved.');
    }

    function getFavourite() {
        if (!Auth::check()) {
            return redirect('/')->with('message', 'Please login to use this feature.');
        }

        $favourites = Favourite::query()->where('userId', Auth::id())->get();

        if (count($favourites) == 0) {
            return redirect()->back()->with('message', 'There is no saved order for this user.');
        }

        Session::forget('order');
        Session::forget('totalPrice');

        $orderController = new OrderController();

        foreach ($favourites as $favourite) {
            $orderController->addToOrder($favourite->pizzaName, $favourite->size, unserialize($favourite->toppings), $favourite->price);
        }

        $favouriteInfo = FavouriteInfo::query()->where('userId', Auth::id())->get();

        Session::put('delivery', $favouriteInfo[0]->delivery);
        Session::put('deals', unserialize($favouriteInfo[0]->deals));

        $dealsController = new DealsController();
        $dealsController->updateDeals();

        return redirect()->back();
    }

    function removeFavourite() {
        if (!Auth::check()) {
            return redirect('/')->with('message', 'Please login to use this feature.');
        }
        Favourite::where('userId', Auth::id())->delete();
        FavouriteInfo::where('userId', Auth::id())->delete();

        return redirect()->back();
    }
}
