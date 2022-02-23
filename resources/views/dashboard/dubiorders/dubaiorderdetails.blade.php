@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.dubiordersdetails')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">@lang('site.dubiordersdetails')</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header with-border">

                    <h3 class="box-title" style="margin-bottom: 15px">@lang('site.dubiordersdetails') (<small style="color: red;font-weight:bold"> </small> 
                
                    @lang('site.orders')  )
                </h3>

                 

<button class="btn btn-block btn-primary print-btn"><i class="fa fa-print"></i> @lang('site.print')</button>
               
                   
                </div><!-- end of box header -->
                <div id="print-area">
                <div class="box-body" id="frame">

                    @if (!empty($serials))

                        <table id="example" class="table table-hover">

                            <thead>
                            <tr>
                          
                               
                                <th>@lang('site.productName')</th>
                                <th>@lang('site.serialCode')</th>
                                <th>@lang('site.validTo')</th>
                             
                            </tr>
                            </thead>

                            <tbody>

                         
                                <tr>
                                    
                                    
                                    <td>
                                        
                                 
                                    {{  $code}}</td>
                                    <td>{{ $product }}</td>
                                    <td>{{ $validTo}}</td>
                                  
                                 



                                      
                                </tr>

                       
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
