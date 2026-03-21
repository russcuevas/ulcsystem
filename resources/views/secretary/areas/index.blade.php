<h2>Your Assigned Areas</h2>

<ul>
    @foreach($areas as $area)
        <li>
            {{ $area->location_name }} - {{ $area->areas_name }}
        </li>
    @endforeach
</ul>