@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.Companies')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li><a href="{{ route('dashboard.Companies.index') }}"> @lang('site.Companies')</a></li>
                <li class="active">@lang('site.edit')</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header">
                    <h3 class="box-title">@lang('site.edit')</h3>
                </div><!-- end of box header -->

                <div class="box-body">

                    @include('partials._errors')

                    <form action="{{ route('dashboard.Companies.update', $category->id) }}" method="post" enctype="multipart/form-data">

                        {{ csrf_field() }}
                        {{ method_field('put') }}

                        <div class="form-group">
                            <label>@lang('site.kind')</label>
                            <select name="kind" class="form-control">
                                <option value="">@lang('site.kind')</option>
                                @if($category->kind=="local")
                            <option value="local" selected>@lang('site.local')</option>
                            <option value="national">@lang('site.national')</option>
                            @elseif($category->kind=="national")
                            <option value="local" >@lang('site.local')</option>
                            <option value="national"selected>@lang('site.national')</option>
                            @else
                            <option value="local" >@lang('site.local')</option>
                            <option value="national">@lang('site.national')</option>
                            @endif
                               
                            </select>
                        </div>


                            <div class="form-group">
                                <label>@lang('site.name')</label>
                                <input type="text" name="name" class="form-control" value="{{ $category->name }}">
                            </div>

                            <div class="form-group">
                            <label for="images">@lang('site.image')</label>
                            <div class="input-group">
                                    <input type="file" name="company_image" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <img src="{{ $category->company_image}}" style="width: 100px" class="img-thumbnail image-preview" alt="">

                        </div>
                        

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-edit"></i> @lang('site.edit')</button>
                        </div>

                    </form><!-- end of form -->

                </div><!-- end of box body -->

            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->

@endsection
