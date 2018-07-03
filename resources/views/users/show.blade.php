@extends('layouts.app')

@section('content')

    <div class="container pt-3 pb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ $user->getFullName() }}</div>

                    <div class="card-body">
                        <p>{{ $user->getName() }}</p>
                        <p>{{ $user->getLastName() }}</p>
                        <p>{{ $user->getMiddleName() }}</p>
                        <p>{{ $user->getEmail() }}</p>
                        <p>{{ $user->getPhone() }}</p>
                        <p>{{ $user->getSex() }}</p>
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection
