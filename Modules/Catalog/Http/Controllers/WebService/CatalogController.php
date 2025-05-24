<?php

namespace Modules\Catalog\Http\Controllers\WebService;

use Illuminate\Http\Request;
use Modules\Advertising\Transformers\WebService\AdvertisingGroupResource;
use Modules\Apps\Transformers\Api\HomeResource;
use Modules\Catalog\Transformers\WebService\AutoCompleteProductResource;
use Modules\Catalog\Transformers\WebService\FilteredOptionsResource;
use Modules\Catalog\Transformers\WebService\PaginatedResource;
use Modules\Catalog\Transformers\WebService\ProductResource;
use Modules\Catalog\Transformers\WebService\CategoryResource;
use Modules\Catalog\Repositories\WebService\CatalogRepository as Catalog;
use Modules\Apps\Http\Controllers\WebService\WebServiceController;
use Modules\Slider\Repositories\WebService\SliderRepository as Slider;
use Modules\Advertising\Repositories\WebService\AdvertisingRepository as Advertising;
use Modules\Slider\Transformers\WebService\SliderResource;
use Modules\Apps\Repositories\Frontend\AppHomeRepository as Home;
use Illuminate\Http\JsonResponse;
use Modules\Catalog\Transformers\WebService\{AgeResource,BrandResource};

class CatalogController extends WebServiceController
{
    protected $catalog;
    protected $slider;
    protected $advert;

    function __construct(Catalog $catalog, Slider $slider, Advertising $advert)
    {
        $this->catalog = $catalog;
        $this->slider = $slider;
        $this->advert = $advert;
    }

    public function getHomeData(Request $request)
    {
        $home_sections = (new Home)->getAll($request);
        
        return HomeResource::collection($home_sections);
    }

    public function getAllCategories(Request $request): JsonResponse
    {
        $categories = $this->catalog->getAllCategories($request);
        return $this->response(CategoryResource::collection($categories));
    }

    public function getAutoCompleteProducts(Request $request)
    {
        $products = $this->catalog->getAutoCompleteProducts($request);
        $result = AutoCompleteProductResource::collection($products);
        return $this->response($result);
    }

    public function getProductsByCategory(Request $request)
    {
        $categories = $this->catalog->getAllMainCategories($request);
        $result['main_categories'] = CategoryResource::collection($categories);

         $options = $this->catalog->getFilterOptions($request);
         $result['options'] = FilteredOptionsResource::collection($options);

        $products = $this->catalog->getProductsByCategory($request);
        $result['products'] = PaginatedResource::make($products)->mapInto(ProductResource::class);

        return $this->response($result);
    }

    public function getProductDetails(Request $request, $id): JsonResponse
    {
        $product = $this->catalog->getProductDetails($request, $id);
        if ($product) {
            $result = [
                'product' => new ProductResource($product),
                'related_products' => ProductResource::collection($this->catalog->relatedProducts($product)),
            ];
            return $this->response($result);
        } else
            return $this->response(null);
    }

    public function ages(Request $request): JsonResponse
    {
        $records = $this->catalog->getAges();
        return $this->response(AgeResource::collection($records));
    }

    public function brands(Request $request): JsonResponse
    {
        $records = $this->catalog->getBrands();
        return $this->response(BrandResource::collection($records));
    }
}
