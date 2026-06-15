@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header">
                    {{ $mode === 'create' ? 'Nuevo Informe Tecnico' : 'Editar Informe Tecnico' }}
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Ticket:</strong> #{{ $ticket->id }} - {{ $ticket->title }}
                    </div>

                    @if($mode === 'create' && $settings)
                        <div class="alert alert-info">
                            <strong>Numero estimado:</strong>
                            INFORME N°{{ str_pad(($settings->last_sequence ?? 0) + 1, 3, '0', STR_PAD_LEFT) }}-{{ now()->year }}- {{ $settings->report_code_suffix }}
                        </div>
                    @endif

                    <form method="POST" action="{{ $mode === 'create' ? route('support-reports.store', $ticket) : route('support-reports.update', $report) }}" id="support-report-form">
                        @csrf
                        @if($mode === 'edit')
                            @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label for="subject" class="form-label">Asunto</label>
                            <input
                                type="text"
                                id="subject"
                                name="subject"
                                class="form-control @error('subject') is-invalid @enderror"
                                value="{{ old('subject', $report->subject ?? '') }}"
                                placeholder="Ej.: Informe tecnico de las computadoras de la Oficina de Seguridad y Salud Ocupacional"
                                required
                            >
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contenido del informe</label>
                            <textarea id="content" name="content" class="form-control @error('content') is-invalid @enderror" rows="8" required>{{ old('content', $report->content ?? '') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">{{ $mode === 'create' ? 'Generar Informe' : 'Actualizar Informe' }}</button>
                            @if($mode === 'edit')
                                <a href="{{ route('support-reports.show', $report) }}" class="btn btn-light">Ver / Reimprimir</a>
                            @endif
                            <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-light">Volver al Ticket</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/jodit@latest/es2021/jodit.fat.min.css"
    />
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jodit@latest/es2021/jodit.fat.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jodit/3.24.5/jodit.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jodit/3.24.5/plugins/emoji/emoji.min.js"></script>

    <script>

    // Botón personalizado 😀
    // Lista de emojis que quieras mostrar
        const emojiList = [
            "😀","😃","😄","😁","😆","😅","😂","🤣","😊","😇",
            "🙂","🙃","😉","😍","🥰","😘","😗","😙","😚","😋",
            "😜","🤪","😝","🤑","🤗","🤭","🤫","🤔","🤨","😐",
            "😑","😶","😏","😒","🙄","😬","🤥","😌","😔","😪",
            "🤤","😴","😷","🤒","🤕","🤢","🤮","🤧","🥵","🥶"
        ];

        // Definir botón de emojis en la toolbar
        Jodit.defaultOptions.controls.emojiPicker = {
            name: 'emojiPicker',
            tooltip: 'Insertar Emoji',
            template: function () {
                return '😊'; // el icono que se verá en la toolbar
            },
            popup: function (editor) {
                // Crear contenedor del popup
                const div = editor.create.fromHTML('<div style="max-height:200px;overflow:auto;padding:5px;"></div>');

                emojiList.forEach(e => {
                    // Crear cada emoji como un span clickeable
                    const span = editor.create.fromHTML(`<span style="font-size:22px;cursor:pointer;padding:3px;">${e}</span>`);
                    span.addEventListener('click', () => {
                        editor.selection.insertHTML(e);
                        editor.events.fire('closePopup'); // cerrar popup al elegir
                    });
                    div.appendChild(span);
                });

                return div;
            }
        };

        // Inicializar editor
        var editor = new Jodit('#content', {
            width: '100%',
            height: 500,
            extraButtons: ['emojiPicker']
        });
    </script>
@endsection
