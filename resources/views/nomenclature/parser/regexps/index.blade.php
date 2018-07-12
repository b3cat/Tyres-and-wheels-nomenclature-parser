@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>
            Регулярные выражения для парсера

        </h2>
        <div class="row mt-5">

            @foreach($mainCategories as $category)
                <div class="col-12 mb-3 reg-exp-container">
                    <h3>{{$category->{'name_ru-RU'} }}</h3>
                    <div class="alert-block">

                    </div>
                    {!! Form::open(['class' => 'update-regexps']) !!}
                    {!! Form::hidden('category_id', $category->{'category_id'}) !!}
                    <div class="row">
                        @foreach($category->{'fields'} as $field)
                            <div class="col-3">
                                @include('forms._input', [
                                 'name' => 'regexp'.$field->{'field_id'},
                                 'text' => $field->{'name_ru-RU'},
                                 'value' => $field->{'regExpMask'}->{'reg_exp_mask'} ,
                             ])
                            </div>
                        @endforeach
                    </div>
                    {{csrf_field()}}
                    {{Form::submit('Сохранить', ['class' => 'btn btn-sm btn-success'])}}
                    {!! Form::close() !!}
                </div>
            @endforeach


        </div>
        <hr>
        <h2>Проверка парсера</h2>
        <div class="row justify-content-center mt-5 check-parser">
            <div class="col-12">

                {!! Form::open(['class' => 'form-inline test-parse']) !!}
                {{ Form::search('source_string', null ,[
                    'placeholder'=>"Исхоная строка...",
                    'class'=>'form-control grow-1  mr-2 mb-2',
                ]) }}
                {!! Form::submit('Парсим', ['class' => 'btn btn-primary mb-2']) !!}
                {!! Form::close() !!}
            </div>
            <div class="col-lg-12 parsing-result">

            </div>
    </div>
@endsection