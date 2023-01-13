<?php

namespace Tests\Unit;

use App\Http\Controllers\DealsController;
use App\Http\Controllers\OrderController;
use App\Models\Order;
use App\Models\Pizza;
use PHPUnit\Framework\TestCase;

class OrdersTest extends TestCase
{
    public function test_createPizza() {
        $pizza = (new OrderController())->createPizza('Original', 'small', 9, ['Tomato Sauce', 'Cheese']);

        $expected = new Order();
        $expected->name = 'Original';
        $expected->size = 'small';
        $expected->price = 9;
        $expected->toppings = ['Tomato Sauce', 'Cheese'];

        $this->assertEquals($pizza, $expected);
    }

    public function test_createPizzaWithNoPrice() {
        $pizza = (new OrderController())->createPizza('Original', 'small', 0, ['Tomato Sauce', 'Cheese']);

        $expected = new Order();
        $expected->name = 'Original';
        $expected->size = 'small';
        $expected->price = 0;
        $expected->toppings = ['Tomato Sauce', 'Cheese'];

        $this->assertEquals($pizza, $expected);
    }

    public function test_createPizzaWithNoToppings() {
        $pizza = (new OrderController())->createPizza('Original', 'small', 9, null);

        $expected = new Order();
        $expected->name = 'Original';
        $expected->size = 'small';
        $expected->price = 9;
        $expected->toppings = null;

        $this->assertEquals($pizza, $expected);
    }

    public function test_smallCounters()
    {
        $order = [];
        $pizza = (new OrderController())->createPizza('Original', 'small', 0, null);
        $pizza2 = (new OrderController())->createPizza('Create Your Own', 'small', 0, null);

        array_push($order, $pizza);
        array_push($order, $pizza2);
        $smallCount = (new DealsController())->countSize($order, 'small');
        $smallNamedCount = (new DealsController())->countNamedSize($order, 'small');

        $this->assertEquals(1, $smallNamedCount);
        $this->assertEquals(2, $smallCount);
    }

    public function test_smallCountersWithNoSmall() {
        $order = [];
        $pizza = (new OrderController())->createPizza('Original', 'medium', 0, null);
        array_push($order, $pizza);
        $smallNamedCount = (new DealsController())->countNamedSize($order, 'small');
        $smallCount = (new DealsController())->countSize($order, 'small');

        $this->assertEquals(0, $smallCount);
        $this->assertEquals(0, $smallNamedCount);
    }

    public function test_mediumCounters() {
        $order = [];

        $pizza = (new OrderController())->createPizza('Original', 'medium', 0, null);
        $pizza2 = (new OrderController())->createPizza('Create Your Own', 'medium', 0, null);

        array_push($order, $pizza);
        array_push($order, $pizza2);
        $mediumCount = (new DealsController())->countSize($order, 'medium');
        $mediumNamedCount = (new DealsController())->countNamedSize($order, 'medium');

        $this->assertEquals(1, $mediumNamedCount);
        $this->assertEquals(2, $mediumCount);
    }

    public function test_mediumCountersWithNoMedium() {
        $order = [];
        $pizza = (new OrderController())->createPizza('Original', 'small', 0, null);
        array_push($order, $pizza);
        $mediumNamedCount = (new DealsController())->countNamedSize($order, 'medium');
        $mediumCount = (new DealsController())->countSize($order, 'medium');

        $this->assertEquals(0, $mediumCount);
        $this->assertEquals(0, $mediumNamedCount);
    }

    public function test_largeCounters() {
        $order = [];

        $pizza = (new OrderController())->createPizza('Original', 'large', 0, null);
        $pizza2 = (new OrderController())->createPizza('Create Your Own', 'large', 0, null);

        array_push($order, $pizza);
        array_push($order, $pizza2);
        $largeCount = (new DealsController())->countSize($order, 'large');
        $largeNamedCount = (new DealsController())->countNamedSize($order, 'large');

        $this->assertEquals(1, $largeNamedCount);
        $this->assertEquals(2, $largeCount);
    }

    public function test_largeCountersWithNoLarge() {
        $order = [];
        $pizza = (new OrderController())->createPizza('Original', 'small', 0, null);
        array_push($order, $pizza);
        $largeNamedCount = (new DealsController())->countNamedSize($order, 'large');
        $largeCount = (new DealsController())->countSize($order, 'large');

        $this->assertEquals(0, $largeCount);
        $this->assertEquals(0, $largeNamedCount);
    }

    public function test_calculatePrice() {
        $priceSmall = (new OrderController())->calculatePrice('Create Your Own', 'small', ['Tomato Sauce', 'Chicken'], null);
        $priceMedium = (new OrderController())->calculatePrice('Create Your Own', 'medium', ['Tomato Sauce', 'Chicken'], null);
        $priceLarge = (new OrderController())->calculatePrice('Create Your Own', 'large', ['Tomato Sauce', 'Chicken'], null);

        $expectedSmall = 8 + (0.9 * 2);
        $expectedMedium = 9 + (1 * 2);
        $expectedLarge = 11 + (1.15 * 2);

        $this->assertEquals($priceSmall, $expectedSmall);
        $this->assertEquals($priceMedium, $expectedMedium);
        $this->assertEquals($priceLarge, $expectedLarge);
    }

    public function test_calculatePriceNoToppings() {
        $priceSmall = (new OrderController())->calculatePrice('Create Your Own', 'small', null, null);
        $priceMedium = (new OrderController())->calculatePrice('Create Your Own', 'medium', null, null);
        $priceLarge = (new OrderController())->calculatePrice('Create Your Own', 'large', null, null);

        $expectedSmall = 8;
        $expectedMedium = 9;
        $expectedLarge = 11;

        $this->assertEquals($priceSmall, $expectedSmall);
        $this->assertEquals($priceMedium, $expectedMedium);
        $this->assertEquals($priceLarge, $expectedLarge);
    }

    public function test_calculatePriceAllToppings() {
        $toppings = ['Cheese', 'Tomato Sauce', 'Pepperoni', 'Ham', 'Chicken', 'Minced Beef', 'Onions', 'Green Peppers', 'Mushrooms', 'Sweetcorn', 'Jalapeno Peppers', 'Pineapple', 'Sausage', 'Bacon'];

        $priceSmall = (new OrderController())->calculatePrice('Create Your Own', 'small', $toppings, null);
        $priceMedium = (new OrderController())->calculatePrice('Create Your Own', 'medium', $toppings, null);
        $priceLarge = (new OrderController())->calculatePrice('Create Your Own', 'large', $toppings, null);

        $expectedSmall = 8 + (0.9 * count($toppings));
        $expectedMedium = 9 + (1 * count($toppings));
        $expectedLarge = 11 + (1.15 * count($toppings));

        $this->assertEquals($priceSmall, $expectedSmall);
        $this->assertEquals($priceMedium, $expectedMedium);
        $this->assertEquals($priceLarge, $expectedLarge);
    }

    public function test_dealsDetected() {
        $deal = ['Two for One Tuesdays', 'Three for Two Thursdays', 'Family Friday', 'Two Large', 'Two Medium', 'Two Small'];
        $delivery = 'Collection';
        $mediumCount = 2;
        $largeCount = 2;
        $mediumNamedCount = 2;
        $largeNamedCount = 2;
        $smallNamedCount = 2;
        $activeDeals = (new DealsController())->setDeals($deal, $delivery, $mediumCount, $largeCount, $mediumNamedCount, $largeNamedCount, $smallNamedCount);

        $expected = ['Two Large', 'Two Medium', 'Two Small'];

        $this->assertEquals($expected, $activeDeals);
    }

    public function test_dealsDetected2() {
        $deal = ['Two for One Tuesdays', 'Three for Two Thursdays', 'Family Friday', 'Two Large', 'Two Medium', 'Two Small'];
        $delivery = 'Delivery';
        $mediumCount = 2;
        $largeCount = 0;
        $mediumNamedCount = 2;
        $largeNamedCount = 2;
        $smallNamedCount = 2;
        $activeDeals = (new DealsController())->setDeals($deal, $delivery, $mediumCount, $largeCount, $mediumNamedCount, $largeNamedCount, $smallNamedCount);

        $expected = ['Two for One Tuesdays'];

        $this->assertEquals($expected, $activeDeals);
    }

    public function test_dealsDetected3() {
        $deal = ['Two for One Tuesdays', 'Three for Two Thursdays', 'Family Friday', 'Two Large', 'Two Medium', 'Two Small'];
        $delivery = 'Collection';
        $mediumCount = 4;
        $largeCount = 0;
        $mediumNamedCount = 4;
        $largeNamedCount = 0;
        $smallNamedCount = 0;
        $activeDeals = (new DealsController())->setDeals($deal, $delivery, $mediumCount, $largeCount, $mediumNamedCount, $largeNamedCount, $smallNamedCount);

        $expected = ['Family Friday'];

        $this->assertEquals($expected, $activeDeals);
    }

    public function test_dealsDetected4() {
        $deal = ['Two for One Tuesdays', 'Three for Two Thursdays', 'Family Friday', 'Two Large', 'Two Medium', 'Two Small'];
        $delivery = 'Delivery';
        $mediumCount = 3;
        $largeCount = 0;
        $mediumNamedCount = 3;
        $largeNamedCount = 0;
        $smallNamedCount = 0;
        $activeDeals = (new DealsController())->setDeals($deal, $delivery, $mediumCount, $largeCount, $mediumNamedCount, $largeNamedCount, $smallNamedCount);

        $expected = ['Three for Two Thursdays'];

        $this->assertEquals($expected, $activeDeals);
    }

    public function test_dealsDetected5() {
        $deal = ['Two for One Tuesdays', 'Three for Two Thursdays', 'Family Friday', 'Two Large', 'Two Medium', 'Two Small'];
        $delivery = 'Delivery';
        $mediumCount = 0;
        $largeCount = 0;
        $mediumNamedCount = 2;
        $largeNamedCount = 2;
        $smallNamedCount = 2;
        $activeDeals = (new DealsController())->setDeals($deal, $delivery, $mediumCount, $largeCount, $mediumNamedCount, $largeNamedCount, $smallNamedCount);

        $expected = [];

        $this->assertEquals($expected, $activeDeals);
    }

    public function test_dealsDetected6() {
        $deal = ['Two for One Tuesdays', 'Three for Two Thursdays', 'Family Friday', 'Two Large', 'Two Medium', 'Two Small'];
        $delivery = 'Delivery';
        $mediumCount = 4;
        $largeCount = 0;
        $mediumNamedCount = 4;
        $largeNamedCount = 0;
        $smallNamedCount = 0;
        $activeDeals = (new DealsController())->setDeals($deal, $delivery, $mediumCount, $largeCount, $mediumNamedCount, $largeNamedCount, $smallNamedCount);

        $expected = [];

        $this->assertEquals($expected, $activeDeals);
    }

    public function test_discountCalculation1() {
        $activeDeals = ['Two for One Tuesdays'];
        $orders = [];
        $mediumOrders = [];
        $mediumNamedOrders = [];
        $largeNamedOrders = [];
        $smallNamedOrders = [];

        $pizza = (new OrderController())->createPizza('Original', 'large', 11, null);
        $pizza2 = (new OrderController())->createPizza('Original', 'medium', 9, null);

        array_push($orders, $pizza);
        array_push($orders, $pizza2);

        $discountPrice = (new DealsController())->calculateDiscount($activeDeals, $orders, $mediumOrders, $mediumNamedOrders, $largeNamedOrders, $smallNamedOrders);

        $expected = 9;

        $this->assertEquals($expected, $discountPrice);
    }

    public function test_discountCalculation2() {
        $activeDeals = ['Three for Two Thursdays'];
        $orders = [];
        $mediumOrders = [];
        $mediumNamedOrders = [];
        $largeNamedOrders = [];
        $smallNamedOrders = [];

        $pizza = (new OrderController())->createPizza('Test Pizza', 'medium', 13, null);
        $pizza2 = (new OrderController())->createPizza('Test Pizza', 'medium', 12, null);
        $pizza3 = (new OrderController())->createPizza('Test Pizza', 'medium', 9, null);

        array_push($mediumOrders, $pizza);
        array_push($mediumOrders, $pizza2);
        array_push($mediumOrders, $pizza3);

        $discountPrice = (new DealsController())->calculateDiscount($activeDeals, $orders, $mediumOrders, $mediumNamedOrders, $largeNamedOrders, $smallNamedOrders);

        $expected = 9;

        $this->assertEquals($expected, $discountPrice);
    }

    public function test_discountCalculation3() {
        $activeDeals = ['Family Friday'];
        $orders = [];
        $mediumOrders = [];
        $mediumNamedOrders = [];
        $largeNamedOrders = [];
        $smallNamedOrders = [];

        $pizza = (new OrderController())->createPizza('Test Pizza', 'medium', 13, null);
        $pizza2 = (new OrderController())->createPizza('Test Pizza', 'medium', 12, null);
        $pizza3 = (new OrderController())->createPizza('Test Pizza', 'medium', 12, null);
        $pizza4 = (new OrderController())->createPizza('Test Pizza', 'medium', 9, null);

        array_push($mediumNamedOrders, $pizza);
        array_push($mediumNamedOrders, $pizza2);
        array_push($mediumNamedOrders, $pizza3);
        array_push($mediumNamedOrders, $pizza4);

        $discountPrice = (new DealsController())->calculateDiscount($activeDeals, $orders, $mediumOrders, $mediumNamedOrders, $largeNamedOrders, $smallNamedOrders);

        $expected = 16;

        $this->assertEquals($expected, $discountPrice);
    }

    public function test_discountCalculation4() {
        $activeDeals = ['Two Large'];
        $orders = [];
        $mediumOrders = [];
        $mediumNamedOrders = [];
        $largeNamedOrders = [];
        $smallNamedOrders = [];

        $pizza = (new OrderController())->createPizza('Test Pizza', 'large', 13, null);
        $pizza2 = (new OrderController())->createPizza('Test Pizza', 'large', 13, null);

        array_push($largeNamedOrders, $pizza);
        array_push($largeNamedOrders, $pizza2);

        $discountPrice = (new DealsController())->calculateDiscount($activeDeals, $orders, $mediumOrders, $mediumNamedOrders, $largeNamedOrders, $smallNamedOrders);

        $expected = 1;

        $this->assertEquals($expected, $discountPrice);
    }

    public function test_discountCalculation5() {
        $activeDeals = ['Two Medium'];
        $orders = [];
        $mediumOrders = [];
        $mediumNamedOrders = [];
        $largeNamedOrders = [];
        $smallNamedOrders = [];

        $pizza = (new OrderController())->createPizza('Test Pizza', 'medium', 13, null);
        $pizza2 = (new OrderController())->createPizza('Test Pizza', 'medium', 13, null);

        array_push($mediumNamedOrders, $pizza);
        array_push($mediumNamedOrders, $pizza2);

        $discountPrice = (new DealsController())->calculateDiscount($activeDeals, $orders, $mediumOrders, $mediumNamedOrders, $largeNamedOrders, $smallNamedOrders);

        $expected = 8;

        $this->assertEquals($expected, $discountPrice);
    }

    public function test_discountCalculation6() {
        $activeDeals = ['Two Small'];
        $orders = [];
        $mediumOrders = [];
        $mediumNamedOrders = [];
        $largeNamedOrders = [];
        $smallNamedOrders = [];

        $pizza = (new OrderController())->createPizza('Test Pizza', 'small', 13, null);
        $pizza2 = (new OrderController())->createPizza('Test Pizza', 'small', 13, null);

        array_push($smallNamedOrders, $pizza);
        array_push($smallNamedOrders, $pizza2);

        $discountPrice = (new DealsController())->calculateDiscount($activeDeals, $orders, $mediumOrders, $mediumNamedOrders, $largeNamedOrders, $smallNamedOrders);

        $expected = 14;

        $this->assertEquals($expected, $discountPrice);
    }

    public function test_discountCalculation7() {
        $activeDeals = ['Three for Two Thursdays'];
        $orders = [];
        $mediumOrders = [];
        $mediumNamedOrders = [];
        $largeNamedOrders = [];
        $smallNamedOrders = [];

        $pizza = (new OrderController())->createPizza('Test Pizza', 'medium', 13, null);
        $pizza2 = (new OrderController())->createPizza('Test Pizza', 'medium', 13, null);
        $pizza3 = (new OrderController())->createPizza('Test Pizza', 'medium', 13, null);

        array_push($mediumOrders, $pizza);
        array_push($mediumOrders, $pizza2);
        array_push($mediumOrders, $pizza3);

        $discountPrice = (new DealsController())->calculateDiscount($activeDeals, $orders, $mediumOrders, $mediumNamedOrders, $largeNamedOrders, $smallNamedOrders);

        $expected = 13;

        $this->assertEquals($expected, $discountPrice);
    }

    public function test_discountCalculation8() {
        $activeDeals = ['Two for One Tuesdays'];
        $orders = [];
        $mediumOrders = [];
        $mediumNamedOrders = [];
        $largeNamedOrders = [];
        $smallNamedOrders = [];

        $pizza = (new OrderController())->createPizza('Original', 'large', 11, null);
        $pizza2 = (new OrderController())->createPizza('Original', 'medium', 11, null);

        array_push($orders, $pizza);
        array_push($orders, $pizza2);

        $discountPrice = (new DealsController())->calculateDiscount($activeDeals, $orders, $mediumOrders, $mediumNamedOrders, $largeNamedOrders, $smallNamedOrders);

        $expected = 11;

        $this->assertEquals($expected, $discountPrice);
    }

    public function test_discountCalculation9() {
        $activeDeals = ['Two Small'];
        $orders = [];
        $mediumOrders = [];
        $mediumNamedOrders = [];
        $largeNamedOrders = [];
        $smallNamedOrders = [];

        $pizza = (new OrderController())->createPizza('Test Pizza', 'small', 5, null);
        $pizza2 = (new OrderController())->createPizza('Test Pizza', 'small', 5, null);

        array_push($smallNamedOrders, $pizza);
        array_push($smallNamedOrders, $pizza2);

        $discountPrice = (new DealsController())->calculateDiscount($activeDeals, $orders, $mediumOrders, $mediumNamedOrders, $largeNamedOrders, $smallNamedOrders);

        $expected = -2;

        $this->assertEquals($expected, $discountPrice);
    }

    public function test_discountCalculation10() {
        $activeDeals = ['Two Medium'];
        $orders = [];
        $mediumOrders = [];
        $mediumNamedOrders = [];
        $largeNamedOrders = [];
        $smallNamedOrders = [];

        $pizza = (new OrderController())->createPizza('Test Pizza', 'medium', 8, null);
        $pizza2 = (new OrderController())->createPizza('Test Pizza', 'medium', 8, null);

        array_push($mediumNamedOrders, $pizza);
        array_push($mediumNamedOrders, $pizza2);

        $discountPrice = (new DealsController())->calculateDiscount($activeDeals, $orders, $mediumOrders, $mediumNamedOrders, $largeNamedOrders, $smallNamedOrders);

        $expected = -2;

        $this->assertEquals($expected, $discountPrice);
    }

    public function test_discountCalculation11() {
        $activeDeals = ['Two Large'];
        $orders = [];
        $mediumOrders = [];
        $mediumNamedOrders = [];
        $largeNamedOrders = [];
        $smallNamedOrders = [];

        $pizza = (new OrderController())->createPizza('Test Pizza', 'large', 11, null);
        $pizza2 = (new OrderController())->createPizza('Test Pizza', 'large', 13, null);

        array_push($largeNamedOrders, $pizza);
        array_push($largeNamedOrders, $pizza2);

        $discountPrice = (new DealsController())->calculateDiscount($activeDeals, $orders, $mediumOrders, $mediumNamedOrders, $largeNamedOrders, $smallNamedOrders);

        $expected = -1;

        $this->assertEquals($expected, $discountPrice);
    }
}
