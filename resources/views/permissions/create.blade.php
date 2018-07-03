@extends('layouts.app')

@section('content')
    <div class="container pt-3 pb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Создание разрешения</div>

                    <div class="card-body">
                        {!! Form::model(null, ['route' => ['permissions.store'], 'method' => 'POST']) !!}
                        @include('permissions._form')
                        <div class="form-group mb-0">
                            <div class="btn-group">
                                <button type="submit" class="btn btn-success" onclick="return confirm(&quot;Подтвердите сохранение?&quot;)">
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
