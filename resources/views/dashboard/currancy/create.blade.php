@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">
        <section class="content-header">
            <h1>@lang('site.Companies')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li><a href="{{ route('dashboard.Companies.index') }}"> @lang('site.Companies')</a></li>
                <li class="active">@lang('site.add')</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header">
                    <h3 class="box-title">@lang('site.add')</h3>
                </div><!-- end of box header -->
                <div class="box-body">

                    @include('partials._errors')

                    <form action="{{ route('dashboard.Companies.store') }}" method="post"  enctype="multipart/form-data">

                        {{ csrf_field() }}
                        {{ method_field('post') }}

                        <div class="form-group col-6"> <label>@lang('site.kind')</label>
                        <select name="kind" id="kind" class="form-control">
                            <option value="">@lang('site.kind')</option>
                            <option value="local">@lang('site.local')</option>
                            <option value="national">@lang('site.national')</option>
                            
                        </select>
                    </div>


                            <div class="form-group">
                                <label>@lang('site.name')</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                            </div>
                        
                        <div class="form-group col-4">
                                <label>@lang('site.image')</label>
                                <input type="file" name="company_image" class="form-control" value="{{ old( 'company_image') }}">
                            </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> @lang('site.add')</button>
                        </div>

                    </form><!-- end of form -->

                </div><!-- end of box body -->

            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->

@endsection
