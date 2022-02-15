<div id="print-area">
    <table class="table table-hover table-bordered">

        <thead>
            <tr>
            <th>@lang('site.client_name')</th>
            <th>@lang('site.phone')</th>
         
                <th>@lang('site.card_code')</th>
                <th>@lang('site.price')</th>
               
                <th>@lang('site.status')</th>

            </tr>
        </thead>

        <tbody>
            {{-- @foreach ($products as $product)--}}
            <tr>
     
            <td>{{  $order->client->name }}</td>
            <td>{{  $order->client->phone }}</td>
           
                <td>{{ $products->card_code }}</td>
                <td>{{ $products->card_price}}</td>
                

                <td>
                    @if($order->paid=="false")
                    {{'Not Complete'}}
                    @else
                    {{' Completed'}}
                    @endif
                </td>
            </tr>
            {{-- @endforeach--}}
        </tbody>
    </table>
    <h3>@lang('site.total') <span>{{ number_format($order->card_price, 2) }}</span></h3>

</div>

<button class="btn btn-block btn-primary print-btn"><i class="fa fa-print"></i> @lang('site.print')</button>