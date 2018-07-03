@extends('layouts.app')

@section('content')
    <div class="container pt-3 pb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ $role->getDisplayName() }}</div>

                    <div class="card-body">
                        <p>Псевдоним {{ $role->getName() }}</p>
                        <p class="h5">Описание</p>
                        <p>{{ $role->getDescription() }}</p>
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection
