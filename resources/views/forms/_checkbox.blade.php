<div class="form-check {{ $formClass ?? null }}">
    <label class="form-check-label">
        {{ Form::checkbox($name,($value ?? null),($checked ?? old($name) ??
        $frd['checked'] ?? null),
        [
            'class'=>'form-check-input '.($class ?? null),
            'required'=>(isset($required) ? 'required' : null),

        ]+($attributes ?? [])
        ) }}

                {!! $label ?? null  !!}

    @isset($text)
            <small class="text-muted">
                {!!  $text ?? null  !!}
            </small>
        @endisset
    </label>
</div>
