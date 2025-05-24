@if($attribute)
    @php $attributeType = $attribute->type;  @endphp

    @if(isset($customData['container_class']) && $attributeType != 'countryAndStates')
        <div class="{{$customData['container_class']}}">
    @endif
    @switch($attributeType)
        @case('radio')
        @case('checkbox')
            @php 
                $options = $attribute->options->pluck('value','id')->toArray(); 
                $defaultOption = $attribute->options->where('is_default', 1)->first(); 
            @endphp


                <label class="d-inline-block right-side">{{$attribute->name}} </label>
            @foreach($options as $key => $title)
                <div class="checkboxes {{$attributeType == 'radio' ? 'radios' : 'checkbox'}} mb-20">
                    <input type="{{$attributeType}}"
                        value="{{$key}}" 
                        id="attributes[{{$key}}]_{{$attributeType}}"
                        @change="refreshChildrenAttributesShowing"
                        data-name="attributes.{{$attribute->id}}" 
                        name="attributes[{{$attribute->id}}]" 
                        {{ $defaultOption && $defaultOption->id == $key ? "checked" : ""}}
                    >
                    <label for="attributes[{{$key}}]_{{$attributeType}}">{{$title}}</label>
                </div>
            @endforeach
            @break  
        @case('countryAndStates')
            @include('user::frontend.profile.addresses.components.country-selector.selector',[
                'attribute' => $attribute,
                'selected_country' => isset($customData['selected_country']) ? $customData['selected_country'] : null,
                'selected_state' => isset($customData['selected_state']) ? $customData['selected_state'] : null,
            ])
            
            @break  
        @case('drop_down')
            @php 
                $options = $attribute->options->pluck('value','id')->toArray(); 
                $defaultOption = $attribute->options->where('is_default', 1)->first(); 
            @endphp
            {!! field('front_attribute')->select("attributes[{$attribute->id}]", $attribute->name, $options, optional($defaultOption)->id,[
                '@change' => 'refreshChildrenAttributesShowing',
            ]) !!}
            @break
        @case('url')
            {!! field('front_attribute')->text("attributes[{$attribute->id}]", $attribute->name,null,[
                'data-name' => "attributes.{$attribute->id}",
                '@keyUp' => 'refreshChildrenAttributesShowing'
            ]) !!}
            @break
        @default
            {!! field('front_attribute')->$attributeType("attributes[{$attribute->id}]", $attribute->name,null,[
                'data-name' => "attributes.{$attribute->id}",
                '@keyUp' => 'refreshChildrenAttributesShowing'
                ]) !!}
            @break
    @endswitch
    
    @if(isset($customData['container_class']) && $attributeType != 'countryAndStates')
        </div>
    @endif
@endif
