@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <h3>Купленные авто</h3>
            @foreach ($userTaxis as $userTaxi)
                <div class="col-md-4 mb-3">
                    <x-my-taxi-card :taxi="$userTaxi"/>
                </div>
            @endforeach
        </div>
    </div>
@endsection
