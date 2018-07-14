@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>
            Упраление парными полями

        </h1>
        <div class="row mt-5">
            <div class="col-12">
                @foreach($mainCategories as $category)
                    {{Form::open(['class' => 'make-a-pair'])}}
                    {{csrf_field()}}
                    <h3>Создать пару полей в категории {{$category->{'name_ru-RU'} }}</h3>
                    <div class="row mb-3">
                        <div class="col-6">
                            @foreach($category->{'fields'} as $field)
                                @include('forms._radio', [
                                    'name' => 'first',
                                    'value' => $field->{'field_id'},
                                    'label' => $field->{'name_ru-RU'},
                                ])
                            @endforeach
                        </div>
                        <div class="col-6">
                            @foreach($category->{'fields'} as $field)
                                @include('forms._radio', [
                                    'name' => 'second',
                                    'value' => $field->{'field_id'},
                                    'label' => $field->{'name_ru-RU'},
                                ])
                            @endforeach
                        </div>
                        <div class="col-12 mt-3">
                            {{Form::submit('Создать пару', ['class' => 'btn btn-sm btn-success'])}}

                        </div>
                    </div>
                    {{Form::close()}}
                    <hr>
                @endforeach

                <div class="row">
                    <div class="col-12 show-table">

                        @include('nomenclature.parser.fields.modules._showTable')

                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection