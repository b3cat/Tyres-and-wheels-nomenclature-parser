@if(isset($response))
        <h2>Основные парметры</h2>
        <table class="table">
            <thead>
            <tr>
                <th scope="col"></th>
                <th scope="col">Название</th>
                <th scope="col">id категории</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th scope="row">Производитель</th>
                <td>{{$response['manufacturer']['displayName']}}</td>
                <td>{{$response['manufacturer']['id']}}</td>
            </tr>
            <tr>
                <th scope="row">Модель</th>
                <td>{{$response['model']['displayName']}}</td>
                <td>{{$response['model']['id']}}</td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="col-lg-12">
        <h2>Дополнительные парметры</h2>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">Параметр</th>
                <th scope="col">Значение</th>
                <th scope="col">id параметра</th>
                <th scope="col">id значения</th>
            </tr>
            </thead>
            <tbody>
            @foreach($response['fields'] as $field)
                <tr>
                    <th>{{$field['fieldName']}}</th>
                    <td>{{$field['displayValue']}}</td>
                    <td>{{$field['fieldId']}}</td>
                    <td>{{$field['fieldsValueId']}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

@endif