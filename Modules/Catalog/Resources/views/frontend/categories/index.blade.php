@extends('apps::frontend.layouts.master')
@section('title', __('catalog::frontend.category_products.title') )

@section('content')

    @if(!empty($parent))
        <div class="second-header category-headr d-flex align-items-center" style="background-image: url('{{asset($parent->banner_image ?? Setting::get('default_banner_categories') )}}')">
            <div class="container">
                <h1> {{$parent->title}}</h1>
            </div>
        </div>
    @endif
    <div class="container">
        <div class="inner-page">
            <div class="row">
                <div class="col-md-12">
                    <div class="list-products">
                        <div class="row" id="records_container">
                            <div class="col-lg-12">
                                @include('catalog::frontend.categories.components.categories-cards',['records' => $categories])

                                @include('apps::frontend.components.paginator.paginator',
                                ['paginator' => $categories , 'getMoreRoute' => route('frontend.categories.children' , !empty($parent) ? $parent->slug : null)])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection