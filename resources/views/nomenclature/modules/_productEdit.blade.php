<div class="col-lg-12">
                           <span class="h4 my-3">Исходная строка:
                        </span><br>
    <br>
    <span class="h3 mt-2 "><mark>{{$product->getSourceString() }}</mark>
                            <a data-productid="{{$product->{'id'} }}" href="#"
                               class="btn btn-sm  btn-outline-secondary parse-again">запарсить еще раз</a></span>

    <br>
    <br>
</div>


<div class="col-lg-4">
    Производитель:
    @if($product->model !== null)
        <span class="badge badge-success">{{$product->model->parentCategory->getName() }}</span>
    @else
        <span class=" badge badge-danger">Значения нет</span>
    @endif
    <br>
    Модель:
    @if($product->model !== null)
        <span class="badge badge-success">{{$product->model->getName() }} </span>

    @else
        <span class=" badge badge-danger">Значения нет</span>
    @endif
    <br>
    @foreach($product->fields as $field)
        {{ $field->field->getName() }}:
        @if($field->value !== null)
            <span class="badge badge-success">{{$field->value->getName() }}</span>

        @else
            <span class=" badge badge-danger">Значения нет</span>
        @endif
        <br>
    @endforeach
    <a href="#" data-productid="{{$product->{'id'} }}" class="btn btn-outline-secondary btn-sm mt-4 send-error">Строка
        была обработана неверно</a>
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
            'value'=>isset($product->model)?$product->model->parentCategory->getName():''])
            {{--<label for="inputPassword2">Производитель</label>--}}
            {{--<input type="password" class="form-control"--}}
            {{--placeholder="Password">--}}
        </div>
        <div class="col-lg-4 ">
            @include('forms._input', ['name' => 'model', 'text' => 'Модель',
            'value'=>isset($product->model)?$product->model->getName():''])
        </div>
    </div>
    <h4 class="">Доп. параметры</h4>
    <hr>
    <div class="row">
        @foreach($product->fields as $field)
            <div class="col-lg-3">
                @include('forms._select',[
                'name'=>$field->field->getFieldId(),
                'class'=>'',
                'text' => $field->field->getName(),
                'value' => ($field->value !== null)?$field->value->getValueId():'',
                'list' => $fieldsValuesLists->get($field->{'field_id'})
                ])
            </div>
        @endforeach
    </div>
    {!! Form::hidden('product_id', $product->{'id'}) !!}
    {!! Form::submit('Сохранить данные', ['class'=>'btn btn-sm btn-success']) !!}
    {!! Form::close() !!}
</div>
