<!DOCTYPE html>
<html dir="{{ locale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>{{ __('order::api.orders.html_order.invoice_details') }}</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    @if (locale() == 'ar')
        <link href="https://cdn.rtlcss.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    @endif

    <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>

    <style>
        .invoice-title h2,
        .invoice-title h3 {
            display: inline-block;
        }

        .table>tbody>tr>.no-line {
            border-top: none;
        }

        .table>thead>tr>.no-line {
            border-bottom: none;
        }

        .table>tbody>tr>.thick-line {
            border-top: 2px solid;
        }
    </style>

</head>

<body>

    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="invoice-title">
                    <h2>{{ __('order::api.orders.html_order.invoice_title') }}</h2>
                    <h3 class="pull-right">{{ __('order::api.orders.html_order.invoice_title') }} # {{ $order->id }}
                    </h3>
                </div>
                <hr>
                <div class="row">
                    <div class="col-xs-6">
                        @if ($order->orderAddress != null)
                            <address>
                                <strong>{{ __('order::api.orders.html_order.billed_to') }}:</strong><br>
                                {{ $order->orderAddress->username ?? ($order->user->name ?? '---') }}<br>
                                @if (!is_null($order->orderAddress->state))
                                    {{ $order->orderAddress->state->city->title }}
                                    /
                                    {{ $order->orderAddress->state->title }}<br>
                                @endif
                                @if ($order->orderAddress->governorate)
                                    {{ $order->orderAddress->governorate }}
                                    <br>
                                @endif
                                @if ($order->orderAddress->block)
                                    {{ $order->orderAddress->block }}
                                    <br>
                                @endif
                                @if ($order->orderAddress->district)
                                    {{ $order->orderAddress->district }}
                                    <br>
                                @endif
                            </address>
                        @else
                            <address>
                                <strong>{{ __('order::api.orders.html_order.billed_to') }}:</strong><br>
                                {{ $order->user->name ?? '---' }}
                            </address>
                        @endif
                    </div>
                    <div class="col-xs-6 text-right">
                        <address>
                            <strong>{{ __('order::api.orders.html_order.shipped_to') }}:</strong><br>
                            @if (isset($order->orderAddress->building))
                                {{ $order->orderAddress->building }}
                                <br>
                            @endif

                            @if (isset($order->orderAddress->street))
                                {{ $order->orderAddress->street }}
                                <br>
                            @endif

                            @if (isset($order->orderAddress->floor))
                                {{ $order->orderAddress->floor }}
                                <br>
                            @endif

                            @if (isset($order->orderAddress->flat))
                                {{ $order->orderAddress->flat }}
                                <br>
                            @endif

                            @if (isset($order->orderAddress->address_title))
                                {{ $order->orderAddress->address_title }}
                                <br>
                            @endif

                            @if (isset($order->orderAddress->address))
                                {{ $order->orderAddress->address }}
                                <br>
                            @endif
                        </address>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-4">
                        <address>
                            <strong>{{ __('order::api.orders.html_order.payment_method') }}:</strong><br>
                            {{ ucfirst($order->transactions->method) }}
                        </address>
                    </div>
                    <div class="col-xs-4 text-center">
                        <address>
                            <strong>{{ __('order::api.orders.html_order.order_status') }}:</strong><br>
                            {{ optional($order->orderStatus)->title ?? '' }}
                        </address>
                    </div>
                    <div class="col-xs-4 text-right">
                        <address>
                            <strong>{{ __('order::api.orders.html_order.order_date') }}:</strong><br>
                            {{ $order->created_at->format('l jS \o\f F Y h:i A') }}<br><br>
                        </address>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><strong>{{ __('order::api.orders.html_order.order_summary') }}</strong>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-condensed">
                                <thead>
                                    <tr>
                                        <td><strong>{{ __('order::api.orders.html_order.form.item') }}</strong></td>
                                        <td class="text-center">
                                            <strong>{{ __('order::api.orders.html_order.form.price') }}</strong></td>
                                        <td class="text-center">
                                            <strong>{{ __('order::api.orders.html_order.form.quantity') }}</strong>
                                        </td>
                                        <td class="text-right">
                                            <strong>{{ __('order::api.orders.html_order.form.totals') }}</strong></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->orderProducts->mergeRecursive($order->orderVariations) as $orderProduct)
                                        <tr>
                                            @if (isset($orderProduct->product_variant_id) || $orderProduct->product_variant_title)
                                                <td>
                                                    {{ generateVariantProductData($orderProduct->variant->product, $orderProduct->product_variant_id, $orderProduct->variant->productValues->pluck('option_value_id')->toArray())['name'] }}
                                                </td>
                                            @else
                                                <td>{{ $orderProduct->product->title }}</td>
                                            @endif
                                            <td class="text-center">{{ $orderProduct->sale_price }}</td>
                                            <td class="text-center">{{ $orderProduct->qty }}</td>
                                            <td class="text-right">{{ $orderProduct->total }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td class="thick-line"></td>
                                        <td class="thick-line"></td>
                                        <td class="thick-line text-center">
                                            <strong>{{ __('order::api.orders.html_order.subtotal') }}</strong></td>
                                        <td class="thick-line text-right">{{ $order->subtotal }}</td>
                                    </tr>
                                    @if ($order->orderCoupons && empty($order->orderCoupons->products))
                                        <tr>
                                            <td class="no-line"></td>
                                            <td class="no-line"></td>
                                            <td class="no-line text-center">
                                                <strong>{{ __('order::api.orders.html_order.coupon') }}</strong></td>
                                            <td class="no-line text-right">
                                                @if ($order->orderCoupons->discount_type == 'value')
                                                    {{ $order->orderCoupons->discount_value }}
                                                @else
                                                    {{ $order->orderCoupons->discount_percentage }} %
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td class="no-line"></td>
                                        <td class="no-line"></td>
                                        <td class="no-line text-center">
                                            <strong>{{ __('order::api.orders.html_order.shipping') }}</strong></td>
                                        <td class="no-line text-right">{{ $order->shipping }}</td>
                                    </tr>
                                    <tr>
                                        <td class="no-line"></td>
                                        <td class="no-line"></td>
                                        <td class="no-line text-center">
                                            <strong>{{ __('order::api.orders.html_order.total') }}</strong></td>
                                        <td class="no-line text-right">{{ $order->total }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
