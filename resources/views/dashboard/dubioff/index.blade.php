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

                   
                </h3>









            </div><!-- end of box header -->
            <div id="print-area">
                <div class="box-body" id="frame">

                    @if (count($Companies)>0)

                    <table id="example" class="table table-hover">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('site.name')</th>
                                <th>@lang('site.kind')</th>
                                <th>@lang('site.controle')</th>

                                <th>@lang('site.Cards')</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($Companies as $index=>$category)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $category->name }}</td>
                                <td>
                                    @if($category->kind=="local")
                                    @lang('site.local')
                                    @elseif($category->kind=="national")
                                    @lang('site.national')
                                    @endif

                                </td>

                                <td>

                                    @if($category->enable==0)
                                    <a class="btn btn-primary  btn-block" target="_blank" href="{{ route('dashboard.dubidisable',$category->id) }}">
                                          Disable 
                                    </a>
                                    @else
                                    <a class="btn btn-danger  btn-block" target="_blank" href="{{ route('dashboard.dubienable',$category->id) }}">
                                    Enable
                                    </a>
                                    @endif
                                </td>

                                <td>
                                    <a class="btn btn-primary btn-sm" target="_blank" href="{{ route('dashboard.dubioff.products',$category->id) }}">


                                        <i class="fa fa-list"></i>
                                        @lang('site.show')
                                    </a>


                                </td>





                            </tr>

                            @endforeach
                        </tbody>

                    </table><!-- end of table -->

                    {{ $Companies->appends(request()->query())->links() }}

                    @else

                    <h2>@lang('site.no_data_found')</h2>

                    @endif

                </div><!-- end of box body -->
            </div>

        </div><!-- end of box -->

    </section><!-- end of content -->

</div><!-- end of content wrapper -->


@endsection