@extends('layouts.dashboard.app')

@section('content')




<div class="content-wrapper">

    <section class="content-header">

        <h1>@lang('site.swaggerorders')</h1>

        <ol class="breadcrumb">
            <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
            <li class="active">@lang('site.swaggerorders') {{--count( $swaggerorders)--}}</li>
        </ol>
    </section>

    <section class="content">

        <div class="box box-primary">

            <div class="box-header with-border">

                <h3 class="box-title" style="margin-bottom: 15px">@lang('site.swaggerorders') (<small style="color: red;font-weight:bold"> {{--count( $dubiorders)--}}</small>

                    @lang('site.swaggerorders') )
                </h3>
                        @if($cardswagger->enable==0)

                        <a class="btn btn-info btn-block" href="{{ route('dashboard.enableswaggerapi') }}">
                        Disable Swagger Cards
                        </a>
                        @else

                        <a class="btn btn-success btn-block"  href="{{ route('dashboard.enableswaggerapi') }}">
                        Enable Swagger Cards
                        </a>
                        @endif



              

            </div><!-- end of box header -->
            <div id="print-area">
                <div class="box-body" id="frame">

                    @if (!empty($dubiorders))

                    <table id="example" class="table table-hover">

                        <thead>
                            <tr>
                                <th>#</th>

                                <th>@lang('site.orderNumber')</th>
                                <th>@lang('site.orderFinalTotal')</th>
                                <th>@lang('site.currencySymbol')</th>
                                <th>@lang('site.orderCreateDate')</th>
                                <th>@lang('site.orderCurrentStatus')</th>
                                <th>@lang('site.orderPaymentMethod')</th>
                                <th>@lang('site.action')</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($dubiorders as $index=>$category)
                            <tr>
                                <td>{{ $index + 1 }}</td>

                                <td>


                                    {{ $category['orderNumber'] }}
                                </td>
                                <td>{{ $category['orderFinalTotal'] }}</td>
                                <td>{{ $category['currencySymbol'] }}</td>
                                <td>{{ $category['orderCreateDate'] }}</td>
                                <td>{{ $category['orderCurrentStatus'] }}</td>
                                <td>{{ $category['orderPaymentMethod'] }}</td>

                                <td>
                                    <a class="btn btn-primary btn-sm" target="_blank" href="{{ route('dashboard.dubiorders.products', $category['orderNumber']) }}">


                                        <i class="fa fa-list"></i>
                                        @lang('site.show')
                                    </a>


                                </td>





                            </tr>

                            @endforeach
                        </tbody>

                    </table><!-- end of table -->



                    @else

                    <h2>@lang('site.no_data_found')</h2>

                    @endif

                </div><!-- end of box body -->
            </div>

        </div><!-- end of box -->

    </section><!-- end of content -->

</div><!-- end of content wrapper -->


@endsection