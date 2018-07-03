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
@include('forms._textarea',[
'name'=>'description',
'class'=>'',
'label'=>'Описание роли',
])

{{--@if(isset($permissions))--}}
    {{--<br>--}}
    {{--<p class="h5">Присвоенные разрешения</p>--}}
    {{--@forelse($permissions as $permission)--}}
        {{--@include('forms._checkbox',[--}}
            {{--'name' => 'permissions[]',--}}
            {{--'value' => $permission->getKey(),--}}
            {{--'label' => $permission->getDisplayName(),--}}
            {{--'checked' => $rolePermissions->contains($permission)--}}
            {{--])--}}
    {{--@empty--}}
        {{--<p>Разрешений не создано.</p>--}}
    {{--@endforelse--}}
    {{--<br>--}}
    {{--<br>--}}
    {{--<br>--}}
{{--@endif--}}