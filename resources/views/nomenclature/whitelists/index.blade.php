@extends('layouts.app')

@section('content')
    <div class="mb-3 py-3 border-bottom ">
        <div class="container ">
            <h1 class="mb-4">
                Управление белыми списками

            </h1>
            {!! Form::open(['route'=>'roles.index','method'=>'GET', 'class'=> 'form-inline']) !!}
            {{ csrf_field() }}
            {{ Form::search('search',$frd['search'] ?? old('search') ?? null,[
                'data-source'=>'/', //route('roles.autocomplete')
                'aria-label'=>'Recipients rolename',
                'aria-describedby'=>'roles__search',
                'placeholder'=>"Поиск...",
                'class'=>'form-control grow-1 js-categories-search mr-2 mb-2',
            ]) }}


            <button class="btn btn-primary mb-2" type="submit">Найти</button>
            {!! Form::close() !!}
        </div>


    </div>

    <div class=" position-sticky">
        <div class="container whitelist-editor-content">

            @include('nomenclature.whitelists._modules._categories', [
                'manufacturers' => $manufacturers,
                'models' => $models
            ])

        </div>
    </div>
    <div class="modal fade " id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered  " role="document">
            <div class="modal-content" >
                {!! Form::open() !!}
                <div class="modal-body">
                    <div class="container">

                        @include('forms._textarea', [
                            'name' => 'whitelist',
                            'label'=> 'Белый список',
                            'class' => 'whitelist-body'
                        ])

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    {{Form::button('Сохранить', ['class' => 'btn btn-success'])}}
                </div>
                {!! Form::close() !!}

            </div>
        </div>
    </div>

@endsection