@if($field->isPairField())
    <div class="col-12 ">
                                    <span class="h5">
                                        {{$field->{'name_ru-RU'}.' и '.$field->{'pairField'}->{'name_ru-RU'} }}
                                        <button class="btn btn-sm btn-outline-primary show-add-reg-exp-form">Добавить</button>
                                    </span>
        <hr>
    </div>

@else
    <div class="col-12">
                                    <span class="h5">
                                        {{$field->{'name_ru-RU'} }}
                                        <button class="btn btn-sm btn-outline-primary show-add-reg-exp-form">Добавить</button>
                                    </span>
        <hr>
    </div>
@endif
<div class="col-12">
    @foreach($field->{'regExpMasks'} as $regExpMask)
        {{Form::open(['class' => 'update-reg-exp'])}}
        {{Form::hidden('reg_exp_id', $regExpMask->{'id'})}}

        <div class="row">
            <div class="col-7">
                @include('forms._input', [
                'name' => 'reg-exp-mask',
                'text' => 'Регулярное выржаени',
                'value' => $regExpMask->{'reg_exp_mask'} ,
                ])
            </div>
            <div class="col-2">
                @include('forms._input', [
                'name' => 'priority',
                'text' => 'Приоритет',
                'value' => $regExpMask->{'priority'} ,
                ])
            </div>
            <div class="col-2">
                {{Form::submit('обновить', ['class' => 'btn btn-success'] )}}
            </div>
            <div class="col-1">
                {{Form::button('✕', ['data-regexpid' => $regExpMask->{'id'}, 'class' => 'btn btn-outline-danger delete-reg-exp'] )}}
            </div>
        </div>

        {{Form::close()}}



    @endforeach
</div>

<div class="col-12 add-reg-exp-form" style="display: none">
    {{Form::open(['class' => 'add-reg-exp'])}}
    <div class="row">

        <div class="col-10">
            {{Form::hidden('field_id', $field->{'field_id'})}}
            @include('forms._input', [
            'name' => 'regexp',
            'text' => 'Регулярное выражение',

            ])
        </div>
        <div class="col-2">
            {{Form::submit('Добавить', ['class' => 'btn btn-outline-success'] )}}
        </div>
    </div>
    {{Form::close()}}

</div>