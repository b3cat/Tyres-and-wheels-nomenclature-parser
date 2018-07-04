@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Результаты парсинга</h1>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Имя</th>
                <th scope="col">Всего элементов</th>
                <th scope="col">Всего полей</th>
                <th scope="col">Получено значений</th>
                <th scope="col">Процент успеха</th>
            </tr>
            </thead>
            <tbody>
            @foreach($results as $key => $result)
                {{--{{dd($result)}}--}}
                <tr>
                    <th scope="col">{{ $key+1 }}</th>
                    <th scope="col">{{ $result->{'name'} }}</th>
                    <th scope="col">{{ $result->{'parsed_items_number'} }}</th>
                    <th scope="col">{{ $result->{'fields_number'} }}</th>
                    <th scope="col">{{ $result->{'parsed_fields_number'} }}</th>
                    <th scope="col">{{ $result->{'fields_number'}?round(($result->{'parsed_fields_number'}/$result->{'fields_number'})*100, 2):'0' }}</th>

                </tr>
            @endforeach
            <tr>
                {{--<th scope="row">Производитель</th>--}}
                {{--<td>{{$response['manufacturer']['displayName']}}</td>--}}
                {{--<td>{{$response['manufacturer']['id']}}</td>--}}
                {{--</tr>--}}
                {{--<tr>--}}
                {{--<th scope="row">Модель</th>--}}
                {{--<td>{{$response['model']['displayName']}}</td>--}}
                {{--<td>{{$response['model']['id']}}</td>--}}
            </tr>
            </tbody>
        </table>
    </div>
@endsection
