
@if(count($records))
    <div class="home-slider-container {{isset($home->classes['container']) ? $home->classes['container'] : ''}}">
        <div class="owl-carousel home-slides">
            @foreach($records as $k => $record)
                @foreach($record->adverts()->active()->Started()->Unexpired()->orderBy('sort')->get() as $k => $advert)
                    <div class="item {{isset($home->classes['cards']) ? $home->classes['cards'] : ''}}">
                        <a href="{{$advert->url}}">
                            <img src="{{ asset($advert->image) }}" alt="" />
                        </a>
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>
@endif