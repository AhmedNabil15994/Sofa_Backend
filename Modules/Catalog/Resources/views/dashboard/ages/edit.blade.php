@extends('apps::dashboard.layouts.app')
@section('title', __('catalog::dashboard.ages.routes.update'))
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
                    <a href="{{ url(route('dashboard.ages.index')) }}">
                        {{__('catalog::dashboard.ages.routes.index')}}
                    </a>
                    <i class="fa fa-circle"></i>
                </li>
                <li>
                    <a href="#">{{__('catalog::dashboard.ages.routes.update')}}</a>
                </li>
            </ul>
        </div>

        <h1 class="page-title"></h1>

        <div class="row">
            {!! Form::model($model,[
                          'url'=> route('dashboard.ages.update',$model->id),
                          'id'=>'updateForm',
                          'role'=>'form',
                          'page'=>'form',
                          'class'=>'form-horizontal form-row-seperated',
                          'method'=>'PUT',
                          'files' => true
                          ])!!}

                <div class="col-md-12">

                    <div class="col-md-3">
                        <div class="panel-group accordion scrollable" id="accordion2">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title"><a class="accordion-toggle"></a></h4>
                                </div>
                                <div id="collapse_2_1" class="panel-collapse in">
                                    <div class="panel-body">
                                        <ul class="nav nav-pills nav-stacked">
                                            <li class="active">
                                                <a href="#global_setting" data-toggle="tab">
                                                    {{ __('catalog::dashboard.ages.form.tabs.general') }}
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-9">
                        <div class="tab-content">
                            <div class="tab-pane active fade in" id="global_setting">
                                <div class="col-md-10">
                                    @include('catalog::dashboard.ages.form')
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        @include('apps::dashboard.layouts._ajax-msg')
                        <div class="form-group">
                            <button type="submit" id="submit" class="btn btn-lg green">
                                {{__('apps::dashboard.general.edit_btn')}}
                            </button>
                        </div>
                    </div>

                </div>

            {!! Form::close()!!}
        </div>
    </div>
</div>
@stop