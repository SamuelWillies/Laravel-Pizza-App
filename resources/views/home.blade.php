<?php
use Illuminate\Support\Facades\Session;

if (Session::exists('totalPrice')) {
    $totalPrice = Session::get('totalPrice');
} else {
    $totalPrice = 0;
}

$toppings = ['Cheese', 'Tomato Sauce', 'Pepperoni', 'Ham', 'Chicken', 'Minced Beef', 'Onions', 'Green Peppers', 'Mushrooms', 'Sweetcorn', 'Jalapeno Peppers', 'Pineapple', 'Sausage', 'Bacon']
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>Pizza</title>
</head>
<body>
    <h1>Pizza</h1>
    @guest
        <a href="/register">Register</a>
    <form method="post" action="/login">
        @csrf
        <fieldset>
            <legend>Login</legend>

            <div>
                <label for="username">Username:</label>
                <input type="text" required name="name" id="username" value="{{ old('name') }}">
            </div>

            <div>
                <label for="password">Password:</label>
                <input type="password" required name="password" id="password">
            </div>

            <div>
                <button type="submit" name="submitLogin" formnovalidate>Login</button>
            </div>

        </fieldset>
    </form>
    @endguest

    @auth
        <p>Logged in {{ Auth::user()->name }}</p>
        <a href="/logout">Log out</a>
    @endauth

    <form method="post" action="/addPizza">
        @csrf
        <fieldset>
            <legend>Select Your Pizza</legend>
            <select name="pizza" id="pizza">
                {{ $pizzas = DB::table('pizzas')->get() }}
                @foreach ($pizzas as $pizza)
                    <option value="{{ $pizza->name }}">{{ $pizza->name }}</option>
                @endforeach
                <option value="Create Your Own">Create Your Own</option>
            </select>

            <br>

            <select name="size" id="size">
                <option value="small">Small</option>
                <option value="medium">Medium</option>
                <option value="large">Large</option>
            </select>
        </fieldset>

        <fieldset>
            <legend>Toppings</legend>
            @foreach ($toppings as $topping)
                <input type="checkbox" id="{{$topping}}" name="toppings[]" value="{{$topping}}">
                <label for="{{$topping}}">{{$topping}}</label>
            @endforeach
        </fieldset>

        <div>
            <button type="submit" name="submitPizza" formnovalidate>Add to Order</button>
        </div>
    </form>

    <form method="post" action="/addDeals">
        @csrf
        <fieldset>
            <legend>Deals</legend>
            <input type="checkbox" id="twoforonetuesdays" name="deal[]" value="Two for One Tuesdays" {{ (is_array(Session::get('deals')) && in_array('Two for One Tuesdays', Session::get('deals'))) ? ' checked' : ''}}>
            <label for="twoforonetuesdays">Two for One Tuesdays</label>
            <input type="checkbox" id="threefortwothursdays" name="deal[]" value="Three for Two Thursdays" {{ (is_array(Session::get('deals')) && in_array('Three for Two Thursdays', Session::get('deals'))) ? ' checked' : ''}}>
            <label for="threefortwothursdays">Three for Two Thursdays</label>
            <input type="checkbox" id="familyfriday" name="deal[]" value="Family Friday" {{ (is_array(Session::get('deals')) && in_array('Family Friday', Session::get('deals'))) ? ' checked' : ''}}>
            <label for="familyfriday">Family Friday</label>
            <input type="checkbox" id="twolarge" name="deal[]" value="Two Large" {{ (is_array(Session::get('deals')) && in_array('Two Large', Session::get('deals'))) ? ' checked' : ''}}>
            <label for="twolarge">Two Large</label>
            <input type="checkbox" id="twomedium" name="deal[]" value="Two Medium" {{ (is_array(Session::get('deals')) && in_array('Two Medium', Session::get('deals'))) ? ' checked' : ''}}>
            <label for="twomedium">Two Medium</label>
            <input type="checkbox" id="twosmall" name="deal[]" value="Two Small" {{ (is_array(Session::get('deals')) && in_array('Two Small', Session::get('deals'))) ? ' checked' : ''}}>
            <label for="twosmall">Two Small</label>
        </fieldset>

        <div>
            <button type="submit" name="submitDeal" formnovalidate>Select Deals</button>
        </div>
    </form>

    <form method="post" action="/selectDelivery">
        @csrf
        <fieldset>
            <legend>Delivery Type</legend>
            <input type="radio" id="delivery" name="delivery" value="Delivery" {{ (Session::get('delivery') == 'Delivery') ? 'checked' : ''}}>
            <label for="delivery">Delivery</label>
            <input type="radio" id="collection" name="delivery" value="Collection" {{ (Session::get('delivery') == 'Collection') ? 'checked' : ''}}>
            <label for="collection">Collection</label><br>
        </fieldset>

        <div>
            <button type="submit" name="submitDelivery" formnovalidate>Select Delivery Option</button>
        </div>
    </form>

    <fieldset>
        <legend>Your Order</legend>
        @if (Session::has('order'))
            @foreach (Session::get('order') as $order)
                {{ $order->name }} - {{ ucfirst($order->size) }} - £{{ sprintf('%0.2f', $order->price) }}<br>
                @if ($order->toppings != null)
                    @foreach ($order->toppings as $topping)
                        <ul>{{ ucfirst($order->size) }} {{ $topping }}</ul>
                    @endforeach
                @endif
            @endforeach
            <p>Total Price: £{{ sprintf('%0.2f', $totalPrice) }}</p>
            @if (Session::exists('totalPriceAfterDiscount'))
                <p>Total Price After Discount: £{{ sprintf('%0.2f', Session::get('totalPriceAfterDiscount')) }}</p>
            @endif
            @if (Session::has('delivery'))
                <p>You have selected: {{ Session::get('delivery') }}</p>
            @else
                <p>Please select a delivery option!</p>
            @endif
        @endif
    </fieldset>

    <form action="/clearOrder">
        <button type="submit" name="clearOrder">Clear Order</button>
    </form>

    <form action="/saveFavourite">
        <button type="submit" name="saveFavourite">Save Order</button>
    </form>

    <form action="/getFavourite">
        <button type="submit" name="getFavourite">Get Saved Order</button>
    </form>

    <form action="/removeFavourite">
        <button type="submit" name="removeFavourite">Remove Saved Order</button>
    </form>

    <form action="/submitOrder">
        <button type="submit" name="submitOrder">Submit Order</button>
    </form>

    <p>{{ Session::get('message') }}</p>

    <h1>Active Deals</h1>
    @if (Session::has('activeDeals'))
        @foreach (Session::get('activeDeals') as $deal)
            <p>{{ $deal }}</p>
        @endforeach
    @endif

    <h1>Chosen Deals</h1>
    @if (Session::has('deals'))
        @foreach (Session::get('deals') as $deal)
            <p>{{ $deal }}</p>
        @endforeach
    @endif

    @if (Session::get('submitted') == true)
        <h1>Submitted Deal</h1>
        @foreach (Session::get('order') as $order)
            {{ $order->name }} - {{ ucfirst($order->size) }} - £{{ $order->price }}<br>
            @if ($order->toppings != null)
                @foreach ($order->toppings as $topping)
                    <ul>{{ ucfirst($order->size) }} {{ $topping }}</ul>
                @endforeach
            @endif
        @endforeach
        <p>Final Price: £{{ Session::get('totalPrice') }}</p>
        @if (Session::has('totalPriceAfterDiscount'))
        <p>Final Price After Discount: £{{ Session::get('totalPriceAfterDiscount') }}</p>
        @endif
        <p>Order is for: {{ Session::get('delivery') }}
    @endif

</body>
</html>
