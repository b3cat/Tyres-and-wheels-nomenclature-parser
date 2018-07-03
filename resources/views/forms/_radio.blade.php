<div class="form-check {{ $formClass ?? null }}">
    <label class="form-check-label">
        {{ Form::radio($name,($value ?? null),
        ($checked ?? old($name) ??
        isset($frd[$name]) && $frd[$name] === $value ? 'true' : null),
        [
            'class'=>'form-check-input '.($class ?? null),
            'required'=>(isset($required) ? 'required' : null),
        ]
        ) }}

        {!!  $label ?? null  !!}

        @isset($text)
            <small class="text-muted">
                {!!  $text ?? null  !!}
            </small>
        @endisset
    </label>
</div>
