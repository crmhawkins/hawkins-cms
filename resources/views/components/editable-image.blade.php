@auth
    @can('edit-content')
        <div data-edit-image-wrap style="{{ $wrapStyle ?? '' }}">
            <{{ $tag ?? 'div' }}
                data-edit-image="{{ $field }}"
                data-block-id="{{ $blockId }}"
                class="{{ $class ?? '' }}"
                style="{{ $style ?? '' }}"
            >{{ $slot ?? '' }}</{{ $tag ?? 'div' }}>
        </div>
    @else
        <{{ $tag ?? 'div' }} class="{{ $class ?? '' }}" style="{{ $style ?? '' }}">{{ $slot ?? '' }}</{{ $tag ?? 'div' }}>
    @endcan
@else
    <{{ $tag ?? 'div' }} class="{{ $class ?? '' }}" style="{{ $style ?? '' }}">{{ $slot ?? '' }}</{{ $tag ?? 'div' }}>
@endauth
