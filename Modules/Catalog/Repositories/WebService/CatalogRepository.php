<?php

namespace Modules\Catalog\Repositories\WebService;

use Illuminate\Support\Facades\DB;
use Modules\Catalog\Entities\Age;
use Modules\Catalog\Entities\Brand;
use Modules\Catalog\Entities\Category;
use Modules\Catalog\Entities\VendorProduct;
use Modules\Catalog\Entities\Product;
use Modules\Catalog\Traits\CatalogTrait;
use Modules\Variation\Entities\Option;
use Modules\Variation\Entities\ProductVariant;
use Modules\Vendor\Entities\Vendor;

class CatalogRepository
{
    use CatalogTrait;

    protected $category;
    protected $product;
    protected $vendor;
    protected $prd;
    protected $prdVariant;
    protected $option;
    protected $defaultVendor;

    function __construct(
        Product        $prd,
        Category       $category,
        ProductVariant $prdVariant,
        Option         $option
    )
    {
        $this->category = $category;
        $this->prd = $prd;
        $this->prdVariant = $prdVariant;
        $this->option = $option;
    }

    public function getLatestNCategories($request)
    {
        $categories = $this->buildCategoriesTree($request);
        $count = $request->categories_count ?? 8;
        return $categories->where('show_in_home', 1)->orderBy('sort', 'asc')->take($count)->get();
    }

    public function getAllCategories($request)
    {
        $categories = $this->buildCategoriesTree($request);
        $categories = $categories->orderBy('sort', 'asc');
        if (!empty($request->categories_count))
            $categories = $categories->take($request->categories_count);
        return $categories->get();
    }

    public function getAllMainCategories($request)
    {
        return $this->category->active()->mainCategories()->orderBy('sort', 'asc')->get();
    }

    public function getFilterOptions($request)
    {
        return $this->option->active()
            ->with(['values' => function ($query) {
                $query->active();
            }])
            ->activeInFilter()
            ->orderBy('id', 'DESC')
            ->get();
    }

    public function getAutoCompleteProducts($request)
    {
        $products = $this->prd->active();
        if ($request['search']) {
            $products = $this->productSearch($products, $request);
        }
        return $products->orderBy('id', 'DESC')->get();
    }

    public function getProductsByCategory($request)
    {
        $allCats = $this->getAllSubCategoryIds($request->category_id);
        array_push($allCats, intval($request->category_id));

        $optionsValues = isset($request->options_values) && !empty($request->options_values) ? array_values($request->options_values) : [];
        
        $products = $this->prd->active()
            ->with([
                'offer' => function ($query) {
                    $query->active()->unexpired()->started();
                },
            ])
            ->with(['variants' => function ($q) {
                $q->with(['offer' => function ($q) {
                    $q->active()->unexpired()->started();
                }]);
            }]);

        if (count($optionsValues) > 0) {
            $products = $products->whereHas('variantValues', function ($query) use ($optionsValues) {
                $query->whereIn('option_value_id', $optionsValues);
            });
        }

        if ($request->category_id) {
            $products->whereHas('categories', function ($query) use ($allCats) {
                $query->whereIn('product_categories.category_id', $allCats);
            });
        }

        if ($request->ages && count($request->ages)) {
            $products->whereHas('ages', function($query) use($request){
                $query->whereIn('ages.id', $request->ages);
            });
        }

        if ($request->brands && count($request->brands)) {
            $products->whereHas('brands', function($query) use($request){
                $query->whereIn('brands.id', $request->brands);
            });
        }

        if ($request->gender) {
            $products->where('gender', $request->gender);
        }

        if ($request['low_price'] && $request['high_price']) {
            $products->whereBetween('price', [$request['low_price'], $request['high_price']]);
        }

        if ($request['search']) {
            $products = $this->productSearch($products, $request);
        }

        if ($request['sort']) {
            $products->when($request['sort'] == 'a_to_z', function ($query) {
                $query->orderByTranslation('title', 'asc');
            });
            $products->when($request['sort'] == 'z_to_a', function ($query) {
                $query->orderByTranslation('title', 'desc');
            });
            $products->when($request['sort'] == 'low_to_high', function ($query) {
                $query->orderBy('price', 'asc');
            });
            $products->when($request['sort'] == 'high_to_low', function ($query) {
                $query->orderBy('price', 'desc');
            });
        } else {
            $products->orderBy('id', 'DESC');
        }
        return $products->paginate(24);
    }

    public function getProductDetails($request, $id)
    {
        $product = $this->prd->active();

        if (!is_null($this->defaultVendor)) {
            $product = $product->where('vendor_id', $this->defaultVendor->id);
        }

        $product = $this->returnProductRelations($product, $request);
        return $product->find($id);
    }

    public function getLatestData($request)
    {
        $product = $this->prd->doesnthave('offer')->active();

        if (!is_null($this->defaultVendor)) {
            $product = $product->where('vendor_id', $this->defaultVendor->id);
        }

        $product = $this->returnProductRelations($product, $request);

        if ($request['search']) {
            $product = $this->productSearch($product, $request);
        }

        return $product->orderBy('id', 'desc')->take(10)->get();
    }

    public function getOffersData($request)
    {
        $product = $this->prd->active();

        if (!is_null($this->defaultVendor)) {
            $product = $product->where('vendor_id', $this->defaultVendor->id);
        }

        $product = $this->returnProductRelations($product, $request);

        if ($request['search']) {
            $product = $this->productSearch($product, $request);
        }

        $product = $product->whereHas('offer', function ($query) {
            $query->active()->unexpired()->started();
        });

        return $product->take(10)->get();
    }

    public function findOneProduct($id)
    {
        $product = $this->prd->active();

        $product = $this->returnProductRelations($product, null);

        return $product->find($id);
    }

    public function findOneProductVariant($id)
    {
        $product = $this->prdVariant->active()->with([
            'offer' => function ($query) {
                $query->active()->unexpired()->started();
            },
            'productValues', 'product',
        ]);

        if (!is_null($this->defaultVendor)) {
            $product = $this->prdVariant->whereHas('product', function ($query) {
                $query->where('vendor_id', $this->defaultVendor->id);
            });
        }

        return $product->find($id);
    }

    public function getAllSubCategoriesByParent($id)
    {
        return $this->category->where('category_id', $id)->get();
    }

    public function buildCategoriesTree($request)
    {
        $categories = $this->category->active()
            ->withCount(['products' => function ($q) {
                $q->active();
            }]);

        $categories = $categories->has('products');

        if ($request->with_sub_categories == 'yes')
            $categories = $categories->with('childrenRecursive');

        if ($request->parent_id)
            $categories = $categories->where('category_id', $request->parent_id);
        else
            $categories = $categories->mainCategories();

        if ($request->get_main_categories == 'yes')
            $categories = $categories->mainCategories();

        if ($request->with_products == 'yes') {
            // Get Main CategoryObserver Products
            $categories = $categories->with([
                'products' => function ($query) use ($request) {
                    $query->active();
                    $query = $this->returnProductRelations($query, $request);
                    if ($request['search']) {
                        $query = $this->productSearch($query, $request);
                    }

                    $query->orderBy('products.sort', 'asc');
                },
            ]);
        }

        return $categories;
    }

    public function productSearch($model, $request)
    {
        return $model->where(function ($query) use ($request) {
            $query->where('sku', $request['search']);
            $query->orWhere(function ($query) use ($request) {

                foreach (config('translatable.locales') as $code) {
                    $query->orWhere('title->'.$code, 'like', '%'.$request['search'].'%');
                    $query->orWhere('slug->'.$code, 'like', '%'.$request['search'].'%');
                }
            })->orWhereHas('searchKeywords', function ($query) use ($request) {

                foreach (config('translatable.locales') as $code) {
                    $query->orWhere('title->'.$code, 'like', '%'.$request['search'].'%');
                }
            });
        });
    }

    public function returnProductRelations($model, $request)
    {
        return $model->with([
            'offer' => function ($query) {
                $query->active()->unexpired()->started();
            },
            'options',
            'images',
            'subCategories',
            'addOns',
            'variants' => function ($q) {
                $q->with(['offer' => function ($q) {
                    $q->active()->unexpired()->started();
                }]);
            },
        ]);
    }

    public function relatedProducts($selectedProduct)
    {
        $relatedCategoriesIds = $selectedProduct->categories()->pluck('product_categories.category_id')->toArray();
        $products = $this->prd->where('id', '<>', $selectedProduct->id)->active();
        $products = $products->whereHas('categories', function ($query) use ($relatedCategoriesIds) {
            $query->whereIn('product_categories.category_id', $relatedCategoriesIds);
        });
        return $products->orderBy('id', 'desc')->take(10)->get();
    }

    public function getProductsByVendor($id)
    {
        $products = $this->prd->active()->where('vendor_id', $id);
        $products = $this->returnProductRelations($products, null);
        return $products->orderBy('id', 'DESC')->paginate(24);
    }


    public function getAges()
    {
        return Age::active()->get();
    }

    public function getBrands()
    {
        return Brand::active()->get();
    }
}
