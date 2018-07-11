<h2 class="text-center">Модели {{isset($parentCategory) ? $parentCategory->{'name_ru-RU'} : null }}</h2>
@foreach($models as $model)

    <div class="list-group-item clearfix ">
        <span class="h4">
            {{$model->{'name_ru-RU'} }}
            <span class="h6"><small>{{$model->{'parentCategory'}->{'name_ru-RU'} }}</small></span>
        </span>

        <span class="pull-right">
            <button data-categoryId="{{$model->{'category_id'} }}" class=" btn btn-sm btn-primary show-whitelist" data-toggle="modal" data-target="#exampleModalCenter">
                Белый список
            </button>
        </span>
    </div>
@endforeach