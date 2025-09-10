<x-app-layout>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />
        <div class="px-5 py-4 container-fluid">
            <div class="alert alert-dark text-sm">
                <strong style="font-size:24px;">Comunidad: {{ $comunidad->nombre }}</strong>
            </div>

            <div class="card">
                <div class="card-body">
                    <div><b>ID:</b> {{ $comunidad->id }}</div>
                    <div><b>Parroquia:</b> {{ $comunidad->parroquia->nombre }}</div>
                    <div><b>Cantón:</b> {{ $comunidad->parroquia->canton->nombre }}</div>
                </div>
            </div>
        </div>
        <x-app.footer />
    </main>
</x-app-layout>
