@extends('apps::dashboard.layouts.app')
@section('title', __('catalog::dashboard.brands.routes.create'))
@section('content')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="page-bar">
                <ul class="page-breadcrumb">
                    <li>
                        <a href="{{ url(route('dashboard.home')) }}">{{ __('apps::dashboard.home.title') }}</a>
                        <i class="fa fa-circle"></i>
                    </li>
                    <li>
                        <a href="{{ url(route('dashboard.brands.index')) }}">
                            {{__('catalog::dashboard.brands.routes.index')}}
                        </a>
                        <i class="fa fa-circle"></i>
                    </li>
                    <li>
                        <a href="#">{{__('catalog::dashboard.brands.routes.create')}}</a>
                    </li>
                </ul>
            </div>

            <h1 class="page-title"></h1>

            <div class="row">
                <form id="form" role="form" class="form-horizontal form-row-seperated" method="post"
                      enctype="multipart/form-data" action="{{route('dashboard.brands.store')}}">
                    @csrf
                    <div class="col-md-12">

                        {{-- RIGHT SIDE --}}
                        <div class="col-md-3">
                            <div class="panel-group accordion scrollable" id="accordion2">
                                <div class="panel panel-default">

                                    <div id="collapse_2_1" class="panel-collapse in">
                                        <div class="panel-body">
                                            <ul class="nav nav-pills nav-stacked">
                                                <li class="active">
                                                    <a href="#global_setting" data-toggle="tab">
                                                        {{ __('catalog::dashboard.brands.form.tabs.general') }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#seo" data-toggle="tab">
                                                        {{ __('catalog::dashboard.brands.form.tabs.seo') }}
                                                    </a>
                                                </li>

                                            </ul>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- PAGE CONTENT --}}
                        <div class="col-md-9">
                            <div class="tab-content">

                                {{-- CREATE FORM --}}
                                <div class="tab-pane active fade in" id="global_setting">
                                    <h3 class="page-title">{{__('catalog::dashboard.brands.form.tabs.general')}}</h3>
                                    <div class="col-md-10">
                                        @foreach (config('translatable.locales') as $code)
                                            <div class="form-group">
                                                <label class="col-md-2">
                                                    {{__('catalog::dashboard.brands.form.title')}} - {{ $code }}
                                                </label>
                                                <div class="col-md-9">
                                                    <input type="text" name="title[{{$code}}]" class="form-control"
                                                           data-name="title.{{$code}}">
                                                    <div class="help-block"></div>
                                                </div>
                                            </div>

                                            {!! field()->textarea('short_description['.$code.']',
                                            __('catalog::dashboard.brands.form.short_description').'-'.$code ,
                                             null,
                                                  ['data-name' => 'short_description.'.$code,'class' => 'form-control']
                                             ) !!}

                                            {!! field()->textarea('description['.$code.']',
                                            __('catalog::dashboard.brands.form.description').'-'.$code ,
                                             null,
                                                  ['data-name' => 'description.'.$code]
                                             ) !!}
                                        @endforeach

                                        {!! field()->file('image', __('catalog::dashboard.brands.form.image')) !!}

                                        <div class="form-group">
                                            <label class="col-md-2">
                                                {{__('catalog::dashboard.brands.form.status')}}
                                            </label>
                                            <div class="col-md-9">
                                                <input type="checkbox" class="make-switch" id="test" data-size="small"
                                                       name="status">
                                                <div class="help-block"></div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="tab-pane fade in" id="seo">
                                    <h3 class="page-title">{{__('catalog::dashboard.brands.form.tabs.seo')}}</h3>
                                    <div class="col-md-10">

                                        @foreach (config('translatable.locales') as $code)
                                            <div class="form-group">
                                                <label class="col-md-2">
                                                    {{__('catalog::dashboard.brands.form.meta_keywords')}} - {{ $code }}
                                                </label>
                                                <div class="col-md-9">
                                                    <textarea name="seo_keywords[{{$code}}]" rows="8" cols="80"
                                                              class="form-control"
                                                              data-name="seo_keywords.{{$code}}"></textarea>
                                                    <div class="help-block"></div>
                                                </div>
                                            </div>
                                        @endforeach

                                        @foreach (config('translatable.locales') as $code)
                                            <div class="form-group">
                                                <label class="col-md-2">
                                                    {{__('catalog::dashboard.brands.form.meta_description')}}
                                                    - {{ $code }}
                                                </label>
                                                <div class="col-md-9">
                                                    <textarea name="seo_description[{{$code}}]" rows="8" cols="80"
                                                              class="form-control"
                                                              data-name="seo_description.{{$code}}"></textarea>
                                                    <div class="help-block"></div>
                                                </div>
                                            </div>
                                        @endforeach

                                    </div>
                                </div>

                                {{-- END CREATE FORM --}}
                            </div>
                        </div>

                        {{-- PAGE ACTION --}}
                        <div class="col-md-12">
                            <div class="form-actions">
                                @include('apps::dashboard.layouts._ajax-msg')
                                <div class="form-group">
                                    <button type="submit" id="submit" class="btn btn-lg blue">
                                        {{__('apps::dashboard.general.add_btn')}}
                                    </button>

                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
