@extends('apps::dashboard.layouts.app')
@section('title', __('service::dashboard.service_orders.show.title'))
@section('css')
    <style>
        .btn:not(.md-skip):not(.bs-select-all):not(.bs-deselect-all).btn-lg {

            padding: 12px 20px 10px;
        }

        .hide_admin_tag {
            display: none;
        }

        .well {
            box-shadow: none;
        }
    </style>

@stop

@section('content')
    <style type="text/css">
        .table>thead>tr>th {
            border-bottom: none !important;
        }
    </style>
    <style type="text/css" media="print">
        @page {
            size: auto;
            margin: 0;
        }

        @media print {
            a[href]:after {
                content: none !important;
            }

            .contentPrint {
                width: 100%;
                /* font-family: tahoma; */
                font-size: 16px;
            }

            .invoice-body td.notbold {
                padding: 2px;
            }

            h2.invoice-title.uppercase {
                margin-top: 0px;
            }

            .invoice-content-2 {
                background-color: #fff;
                padding: 5px 20px;
            }

            .invoice-content-2 .invoice-cust-add,
            .invoice-content-2 .invoice-head {
                margin-bottom: 0px;
            }

            .no-print,
            .no-print * {
                display: none !important;
            }

        }
    </style>

    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="page-bar">
                <ul class="page-breadcrumb">
                    <li>
                        <a href="{{ url(route('dashboard.home')) }}">{{ __('apps::dashboard.home.title') }}</a>
                        <i class="fa fa-circle"></i>
                    </li>
                    <li>
                        <a href="{{ url(route('dashboard.service_orders.index')) }}">
                            {{ __('service::dashboard.service_orders.index.title') }}
                        </a>
                        <i class="fa fa-circle"></i>
                    </li>
                    <li>
                        <a href="#">{{ __('service::dashboard.service_orders.show.title') }}</a>
                    </li>
                </ul>
            </div>

            <h1 class="page-title"></h1>

            <div class="row">
                <div class="col-md-8">
                    <!-- BEGIN SAMPLE FORM PORTLET-->
                    <div class="portlet light bordered" style="    border: 1px solid #e7ecf1!important">
                        <div class="portlet-title no-print">
                            <div class="caption font-red-sunglo">
                                <i class="font-red-sunglo fa fa-file-text-o"></i>
                                <span class="caption-subject bold uppercase">
                                    {{ __('service::dashboard.service_orders.show.invoice_customer') }}
                                </span>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="row invoice-head contentPrint">
                                <div class="col-md-12 col-xs-12" style="margin-bottom: 30px;">
                                    <div class="invoice-logo row">

                                        <span class="header">
                                            <h3 class="uppercase">#{{ $order['id'] }}</h6>
                                        </span>
                                        @if (config('setting.logo'))
                                            <span class="image">
                                                <img src="{{ url(config('setting.logo')) }}" alt="" />
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6 col-xs-6">
                                    <div class="note well">
                                        <div class="company-address">
                                            <h6 class="uppercase">#{{ $order['id'] }}</h6>
                                            <h6 class="uppercase">
                                                {{ date('Y-m-d / H:i:s', strtotime($order->created_at)) }}
                                            </h6>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-xs-6">
                                    <div class="note well">
                                        @if (!empty($order->contact_info))
                                            <div class="company-address">
                                                <h6 class="">
                                                    {{ __('service::dashboard.service_orders.show.user.name') }}:
                                                    {{ $order->contact_info['name'] ?? '---' }}
                                                </h6>
                                                <h6 class="">
                                                    {{ __('service::dashboard.service_orders.show.user.mobile') }}:
                                                    {{ $order->contact_info['mobile'] ?? '---' }}
                                                </h6>
                                                <h6 class="">
                                                    {{ __('service::dashboard.service_orders.show.user.email') }}:
                                                    {{ $order->contact_info['email'] ?? '---' }}
                                                </h6>
                                            </div>
                                        @else
                                            <div class="company-address">
                                                <h6 class="">
                                                    {{ __('service::dashboard.service_orders.show.user.name') }}:
                                                    {{ optional($order->user)->name ?? '---' }}
                                                </h6>
                                                <h6 class="">
                                                    {{ __('service::dashboard.service_orders.show.user.mobile') }}:
                                                    {{ optional($order->user)->mobile ?? '---' }}
                                                </h6>
                                                <h6 class="">
                                                    {{ __('service::dashboard.service_orders.show.user.email') }}:
                                                    {{ optional($order->user)->email ?? '---' }}
                                                </h6>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-xs-12 table-responsive">
                                    <br>
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th class="invoice-title uppercase text-left">
                                                    {{ __('service::dashboard.service_orders.show.items.title') }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="notbold text-left">

                                                    @if ($order->service)
                                                        <a
                                                            href="{{ route('dashboard.services.edit', $order->service->id) }}">
                                                            <img class="product_photo"
                                                                src="{{ asset($order->service->image) }}" width="39px"
                                                                style="margin: 0px 2px;">
                                                            <span>
                                                                {{ $order->service->title }}
                                                            </span>
                                                        </a>
                                                    @else
                                                        <span>---</span>
                                                    @endif


                                                    @if ($order->description)
                                                        <h5>
                                                            <b>#
                                                                {{ __('service::dashboard.service_orders.show.items.description') }}</b>
                                                            : {{ $order->description }}
                                                        </h5>
                                                    @endif

                                                </td>

                                            </tr>
                                        </tbody>

                                    </table>
                                </div>

                                @if ($order->files)
                                    <div class="col-xs-12 table-responsive">
                                        <br>
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="invoice-title uppercase text-left">
                                                        {{ __('service::dashboard.service_orders.show.items.files') }}
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach ($order->files as $key => $file)
                                                    <tr>
                                                        <td class="notbold text-left">
                                                            <a href="{{ url($file) }}"
                                                                target="_blank">{{ __('service::dashboard.service_orders.show.items.file') }}
                                                                -
                                                                {{ ++$key }}</a>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                            </tbody>

                                        </table>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-md-4 no-print">
                    <!-- BEGIN SAMPLE FORM PORTLET-->
                    <div class="portlet light bordered" style="border: 1px solid #e7ecf1!important">

                        <div class="portlet-body">
                            <div class="row">
                                <div class="col-xs-2">
                                    <a class="btn btn-lg blue hidden-print margin-bottom-5"
                                        onclick="javascript:window.print();">
                                        {{ __('apps::dashboard.general.print_btn') }}
                                        <i class="fa fa-print"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

@stop

@section('scripts')
    <script></script>
@endsection
