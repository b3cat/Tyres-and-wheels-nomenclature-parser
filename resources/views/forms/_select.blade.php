<div class="form-group {{ $errors->has($name) ? ' has-danger' : '' }}">
    @isset($label)
        <label class="form-control-label"
               for="input_{{ $type  ?? 'text' }}_{{ ($name) }}">{!! $label !!}&nbsp;</label>
    @endisset
    {{ Form::select(
    $name,
    $list,
    old($name) ?? ($value ?? null),
    [
    'class'=>'form-control form-control '
    .($errors->has(str_replace(['[',']'],['.',''],$name)) ? ' is-invalid ' : '')
    .($class ?? ''),
    'required'=>(isset($required) ? 'required' : null),
    ]+($attributes ?? [])) }}

    @if(isset($feedback) || $errors->has(str_replace(['[',']'],['.',''],$name)) === true)
        <div class=" invalid-feedback ">{{ $feedback ?? $errors->first(str_replace(['[',']'],['.',''],$name)) }}</div>
    @endif

    @isset($text)
        <small class="form-text text-muted">{{ $text }}.</small>
    @endisset
</div>
