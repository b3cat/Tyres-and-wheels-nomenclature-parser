<h2>Список парных полей</h2>
@foreach($mainCategories as $category)
    <h3>{{$category->{'name_ru-RU'} }}</h3>
    <table class="table">
        <thead>
        <tr>
            <th >Пары полей</th>
            <th class="text-right">Действия</th>
        </tr>
        </thead>
        @foreach($category->pairFields() as $field)
            <tr>
                <td>
                    {{$field->{'name_ru-RU'} }}
                    &#128279; {{$field->{'pairField'}->{'name_ru-RU'} }}
                </td>
                <td class="text-right">
                    <button class="btn btn-sm btn-danger ">Удалить</button>
                </td>
            </tr>


        @endforeach
    </table>
    <hr>
@endforeach