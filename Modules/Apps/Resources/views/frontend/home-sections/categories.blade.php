@if(count($records) > 0)
<div class="categories mb-40">
    <div class="container {{isset($home->classes['container']) ? $home->classes['container'] : ''}}">
        <h3 class="slider-title {{isset($home->classes['title']) ? $home->classes['title'] : ''}}">   {{$home->title}}</h3>

        @php
            $numOfColumns = $home->grid_columns_count ?? 4;
            $bootstrapColWidth = 12 / $numOfColumns;
        @endphp
        @include('catalog::frontend.categories.components.categories-cards',['records' => $records])
    </div>
@endif