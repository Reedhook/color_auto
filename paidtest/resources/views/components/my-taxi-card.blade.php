<div class="card">
    <div class="card-body">
        <h5 class="card-title">{{ $taxi->original->name }}</h5>
        <h6 class="card-subtitle mb-2 text-muted">{{ $taxi->price }} руб.</h6>
        <h5 class="card-title" style="color:{{$color->name}}">{{$color->name}}</h5>
        <form action="{{ route('taxi.colorUpdate', ['taxi' => $taxi->taxi_id]) }}" method="POST">
            @csrf
            <input type="hidden" name="taxi_id" value="{{ $taxi->taxi_id }}">
            <label for="color_id">Цвет:</label>
            <label>
                <select name="color_id">
                    @foreach($colors as $color)
                        <option value="{{ $color->id }}">{{ $color->name }}</option>
                    @endforeach
                </select>
            </label>

            @if (!$is_free)
                <button type="submit" class="btn btn-primary">Поменять цвет (1000 руб.)</button>
            @else
                <button type="submit" class="btn btn-primary">Поменять цвет</button>
            @endif
        </form>
    </div>
</div>
