<div class="row">
    <div class="col-6">
        <h2 class="text-center">Производители</h2>
        <div class="list-group">
            @foreach($manufacturers as $manufacturer)

                <div class="list-group-item clearfix ">
                            <span class="h4">
                                {{$manufacturer->{'name_ru-RU'} }}
                            </span>
                    <span class="pull-right">
                                <a href="#" data-categoryid="{{$manufacturer->{'category_id'} }}" class="btn btn-sm btn-info js-show-models">
                                    Показать модели
                                </a>
                                <button data-categoryid="{{$manufacturer->{'category_id'} }}"
                                        class="btn btn-sm btn-primary show-whitelist"
                                        data-toggle="modal" data-target="#exampleModalCenter">
                                    Белый список
                                </button>
                            </span>
                </div>
            @endforeach
        </div>
    </div>
    <div class="col-6 whitelist-models">

        @include('nomenclature.whitelists._modules._models', [
            'models' => $models
        ])
    </div>
</div>