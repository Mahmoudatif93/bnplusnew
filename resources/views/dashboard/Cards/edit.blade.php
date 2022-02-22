@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>@lang('site.Cards')</h1>

            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li><a href="{{ route('dashboard.Cards.index') }}"> @lang('site.Cards')</a></li>
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

                    <form action="{{ route('dashboard.Cards.update', $category->id) }}" method="post" enctype="multipart/form-data">

                        {{ csrf_field() }}
                        {{ method_field('put') }}


                        <div class="form-group">
                                <label>@lang('site.name')</label>
                                <input type="text" name="card_name" class="form-control "  value="{{ $category->card_name }}">
                            </div>


                        <div class="form-group">
                            <label>@lang('site.Companies')</label>
                            <select name="company_id" class="form-control">
                                <option value="">@lang('site.Companies')</option>
                                @foreach ($Companies as $row)
                                    <option value="{{ $row->id }}" {{ $category->company_id == $row->id ? 'selected' : '' }}>{{ $row->name }}</option>
                                @endforeach
                            </select>
                        </div>


                            <div class="form-group">
                                <label>@lang('site.price')</label>
                                <input type="text" name="card_price" class="form-control" value="{{ $category->card_price }}">
                            </div>
                            <div class="form-group">
                                <label>@lang('site.card_code')</label>
                                <input type="text" name="card_code" class="form-control" value="{{ $category->card_code }}">
                            </div>
                         {{--   <div class="form-group">
                                <label>@lang('site.amounts')</label>
                                <input type="number" name="amounts" class="form-control" value="{{ $category->amounts }}">
                            </div>
--}}
                            <div class="form-group">
                            <label for="images">@lang('site.image')</label>
                            <div class="input-group">
                                    <input type="file" name="card_image" class="form-control">
                            </div>
                        </div>

                        <div class="form-group">
                            <img src="{{ $category->card_image }}" style="width: 100px" class="img-thumbnail image-preview" alt="">

                        </div>
                        
                     <!--   <div class="form-group col-6">
                                <label>@lang('site.offer')</label>
                               
                                <input class="form-check-input" name="offer" type="checkbox" {{--$checked--}} >

                            </div>

-->
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-edit"></i> @lang('site.edit')</button>
                        </div>

                    </form><!-- end of form -->

                </div><!-- end of box body -->

            </div><!-- end of box -->

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->

@endsection
