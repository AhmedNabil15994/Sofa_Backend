<?php

namespace Modules\Catalog\Repositories\Dashboard;

use Illuminate\Http\Request;
use Modules\Catalog\Entities\Brand;
use Modules\Core\Repositories\Dashboard\CrudRepository;

class BrandRepository extends CrudRepository
{

    public function __construct($model = null)
    {
        parent::__construct(new Brand);
        $this->fileAttribute = ['image' => 'image'];
    }

    public function prepareData(array $data, Request $request, $model, $is_create = true): array
    {
        $data['status'] = $request->status ? 1 : 0;
        return parent::prepareData($data, $request, $model, $is_create);
    }
}
