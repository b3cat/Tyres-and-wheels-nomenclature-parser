@extends('layouts.app')

@section('content')

    <div class="container pt-3 pb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ $role->getDisplayName() }}</div>

                    <div class="card-body">
                        {!! Form::model($role, ['route' => ['roles.update', $role], 'method' => 'PATCH', 'class' => 'js-ajax']) !!}
                        @include('roles._form')
                        <div class="form-group mb-0">
                            <div class="btn-group">
                                <a class="btn btn-outline-success" href="{{ route('roles.permissions', $role) }}">Разрешения</a>
                                <button type="submit" class="btn btn-primary" onclick="return confirm(&quot;Подтвердите сохранение?&quot;)">
                                    Сохранить
                                </button>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                    </div>

                </div>
            </div>
        </div>
    @endsection
