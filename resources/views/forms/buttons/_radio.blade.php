<label class="btn btn-{{ $type ?? 'primary' }} {{ $isChecked === true ? 'active':null }}">
    <input type="radio" name="{{ $name }}" value="{{ $value }}" id="{{ str_slug($name.'_'.$value) }}"
           autocomplete="off" {{ $isChecked === true ? 'checked':null }}> {{ $label }}
</label>