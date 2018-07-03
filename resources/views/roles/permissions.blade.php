@extends('layouts.app')

@section('content')
    <div class="container pt-3">
        <div class="row justify-content-center">
            <div class="col-12">
                {!! Form::open(['route'=>['roles.permissions', $role],'method'=>'GET', 'class'=> 'form-inline']) !!}
                {{ Form::search('search',$frd['search'] ?? old('search') ?? null,[
                    'data-source'=>'/', //route('roles.autocomplete')
                    'aria-label'=>'Recipients permissionname',
                    'aria-describedby'=>'permissions__search',
                    'placeholder'=>"Поиск...",
                    'class'=>'form-control grow-1 js-autocomplete js-on-change-submit mr-2 mb-2',
                ]) }}
                {!! Form::select('perPage', ['10' => '10', '25' => '25', '50' => '50', '100' => '100', '200' => '200'], $frd['perPage'] ?? old('perPage') ?? null, ['placeholder' => 'Кол-во', 'class' => 'form-control mr-2 mb-2']) !!}
                <div class="btn-group mr-2 mb-2" role="group">
                    <a class="btn btn-outline-secondary" href="{{ route('roles.permissions', $role) }}"
                       title="Очистить форму">Очистить</a>
                </div>
                <button class="btn btn-primary mb-2" type="submit">Вперед!</button>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <div class="container pt-3 pb-5">
        <div class="row justify-content-center">
            <div class="col-12">
                {{ Form::model($role,['route'=>['roles.permissions.update',$role],'method'=>'PATCH', 'class' => 'js-ajax']) }}
                <div class="btn-group mb-2 mt-2">
                    <a href="#" onclick="$('input[type=checkbox]').attr('checked','checked'); return false;"
                       class="btn btn-outline-secondary">+</a>

                    <a href="#" onclick="$('input[type=checkbox]').removeAttr('checked'); return false;"
                       class="btn btn-outline-secondary">-</a>
                    <button type="submit" class="btn btn-success" onclick="return confirm(&quot;Подтвердите сохранение?&quot;)">
                        Сохранить
                    </button>
                </div>
                <div class="list-group">
                    @forelse ($permissions as $permission)
                        <div class="list-group-item list-group-item-action">
                            {{ Form::hidden('permissions[off]['.$permission->getKey().']') }}
                            <div class="row">
                                <label class="col form-check-label">
                                    @include('forms._checkbox',[
                                        'name' => 'permissions[on]['.$permission->getKey().']',
                                        'label' => $permission->getDisplayName(),
                                        'checked' => $role->hasPermission([$permission->getName()]),
                                    ])
                                </label>
                                <div class="col">
                                    <small class="text-muted">
                                        <i>
                                            {{ $permission->getName() }}
                                        </i>
                                    </small>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p>No permissions</p>
                    @endforelse
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    {{ $permissions->links() }}
@endsection
