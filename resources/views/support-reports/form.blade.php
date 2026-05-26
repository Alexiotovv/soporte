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
                            <div id="editor" style="min-height: 260px; background: #fff; border: 1px solid rgba(20,81,61,.25); border-radius: 10px;"></div>
                            <textarea id="content" name="content" class="d-none">{{ old('content', $report->content ?? '') }}</textarea>
                            @error('content')
                                <div class="text-danger small mt-1">{{ $message }}</div>
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
    <style>
        .tox-tinymce {
            border-radius: 10px !important;
            border-color: rgba(20,81,61,.25) !important;
        }
    </style>
@endsection

@section('scripts')
    <script src="https://cdn.tiny.cloud/1/3rj3ihcljdbzgjfkppqu4rn5i2vrolk8no4osondr4uyyo5e/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
    <script>
        (function () {
            const hiddenContent = document.getElementById('content');

            tinymce.init({
                selector: '#editor',
                height: 420,
                menubar: false,
                branding: false,
                promotion: false,
                plugins: 'link lists table image code autoresize',
                toolbar: 'undo redo | blocks | bold italic underline | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist | table | link image | code | removeformat',
                content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }',
                images_upload_url: '{{ route('support-reports.media.upload') }}',
                images_upload_credentials: true,
                automatic_uploads: true,
                file_picker_types: 'image',
                images_reuse_filename: true,
                file_picker_callback: function (callback, value, meta) {
                    if (meta.filetype !== 'image') {
                        return;
                    }

                    const input = document.createElement('input');
                    input.type = 'file';
                    input.accept = 'image/*';

                    input.onchange = function () {
                        const file = input.files[0];
                        if (!file) {
                            return;
                        }

                        const formData = new FormData();
                        formData.append('file', file);

                        fetch('{{ route('support-reports.media.upload') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: formData,
                            credentials: 'same-origin'
                        })
                        .then(function (response) {
                            if (!response.ok) {
                                throw new Error('No se pudo subir la imagen.');
                            }

                            return response.json();
                        })
                        .then(function (payload) {
                            if (!payload.location) {
                                throw new Error('Respuesta invalida del servidor.');
                            }

                            callback(payload.location, { alt: file.name });
                        })
                        .catch(function (error) {
                            alert(error.message || 'Error al subir la imagen.');
                        });
                    };

                    input.click();
                },
                images_upload_handler: function (blobInfo, progress) {
                    return new Promise(function (resolve, reject) {
                        const formData = new FormData();
                        formData.append('file', blobInfo.blob(), blobInfo.filename());

                        fetch('{{ route('support-reports.media.upload') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: formData,
                            credentials: 'same-origin'
                        })
                        .then(function (response) {
                            if (!response.ok) {
                                throw new Error('No se pudo subir la imagen.');
                            }

                            return response.json();
                        })
                        .then(function (payload) {
                            if (!payload.location) {
                                throw new Error('Respuesta invalida del servidor.');
                            }

                            resolve(payload.location);
                        })
                        .catch(function (error) {
                            reject(error.message || 'Error al subir la imagen.');
                        });
                    });
                },
                setup: function (editor) {
                    editor.on('init', function () {
                        if (hiddenContent.value && hiddenContent.value.trim() !== '') {
                            editor.setContent(hiddenContent.value);
                        }
                    });

                    editor.on('change keyup', function () {
                        hiddenContent.value = editor.getContent();
                    });
                }
            });

            document.getElementById('support-report-form').addEventListener('submit', function () {
                if (window.tinymce) {
                    hiddenContent.value = tinymce.get('editor').getContent();
                }
            });
        })();
    </script>
@endsection
