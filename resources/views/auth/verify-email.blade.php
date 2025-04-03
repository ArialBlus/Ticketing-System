<x-guest-layout>
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h4 class="card-title mb-0">Verificar Correo Electrónico</h4>
                        <p class="text-muted mt-2">
                            Gracias por registrarte. Antes de comenzar, ¿podrías verificar tu dirección de correo electrónico haciendo clic en el enlace que te acabamos de enviar?
                            Si no recibiste el correo, con gusto te enviaremos otro.
                        </p>
                    </div>

                    @if (session('status') == 'verification-link-sent')
                        <div class="alert alert-success mb-4">
                            Se ha enviado un nuevo enlace de verificación a tu dirección de correo electrónico.
                        </div>
                    @endif

                    <div class="d-flex justify-content-between">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                Reenviar correo de verificación
                            </button>
                        </form>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-link text-decoration-none">
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
