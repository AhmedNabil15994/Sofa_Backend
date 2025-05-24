<div class="container {{isset($home->classes['container']) ? $home->classes['container'] : ''}}">
    <h3 class="slider-title {{isset($home->classes['title']) ? $home->classes['title'] : ''}}"> {{$home->title}}</h3>
    {!! $home->description !!}
</div>
