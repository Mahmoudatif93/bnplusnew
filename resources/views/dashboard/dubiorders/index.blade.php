@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.dubiorders')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">@lang('site.dubiorders') {{count(  $dubiorders)}}</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header with-border">

                    <h3 class="box-title" style="margin-bottom: 15px">@lang('site.dubiorders') (<small style="color: red;font-weight:bold"> {{count(  $dubiorders)}}</small> 
                
                    @lang('site.orders')  )
                </h3>

                 
<button class="btn btn-success" onclick="printDocument()">Print Report</button>
               
                   
                </div><!-- end of box header -->

                <div class="box-body" id="documentId">

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
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($dubiorders as $index=>$category)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    
                                    <td>{{ $category['orderNumber'] }}</td>
                                    <td>{{ $category['orderFinalTotal'] }}</td>
                                    <td>{{ $category['currencySymbol'] }}</td>
                                    <td>{{ $category['orderCreateDate'] }}</td>
                                    <td>{{ $category['orderCurrentStatus'] }}</td>
                                    <td>{{ $category['orderPaymentMethod'] }}</td>
                                 



                                      
                                </tr>

                            @endforeach
                            </tbody>

                        </table><!-- end of table -->

                       

                    @else

                        <h2>@lang('site.no_data_found')</h2>

                    @endif

                </div><!-- end of box body -->


            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->


<script>

function printDocument() {
    var doc = document.getElementById('example');

    doc.contentWindow.focus();
    doc.contentWindow.print();
   
}

</script>

@endsection
