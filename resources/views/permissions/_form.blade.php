    @include('forms._input',[
    'name'=>'name',
    'class'=>'',
    'required'=>true,
    'text'=>'Обязательное, уникальное поле',
    'label'=>'Псевдоним',
    ])
    @include('forms._input',[
    'name'=>'display_name',
    'class'=>'',
    'label'=>'Отображаемое название',
    ])
    @if(empty($permission))
        @include('forms._checkbox',[
            'name' => 'crud',
            'formClass' => 'form-group',
            'label' => "Создать CRUD",
            'checked' => false,
        ])
    @endif
    @include('forms._textarea',[
    'name'=>'description',
    'class'=>'',
    'label'=>'Описание разрешения',
    ])