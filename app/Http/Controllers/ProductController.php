<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cart;
use App\Product;
use App\Order;
use Session;
use Auth;
use Stripe\Charge;
use Stripe\Stripe;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function getIndex(){

        $products = Product::all();
        return view('shop.index', ['products' => $products]);
    }

    public function getAddToCart(Request $request, $id)
    {
        $product = Product::find($id);
        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Cart($oldCart);
        $cart->add($product, $product->id);

        Session::put('cart', $cart);
        return redirect()->route('product.index');
    }

    public function getReduceByOne($id) {
        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Cart($oldCart);
        $cart->reduceByOne($id);

        if (count($cart->items) > 0) {
            Session::put('cart', $cart);
        } else {
            Session::forget('cart');
        }
        return redirect()->route('product.shoppingCart');
    }

    public function getRemoveItem($id) {
        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Cart($oldCart);
        $cart->removeItem($id);

        if (count($cart->items) > 0) {
            Session::put('cart', $cart);
        } else {
            Session::forget('cart');
        }

        return redirect()->route('product.shoppingCart');
    }

    public function getCart()
    {
        if (!Session::has('cart')) {
            return view('shop.shopping-cart');
        }
        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);

        return view('shop.shopping-cart', ['products' => $cart->items, 'totalPrice' => $cart->totalPrice]);
    }

    public function getCheckout()
    {
        error_log('error_log');
        var_dump('var_dump');
        echo 'echo';
        info('This is some useful information.');
        Log::info('Log_info');

        if (!Session::has('cart')) {
            return view('shop.shopping-cart');
        }
        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);
        $total = $cart->totalPrice;
        return view('shop.checkout', ['total' => $total]);
    }

    public function postCheckout(Request $request)
    {

        if (!Session::has('cart')) {    
            Log::info('trigger');
            return view('shop.shopping-cart');
        }
        else {
            $oldCart = Session::get('cart');
            $cart = new Cart($oldCart);
    
            Stripe::setApiKey('sk_test_51GtPlTARuhT4kw25HFnaYjt6OXuS1fTzNxO3LwUnZHNaGtAKwhvOtmAB9oVWQ3UQblAXfxKvUkkIVux7LWoh3HCg00UTeN1uLh');
            try {
                $charge = Charge::create(array(
                    "amount" => $cart->totalPrice * 100,
                    "currency" => "myr",
                    "source" => $request->input('stripeToken'), // obtained with Stripe.js
                    "description" => "Test Charge"
                ));
                $order = new Order();
                $order->cart = serialize($cart);
                $order->address = $request->input('address');
                $order->name = $request->input('name');
                $order->payment_id = $charge->id;
                
                Auth::user()->orders()->save($order);
            } catch (\Exception $e) {
                Log::info('trigger1');
                return redirect()->route('checkout')->with('error', $e->getMessage());
            }
    
            Session::forget('cart');
            return redirect()->route('product.index')->with('success', 'Successfully purchased products!');
        }
    }
}
