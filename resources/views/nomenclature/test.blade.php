@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>
            Тестирование парсера номенклатуры шин и дисков

        </h1>
        {{--<div class="form-group">--}}
        {{--category_id: {{$category->{'category_id'} }}<br>--}}
        {{--category_parent_id: {{$category->{'category_parent_id'} }}<br>--}}
        {{--</div>--}}

        {!! Form::open(['action' => 'NomenclatureController@parse', 'method' => 'post']) !!}
        <div class="form-group">
            {!! Form::text('source_string', isset($response['sourceString']) ? $response['sourceString'] : null, ['class' => 'form-control', 'placeholder' => 'Введите строку номенклатуры']) !!}
        </div>
        <div class="form-group">
            {!! Form::submit('Посмотреть', ['class' => 'btn btn-success']) !!}
        </div>
        {!! Form::close() !!}

        @if(isset($response))
            <div class="col-lg-12">
                <h2>Основные парметры</h2>
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">Название</th>
                        <th scope="col">id категории</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th scope="row">Производитель</th>
                        <td>{{$response['manufacturer']['displayName']}}</td>
                        <td>{{$response['manufacturer']['id']}}</td>
                    </tr>
                    <tr>
                        <th scope="row">Модель</th>
                        <td>{{$response['model']['displayName']}}</td>
                        <td>{{$response['model']['id']}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-12">
                <h2>Дополнительные парметры</h2>
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">Параметр</th>
                        <th scope="col">Значение</th>
                        <th scope="col">id параметра</th>
                        <th scope="col">id значения</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($response['fields'] as $field)
                        <tr>
                            <th>{{$field['fieldName']}}</th>
                            <td>{{$field['displayValue']}}</td>
                            <td>{{$field['fieldId']}}</td>
                            <td>{{$field['fieldsValueId']}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection