@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>
            Регулярные выражения для парсера

        </h2>
        <div class="row mt-5">
            @foreach($mainCategories as $category)
                {{Form::open([
                    'class' => 'col-lg-12 mb-3'
                ])}}
                <div class="">

                    {{Form::hidden('category_id', $category->{'category_id'})}}
                    @include('forms._input', [
                        'name' => $category->{'category_id'},
                        'text' => 'Регулряное выражение для определения категории '.$category->{'name_ru-RU'},
                        'value' => $category->{'regExp'}->{'regExpValue'}->{'reg_exp'}
                    ])
                    {{Form::submit('Обновить', ['class' => 'btn btn-success'])}}
                </div>
                {{Form::close()}}

            @endforeach
            @foreach($mainCategories as $category)
                <div class="col-12 mb-3 reg-exp-container">
                    <h3>{{$category->{'name_ru-RU'} }}</h3>
                    <div class="alert-block">

                    </div>
                    @php($doNotShow = [])
                    @foreach($category->fieldsWithPairs() as $field)

                        <div class="row mt-4 reg-exp-container">
                            @include('nomenclature.parser.regexps.modules._regEpsList')
                        </div>

                    @endforeach

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