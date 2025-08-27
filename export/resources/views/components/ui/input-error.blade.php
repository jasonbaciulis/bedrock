{{--
    Display error when there is a validation error with the name field.
    Uses Laravel Precognition:
    https://laravel.com/docs/12.x/precognition#using-alpine
--}}

@props(['handle', 'id'])

<template x-if="form.invalid('{{ $handle }}')">
    <p
        id="{{ $id }}-error"
        {{ $attributes->class(['text-destructive text-sm']) }}
        x-text="form.errors.{{ $handle }}"
    ></p>
</template>
