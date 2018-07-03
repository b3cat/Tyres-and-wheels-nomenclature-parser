@extends('layouts.app')

@section('content')
    <div class="container pt-3">
        <div class="row justify-content-center">
            <div class="col-12">
                {!! Form::open(['route'=>'users.index','method'=>'GET', 'class'=> 'form-inline']) !!}
                    {{ Form::search('search',$frd['search'] ?? old('search') ?? null,[
                        'data-source'=>'/', //route('users.autocomplete')
                        'aria-label'=>'Recipients username',
                        'aria-describedby'=>'users__search',
                        'placeholder'=>"Поиск...",
                        'class'=>'form-control js-autocomplete js-on-change-submit grow-1 mr-2 mb-2',
                    ]) }}
                    {!! Form::select('sex', ['male' => 'Мужчина', 'female' => 'Женщина'], $frd['sex'] ?? old('sex') ?? null, ['placeholder' => 'Пол', 'class' => 'form-control mr-2 mb-2']) !!}
                    {!! Form::select('perPage', ['10' => '10', '25' => '25', '50' => '50', '100' => '100', '200' => '200'], $frd['perPage'] ?? old('perPage') ?? null, ['placeholder' => 'Кол-во', 'class' => 'form-control mr-2 mb-2']) !!}
                    <div class="btn-group mr-2 mb-2" role="group">
                        <a class="btn btn-outline-secondary " href="{{ route('users.index') }}"
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
                <div class="list-group">
                    @forelse ($users as $user)
                        <div class="list-group-item list-group-item-action">
                            <div class="row">
                                <p class="col mb-0 d-flex align-items-center">
                                    <a href="{{ route('users.show', $user) }}">{{ $user->getFullName() }}</a>
                                </p>
                                <p class="col mb-0 d-flex align-items-center">
                                    <a href="mailto:{{ $user->getEmail() }}">{{ $user->getEmail() }}</a>
                                </p>
                                <div class="col btn-group">
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-primary ml-auto">Редактировать</a>
                                    {!! Form::model($user, ['route' => ['users.destroy', $user], 'method' => 'DELETE', 'class' => 'js-ajax btn-group']) !!}
                                    <button type="submit" class="btn btn-danger" title="Удалить пользователя" onclick="return confirm(&quot;Подтвердите удаление пользователя?&quot;)">Удалить</button>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                        {{--<a href="{{ route('users.show', $user) }}" class="list-group-item list-group-item-action">{{ $user->f_name }} {{ $user->l_name }} | {{ $user->email }}</a>--}}
                    @empty
                        <p>No users</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    {{ $users->links() }}
@endsection
