export default function NewsletterBye() {
  return (
    <main className="min-h-screen flex items-center justify-center p-6 bg-neutral-50">
      <div className="max-w-md text-center">
        <p className="text-xs tracking-[0.3em] uppercase text-neutral-400 mb-4">Hasta pronto</p>
        <h1 className="font-serif text-4xl mb-4">Te has dado de baja</h1>
        <p className="text-neutral-600 leading-relaxed">
          Ya no recibirás más correos. Si cambias de idea, siempre puedes volver a
          suscribirte cuando quieras.
        </p>
      </div>
    </main>
  );
}
