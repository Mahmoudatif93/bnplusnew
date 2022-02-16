@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.dubiorders')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">@lang('site.dubiorders')</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header with-border">

                    <h3 class="box-title" style="margin-bottom: 15px">@lang('site.dubiorders') <small>{{ $dubiorders->total() }}</small></h3>

                 

               
                   
                </div><!-- end of box header -->

                <div class="box-body">

                    @if ($dubiorders->count() > 0)

                        <table class="table table-hover">

                            <thead>
                            <tr>
                                <th>#</th>
                               
                                <th>@lang('site.Companies')</th>
                                <th>@lang('site.name')</th>
                                <th>@lang('site.kind')</th>
                                <th>@lang('site.price')</th>
                                <th>@lang('site.card_code')</th>
                                <th>@lang('site.avaliable')</th>
                                
                                <th>@lang('site.image')</th>
                            
                                <th>@lang('site.action')</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($dubiorders as $index=>$category)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    
                                    <td>{{ $category->company->name }}</td>
                                    <td>{{ $category->card_name }}</td>
                                    <td>
                                    @if($category->nationalcompany=='local')
                                    @lang('site.local')
                                    @elseif($category->nationalcompany=='national')
                                    @lang('site.national')
                                    @endif

                                    </td>

                                    <td>{{ $category->card_price }}</td>
                                    <td>{{ $category->card_code }}</td>
                                 



                                      
                                </tr>

                            @endforeach
                            </tbody>

                        </table><!-- end of table -->

                        {{ $dubiorders->appends(request()->query())->links() }}

                    @else

                        <h2>@lang('site.no_data_found')</h2>

                    @endif

                </div><!-- end of box body -->


            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->



    <script type="text/javascript">
    $(document).ready(function() {
        $('select[name="company_id"]').on('change', function() {
            var company_id = $(this).val();
            if(company_id) {
                $.ajax({
                    url: 'compcard/'+company_id,
                    type: "GET",
                    dataType: "json",
                    success:function(data) {

                        
                        $('select[name="card_price"]').empty();
                        $.each(data, function(key, value) {
                            $('select[name="card_price"]').append('<option value="'+ value +'">'+ value +'</option>');
                        });


                    }
                });
            }else{
                $('select[name="card_price"]').empty();
            }
        });
    });
</script>



@endsection
