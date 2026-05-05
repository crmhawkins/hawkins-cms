@php $content = $block->content ?? []; @endphp
<section class="block-contact-form" style="padding:3rem 1rem;background:#f9f9f9;">
    <div style="max-width:600px;margin:0 auto;">
        <h2 style="text-align:center;margin-bottom:2rem;">{{ $content['title'] ?? 'Contacto' }}</h2>

        @if(session('success'))
            <div style="background:#d4edda;color:#155724;padding:1rem;border-radius:4px;margin-bottom:1rem;">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('contact.submit') }}" style="display:flex;flex-direction:column;gap:1rem;">
            @csrf
            <input type="text" name="name" placeholder="Tu nombre" required
                   style="padding:.75rem;border:1px solid #ddd;border-radius:4px;font-size:1rem;">

            <input type="email" name="email" placeholder="Tu email" required
                   style="padding:.75rem;border:1px solid #ddd;border-radius:4px;font-size:1rem;">

            <textarea name="message" rows="5" placeholder="Tu mensaje" required
                      style="padding:.75rem;border:1px solid #ddd;border-radius:4px;font-size:1rem;resize:vertical;"></textarea>

            <button type="submit"
                    style="padding:.75rem 2rem;background:#333;color:#fff;border:none;border-radius:4px;font-size:1rem;cursor:pointer;">
                Enviar mensaje
            </button>
        </form>
    </div>
</section>
