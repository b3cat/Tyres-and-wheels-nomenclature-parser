@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>
            {{$category->{'name_ru-RU'} }}

        </h1>
        <div class="form-group btn-group" role="group" aria-label="Basic example">
            <a href="{{ url('/whitelist/make/'.($category->{'id'} - 1)) }}" class="btn btn-info"><<</a>
            <a href="#" class="btn btn-info disabled">{{$category->{'name_ru-RU'} }}</a>
            <a href="{{ url('/whitelist/make/'.($category->{'id'} + 1)) }}" class="btn btn-info">>></a>
        </div>
        <div class="form-group">
            category_id: {{$category->{'category_id'} }}<br>
            category_parent_id: {{$category->{'category_parent_id'} }}<br>
        </div>

        {!! Form::open(['action' => 'WhitelistController@saveWhitelist', 'method' => 'post']) !!}
        {!! Form::hidden('whitelisted_id', $category->{'id'}) !!}
        <div class="form-group">
            {!! Form::textarea('whitelist', $whitelist, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::submit('Сохранить', ['class' => 'btn btn-success']) !!}
        </div>
        {!! Form::close() !!}
    </div>
@endsection