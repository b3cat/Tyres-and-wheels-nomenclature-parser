@extends('layouts.app')

@section('content')
    <div class="mb-3 py-3 bg-white border-bottom ">
        <div class="container ">
            <div id="app">
                <passport-clients></passport-clients>

            </div>

            {!! Form::open(['route'=>'roles.index','method'=>'GET', 'class'=> 'form-inline']) !!}
            <div class="btn-group mr-2 mb-2" role="group">
                <a class="btn btn-info show-whitelists" href="#">Управлять белыми списками</a>
            </div>
            {{ Form::search('search',$frd['search'] ?? old('search') ?? null,[
                'data-source'=>'/', //route('roles.autocomplete')
                'aria-label'=>'Recipients rolename',
                'aria-describedby'=>'roles__search',
                'placeholder'=>"Поиск...",
                'class'=>'form-control grow-1 js-autocomplete js-on-change-submit mr-2 mb-2',
            ]) }}
            {!! Form::select('perPage', ['10' => '10', '25' => '25', '50' => '50', '100' => '100', '200' => '200'], $frd['perPage'] ?? old('perPage') ?? null, ['placeholder' => 'Кол-во', 'class' => 'form-control mr-2 mb-2']) !!}


            <button class="btn btn-primary mb-2" type="submit">Обновить</button>
            {!! Form::close() !!}


        </div>

    </div>

    <div class="container mt-5">
        <h1>
            Тестирование парсера номенклатуры шин и дисков

        </h1>
        <div class="row">

        </div>
        <div class="row position-relative">
            <div class="col-lg-12">

                @foreach($errorProducts as $product)
                    <div class="row product-wrapper">
                        @include('nomenclature.modules._productEdit', [
                        'product' => $product,
                        'fieldsValuesLists' => $fieldsValuesLists
                    ])
                    </div>
                    <hr>
                @endforeach
                {{ $errorProducts->links() }}
            </div>

            {{--<div class="col-lg-4">--}}
            {{--<form class=" position-sticky sticky-top pt-4">--}}
            {{--<h4>Белые списки</h4>--}}
            {{--<div class="form-group">--}}
            {{--<label for="exampleInputEmail1">Категория</label>--}}
            {{--<select type="" class="form-control js-whitelist" id="q" aria-describedby="emailHelp">--}}
            {{--<option>BF Goodrich</option>--}}
            {{--</select>--}}
            {{--</div>--}}
            {{--<div class="form-group">--}}
            {{--<label for="">Список</label>--}}
            {{--<textarea type="password" rows="10" class="form-control" id=""--}}
            {{--placeholder="Password">--}}
            {{--</textarea>--}}
            {{--</div>--}}

            {{--<button type="submit" class="btn btn-primary">Submit</button>--}}
            {{--</form>--}}
            {{--</div>--}}
        </div>

    </div>
@endsection