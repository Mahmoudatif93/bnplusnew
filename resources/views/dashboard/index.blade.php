@extends('layouts.dashboard.app')

@section('title', __('site.dashboard'))

@section('content')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">

        <h1>
            @lang('site.dashboard')
        </h1>

        <ol class="breadcrumb">
            <li>
                <a href="{{ route('dashboard.index') }}"> <i class="fa fa-dashboard"></i> @lang('site.dashboard') </a>
            </li>


        </ol>
    </section>

    <section class="content">

        <div class="box box-primary">

            <div class="box-header with-border" style="padding: 15px">


            </div>

            <div class="box-body">
                {{--   @if($users->count() >0)
             <table class="table table-bordered">
                    <thead>

                        <tr>
                            <th style="width: 10px">#</th>
                            <th>@lang('site.first_name')</th>
                            <th>@lang('site.last_name')</th>
                            <th>@lang('site.email')</th>
                            <th>@lang('site.image')</th>
                            <th>@lang('site.action')</th>
                        </tr>
                    </thead>
                    <tbody>




                    </tbody>
                </table>
                @else
                <h2>@lang('site.no_data_found')</h2>
                @endif--}}

            </div><!-- End of box-body -->

        </div><!-- End of box -->

    </section><!-- End of content -->

</div>


@endsection



<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
</button>
