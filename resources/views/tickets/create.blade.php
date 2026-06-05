@extends('layouts.app')

@section('css')
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/jodit@latest/es2021/jodit.fat.min.css"
    />

@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Crear Nuevo Ticket</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label for="title">Título</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="priority">Prioridad</label>
                            <select class="form-control @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>🔵 Bajo</option>
                                <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>🟡 Medio</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>🔴 Alto</option>
                            </select>
                            @error('priority')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="category_id">Categoria</label>
                            <select class="form-control @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                                <option value="">Sin categoria</option>
                                @foreach($categories as $category)
                                    <option
                                        value="{{ $category->id }}"
                                        data-color="{{ $category->color }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}
                                    >
                                        {{ $category->name }} ({{ $category->color }})
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted d-inline-flex align-items-center gap-2 mt-2">
                                <span id="category-color-dot" style="width:14px;height:14px;border-radius:50%;display:inline-block;background:#d1d5db;"></span>
                                Color de la categoria seleccionada
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="description">Descripción</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="file">Archivo adjunto <small>(Opcional)</small></label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file">
                            <small class="form-text text-muted">
                                Formatos permitidos: JPG, PNG, JPEG. Tamaño máximo: 5MB
                            </small>
                            @error('file')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            
                            @isset($ticket->file)
                                <div class="mt-2">
                                    <small>Archivo actual:</small>
                                    <a href="{{ $ticket->file_url }}" target="_blank" class="d-block">
                                        {{ $ticket->file_name }}
                                    </a>
                                </div>
                            @endisset
                        </div>

                        <br>
                         <a href="{{ route('tickets.index') }}" class="btn btn-light btn-sm">⬅️ Regresar a lista</a>
                        <button type="submit" class="btn btn-light btn-sm">📤 Enviar Ticket</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

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

                // Crear cada emoji como un span clickeable
                emojiList.forEach(e => {
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
    var editor = new Jodit('#description', {
        width: '100%',
        height: 350,
        buttons: 'bold,italic,ul,ol,emojiPicker'
    });

    const categorySelect = document.getElementById('category_id');
    const categoryColorDot = document.getElementById('category-color-dot');

    function updateCategoryColorDot() {
        const selectedOption = categorySelect.options[categorySelect.selectedIndex];
        const color = selectedOption ? selectedOption.getAttribute('data-color') : null;
        categoryColorDot.style.background = color || '#d1d5db';
    }

    if (categorySelect) {
        categorySelect.addEventListener('change', updateCategoryColorDot);
        updateCategoryColorDot();
    }
    
    
    </script>
@endsection