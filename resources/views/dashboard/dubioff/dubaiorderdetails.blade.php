@extends('layouts.dashboard.app')

@section('content')




<div class="content-wrapper">

    <section class="content-header">

        <h1>@lang('site.dubioff')</h1>

        <ol class="breadcrumb">
            <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
            <li class="active">@lang('site.dubioff') {{--count( $dubioff)--}}</li>
        </ol>
    </section>

    <section class="content">

        <div class="box box-primary">

            <div class="box-header with-border">

                <h3 class="box-title" style="margin-bottom: 15px">@lang('site.dubioff') (<small style="color: red;font-weight:bold"> {{--count( $dubioff)--}}</small>

                    @lang('site.orders') )
                </h3>









            </div><!-- end of box header -->
            <div id="print-area">
                <div class="box-body" id="frame">

                    @if (count($cards)>0)

                    <table id="example" class="table table-hover">

                        <thead>
                            <tr>
                           
                                <th>@lang('site.Companies')</th>
                                <th>@lang('site.name')</th>
                          
                                <th>@lang('site.price')</th>
                                <th>@lang('site.card_code')</th>

                   

                                <th>@lang('site.action')</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($cards as $index=>$category)
                            <tr>
                                
                                
                                    
                                <td>{{ $category->company->name }}</td>
                                    <td>{{ $category->card_name }}</td>

                                    <td>{{ $category->card_price }}</td>
                                    <td>{{ $category->card_code }}</td>

                                <td>
                                @if(category->purchase==0)
                                    @if($category->enable==0)
                                    <a class="btn btn-primary  btn-block" target="_blank" href="{{ route('dashboard.dubidisablecard',$category->id) }}">
                                       Disable
                                    </a>
                                    @else
                                    <a class="btn btn-danger  btn-block" target="_blank" href="{{ route('dashboard.dubienablecard',$category->id) }}">
                                    Enable  
                                    </a>
                                    @endif
                                    @else
                                    <button class="btn btn-success  btn-block" disabled>
                                    تم البيع
                                    </button>
                                    @endif
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