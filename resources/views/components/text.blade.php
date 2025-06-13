@props(['tag' => 'p'])

@php $tag = $tag ?? 'p'; @endphp

<{{ $tag }} {{ $attributes->merge(['class' => 'fs-6 text-light']) }}>
    {{ $slot }}
</{{ $tag }}>