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
                   


                <button class="btn btn-block btn-primary print-btn"><i class="fa fa-print"></i> @lang('site.print')</button>




              

            </div><!-- end of box header -->
            <div id="print-area">
                <div class="box-body" id="frame">

                    @if (!empty($alldata))

                    <table id="example" class="table table-hover  table-bordered">

                        <thead>
                            <tr>
                              

                                <th>id</th>
                                <th>number</th>
                                <th>dateTime</th>
                                <th>statement</th>
                                <th>credit</th>
                                <th>creditorName</th>
                                <th>debitorName</th>


                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($alldata['results'] as $index=>$category)
                            <tr>
                         

                                <td>


                                    {{ $category['id'] }}
                                </td>
                                <td>{{ $category['number'] }}</td>
                                <td>{{ $category['dateTime'] }}</td>
                                <td>{{ $category['statement'] }}</td>
                                <td>{{ $category['credit'] }}</td>
                                <td>{{ $category['creditorName'] }}</td>
                                <td>{{ $category['debitorName'] }}</td>
                                

                             





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