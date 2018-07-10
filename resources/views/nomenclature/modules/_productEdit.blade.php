    <div class="col-lg-12">
                           <span class="h4 my-3">Исходная строка:
                        </span><br>
        <br>
        <span class="h3 mt-2 "><mark>{{$product->{'source_string'} }}</mark>
                            <button class="btn btn-sm  btn-outline-secondary">запарсить еще раз</button></span>

        <br>
        <br>
    </div>


    <div class="col-lg-4">
        Производитель:
        @if(isset($product->model->parentCategory->{'name_ru-RU'}))
            <span class="badge badge-success">{{$product->model->parentCategory->{'name_ru-RU'} }}</span>
        @else
            <span class=" badge badge-danger">Значения нет</span>
        @endif
        <br>
        Модель:
        @if(isset($product->model->{'name_ru-RU'}))
            <span class="badge badge-success">{{$product->model->{'name_ru-RU'} }} </span>

        @else
            <span class=" badge badge-danger">Значения нет</span>
        @endif
        <br>
        @foreach($product->fields as $field)
            {{ $field->field->{'name_ru-RU'} }}:
            @if(isset($field->value->{'name_ru-RU'}))
                <span class="badge badge-success">{{$field->value->{'name_ru-RU'} }}</span>

            @else
                <span class=" badge badge-danger">Значения нет</span>
            @endif
            <br>
        @endforeach
        <a href="#" class="btn btn-outline-secondary btn-sm mt-4">Строка была обработана неверно</a>
        <small class="form-text text-muted">Если из строки были получены не все возможные параметры,
            или они были получены неверно
        </small>
    </div>
    <div class="col-lg-8">

        {{Form::open(['route' => 'taw.product.update', 'class' => 'update-product-form', 'method' => 'post'])}}
        <h4 class="">Основные поля</h4>
        <hr>
        <div class="row">
            <div class="col-lg-4">
                @include('forms._input', ['name' => 'manufacturer', 'text' => 'Производитель',
                'value'=>isset($product->model)?$product->model->parentCategory->{'name_ru-RU'}:''])
                {{--<label for="inputPassword2">Производитель</label>--}}
                {{--<input type="password" class="form-control"--}}
                {{--placeholder="Password">--}}
            </div>
            <div class="col-lg-4 ">
                @include('forms._input', ['name' => 'model', 'text' => 'Модель',
                'value'=>isset($product->model)?$product->model->{'name_ru-RU'}:''])
            </div>
        </div>
        <h4 class="">Доп. параметры</h4>
        <hr>
        <div class="row">
            @foreach($product->fields as $field)
                <div class="col-lg-3">
                    @include('forms._select',['name'=>$field->field->{'field_id'},'class'=>'', 'text' => $field->field->{'name_ru-RU'},
                    'value' => isset($field->value->{'name_ru-RU'})?$field->value->{'fields_value_id'}:'',

                    'list' => $fieldsValuesLists[$field->{'field_id'}]])
                </div>
            @endforeach
        </div>
        {!! Form::hidden('product_id', $product->{'id'}) !!}
        {!! Form::submit('Сохранить данные', ['class'=>'btn btn-sm btn-success']) !!}
        {!! Form::close() !!}
    </div>
