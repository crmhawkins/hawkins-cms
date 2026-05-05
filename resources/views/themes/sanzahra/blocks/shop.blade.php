@php
    $tenant = function_exists('tenant') ? tenant() : null;
    $enabled = $tenant && ($tenant->ecommerce_enabled ?? false);
    $title = $block->content['title'] ?? 'Nuestra Tienda';
    $max = (int) ($block->content['max_products'] ?? 6);
    $products = $enabled ? \App\Models\Product::active()->latest()->take($max)->get() : collect();
@endphp

@if ($enabled)
    <section class="shop-block">
        <h2>{{ $title }}</h2>
        <div class="products-grid">
            @foreach ($products as $product)
                <article class="product-card">
                    @if (!empty($product->images))
                        <img src="{{ $product->images[0] }}" alt="{{ $product->name }}">
                    @endif
                    <h3>{{ $product->name }}</h3>
                    <p class="price">{{ $product->priceFormatted }}</p>
                    <form method="POST" action="{{ route('shop.cart.add') }}">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="qty" value="1">
                        <button type="submit">Añadir al carrito</button>
                    </form>
                </article>
            @endforeach
        </div>
    </section>
@endif
