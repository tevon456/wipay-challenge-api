<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Sale;
use App\Models\Customer;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Faker\Factory as Faker;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request as GRequest;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->query('per_page', 10);
        $books = $user?->role == 'admin' ? Book::with('sales')->withCount('sales')->paginate($perPage) : Book::paginate($perPage);

        return response()->json($books);
    }

    /**
     * Display a search result of resources.
     */
    public function search(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->query('per_page', 10);
        $search = $request->query('query');

        $resultsWithSales = Book::where('title', 'LIKE', '%' . $search . '%')
            ->orWhere('author_name', 'LIKE', '%' . $search . '%')
            ->with('sales')->withCount('sales')->paginate($perPage);

        $resultsWithoutSales = Book::where('title', 'LIKE', '%' . $search . '%')
            ->orWhere('author_name', 'LIKE', '%' . $search . '%')
            ->paginate($perPage);

        $books = $user?->role == 'admin' ? $resultsWithSales : $resultsWithoutSales;

        return response()->json($books);
    }

    /**
     * Purchase a book.
     */
    public function purchase(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'book_id' => 'integer|required|min:1',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 400);
        }

        $bookId = $request->input('book_id');
        $user = auth()->user();
        $client = new Client();
        $book = Book::find($bookId);
        $quantity = $request->input('quantity');
        $total = $book->price * $quantity;
        $customer = Customer::with('user')->where('user_id', $user?->id)->first();
        $faker = Faker::create();
        $orderId = $faker->lexify('????????????????');

        if ($quantity > $book->inventory_count) {
            return response()->json(['errors' => [
                'message' => 'requested quantity greater than available stock.'
            ]], 400);
        }

        if (!$customer) {
            return response()->json(['errors' => [
                'message' => 'we are unable to process your request.'
            ]], 400);
        }

        if ($book->inventory_count == 0) {
            return response()->json(['errors' => [
                'message' => 'out of stock.'
            ]], 400);
        }

        // New pending sale entry 
        Sale::create([
            'book_id' => $bookId,
            'customer_id' => $customer->id,
            'price_at_purchase' => $book->price,
            'quantity' => $quantity,
            'order_id' => $orderId,
            'status' => 'pending',
        ]);

        $payload = [
            'account_number' => '1234567890',
            'currency' => 'JMD',
            'environment' => 'sandbox',
            'fee_structure' => 'customer_pay',
            'method' => 'credit_card',
            'order_id' => $orderId,
            'origin' => 'wipay-challenge',
            'response_url' => 'http://localhost:8000/api/book/purchase/callback',
            'country_code' => 'JM',
            'total' => $total,
            'addr1' => $customer->address_line_1,
            'city' => $customer->city,
            'phone' => $customer->phone_number,
            'email' => $customer->user->email,
            'name' => $customer->user->name,
        ];

        $formData = http_build_query($payload);

        try {

            $request = new GRequest('POST', 'https://jm.wipayfinancial.com/plugins/payments/request', [
                'Content-Type' => 'application/x-www-form-urlencoded', 'Accept' => 'application/json'
            ], $formData);

            $response = $client->send($request);

            return json_decode($response->getBody(), true);
        } catch (ClientException $error) {
            if ($error->hasResponse()) {

                $response = $error->getResponse();
                $statusCode = $response->getStatusCode();
                $body = $response->getBody()->getContents();

                return response()->json(['errors' => [
                    'statusCode' => $statusCode,
                    'body' => $body,
                ]], 400);
            } else {
                return response()->json(['errors' => [
                    'message' => 'we are unable to process your request.'
                ]], 400);
            }
        }
    }

    public function paymentCallback(Request $request)
    {
        $orderId = $request->query('order_id');
        $status = $request->query('status');
        $total = $request->query('total');

        $sale = Sale::where('order_id', $orderId)->first();

        if (!$sale) {
            return response()->json(['errors' => [
                'message' => 'issue confirming payment.'
            ]], 400);
        }

        if ($status != 'success') {

            $sale->update([
                'status' => 'failed'
            ]);

            return response()->json(['errors' => [
                'message' => 'payment failed.'
            ]], 400);
        }

        $sale->update([
            'status' => 'success',
            'final_cost' => $total,
        ]);

        $book = Book::find($sale->book_id);

        $book->update([
            'quantity' => $book->quantity - $sale->quantity
        ]);

        return response()->json([
            'message' => 'book(s) purchased successfully.',
            'details' => [
                'title' => $book->title,
                'author_name' => $book->author_name,
                'isbn' => $book->isbn,
                'total' => $sale->final_cost,
                'quantity' => $sale->quantity,
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'author_name' => 'required',
            'title' => 'required',
            'isbn' => 'required|unique:books,isbn|min:10|max:13',
            'price' => 'required|numeric|min:0',
            'inventory_count' => 'required|integer',
        ]);

        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 400);
        }

        $book = Book::create($request->all());

        return response()->json($book, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = auth()->user();
        $book = $user?->role == 'admin' ? Book::with('sales')->withCount('sales')->find($id) : Book::find($id);

        if (!$book) {
            return response()->json(['errors' => [
                'message' => 'requested resource not found.'
            ]], 404);
        }

        return response()->json($book);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $validation = Validator::make($request->all(), [
            'author_name' => 'required',
            'title' => 'required',
            'isbn' => 'required|min:10|max:13',
            'price' => 'required|numeric|min:0',
            'inventory_count' => 'required|integer',
        ]);

        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 400);
        }

        $book = Book::find($id);

        if (!$book) {
            return response()->json(['errors' => [
                'message' => 'requested resource not found.'
            ]], 404);
        }

        $book->update($request->all());

        return response()->json($book);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['errors' => [
                'message' => 'requested resource not found.'
            ]], 404);
        }

        $book->delete();

        return response()->json([
            'message' => 'book deleted successfully.',
        ]);
    }
}
