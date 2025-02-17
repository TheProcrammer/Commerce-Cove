<x-mail::message>
    {{-- Email Header --}}
    <h1 style="text-align: center; font-size: 24px"> Payment was completed successfully.</h1>

    {{-- Loop through each order in the $orders collection --}}
    @foreach($orders as $order)
        <x-mail::table>
            <table>
                <tbody>
                    <tr>
                        <td>Seller</td>
                        <td>
                            <a href="{{url('/')}}">
                                {{$order->vendorUser->vendor->store_name}}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>Order #</td>
                        <td># {{$order->id}}</td>
                    </tr>
                    <tr>
                        <td>Items</td>
                        <td>{{$order->orderItems->count()}}</td>
                    </tr>
                    <tr>
                        <td>Total</td>
                        <td>{{Number::currency($order->total_price)}}</td>
                    </tr>
                </tbody>
            </table>
        </x-mail::table>

        {{-- Display individual order items in a table --}}
        <x-mail::table>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderItems as $orderItem)
                        <tr>
                            <td>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td padding="" style="padding: 5px">
                                                <img style="min-width: 60px; max-width: 60px;" src="{{$orderItem->product->getImageForOptions($orderItem->variation_type_option_ids)}}" alt="">
                                            </td>
                                            <td style="font-size: 13px; padding: 5px">
                                                {{$orderItem->product->title}}    
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td>
                                {{$orderItem->quantity}}
                            </td>
                            <td>
                                {{Number::currency($orderItem->price)}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-mail::table>
    @endforeach
    <x-mail::button :url="$order->id">View Order Details</x-mail::button>
    <x-mail::subcopy>Thank you for shopping with us.</x-mail::subcopy>
    <x-mail::panel>Lorem Ipsum dolor sit amet</x-mail::panel>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>