@extends('layouts.app')

@section('content')
    <div class="container pt-3 pb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ $permission->getDisplayName() }}</div>

                    <div class="card-body">
                        <p>Псевдоним {{ $permission->getName() }}</p>
                        <p class="h5">Описание</p>
                        <p>{{ $permission->getDescription() }}</p>
                        <br>
                        <a class="btn btn-primary" href="{{ route('permissions.edit', $permission) }}">Редактировать</a>
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection
