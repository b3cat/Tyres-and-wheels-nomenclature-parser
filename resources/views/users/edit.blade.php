@extends('layouts.app')

@section('content')

    <div class="container pt-3 pb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ $user->getFullName() }}</div>

                    <div class="card-body">
                        {!! Form::model($user, ['route' => ['users.update', $user], 'method' => 'PATCH', 'class' => 'js-ajax']) !!}
                        @include('users._form')
                        <div class="form-group mb-0">
                            <div class="btn-group">
                                <a class="btn btn-outline-success" href="{{ route('users.roles', $user) }}">Роли</a>
                                <button type="submit" class="btn btn-primary">
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
