<?php

namespace Modules\Apps\Transformers\Api;

use  Illuminate\Http\Resources\Json\JsonResource;
use Modules\Catalog\Entities\{Category,Product};
use Modules\Advertising\Entities\Advertising;
use Modules\Catalog\Entities\Brand;

class HomeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            "id"   => $this->id ,
            'title' => $this->title,
            'display_type' => $this->display_type,
            'grid_columns_count' => $this->grid_columns_count,
        ];

        return $this->addSectionData($data);
    }

    public function addSectionData($data)
    {
        $type = $this->type;

        switch ($type){
            case 'categories':
                $model = !$this->$type()->count() ? new Category : Category::whereIn('id',$this->$type->pluck('id')->toArray());
                $records =  $model->has('products')->active()->MainCategories()->get();
            break;
            case 'products':
                $model = !$this->$type()->count() ? new Product : Product::whereIn('id',$this->$type->pluck('id')->toArray());
                $records = $model->active()->orderByRaw("RAND()")->take(24)->get();
            break;
            case 'sliders':
                $records =  Advertising::whereIn('ad_group_id', $this->$type->pluck('id')->toArray())
                    ->active()->Started()->Unexpired()->orderBy('sort')->get();
                    break;
            case 'brands':
                $model = !$this->$type()->count() ? new Brand : Brand::whereIn('id',$this->$type->pluck('id')->toArray());
                $records = $model->active()->has("products")->orderByRaw("RAND()")->take(24)->get();
            break;
            default:
                $records = $this->$type;
                break;
        }

        $data['type'] = $type;
        if($type == 'description'){

            $data['data'] = [
                'description' => $this->description
            ];
        } else{
            $resource = $this->getResourceByType();
            $data['data'] = $resource::collection($records);
        }
            
        return $data;
    }
}
