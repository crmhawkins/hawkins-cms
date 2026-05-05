@auth
    @can('edit-content')
        <{{ $tag ?? 'span' }}
            data-edit-field="{{ $field }}"
            data-block-id="{{ $blockId }}"
            class="{{ $class ?? '' }}"
            style="{{ $style ?? '' }}"
        >{{ $slot }}</{{ $tag ?? 'span' }}>
    @else
        <{{ $tag ?? 'span' }} class="{{ $class ?? '' }}" style="{{ $style ?? '' }}">{{ $slot }}</{{ $tag ?? 'span' }}>
    @endcan
@else
    <{{ $tag ?? 'span' }} class="{{ $class ?? '' }}" style="{{ $style ?? '' }}">{{ $slot }}</{{ $tag ?? 'span' }}>
@endauth
