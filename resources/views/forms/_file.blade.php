<div class="{{ $errors->has($name) ? ' has-danger' : '' }}">
    <div class="input-group">

        <div class="custom-file">
            {{ Form::file(
   $name,
   [
   'required'=>(isset($required) ? 'required' : null),
   'id'=>'input_file_'.str_slug($name),
   'class'=>' js-custom-file-input '
   .($errors->has($name) ? ' is-invalid ' : '')
   .($class ?? ''),
   'placeholder'=>($placeholder ?? null),
   'autocomplete'=>($autocomplete ?? 'on'),
   'data-value'=>($value ?? null),
   ]+($attributes ?? [])
   ) }}
            <label class="custom-file-label"
                   for="{{ 'input_file_'.str_slug($name)  }}">{{ $label ?? 'Выберите файл' }}</label>
        </div>

    </div>
    @isset($text)
        <small class="form-text text-muted"> {!! $text   !!}</small>
    @endisset
</div>

