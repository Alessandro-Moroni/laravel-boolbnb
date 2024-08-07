@extends('layouts.admin')

@section('content')
    @php
        $isEdit = isset($house);
    @endphp

    <h1 class="py-5 text-center mt-3 rounded-3 bg-gray">{{ $title }}</h1>

    <h6 class="ps-5">I campi con <strong>(*)</strong> sono obbligatori</h6>

    <form id="houseForm" class="row fw-medium rounded-3 bg-gray p-5" enctype="multipart/form-data" action='{{ $route }}'
        method='POST'>
        @csrf
        @method($method)

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="m-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- titolo --}}
        <div class="col-12 col-md-6 mb-3">
            <label for="title" class="form-label">Titolo (*)</label>
            <input name="title" type="text" placeholder="Inserisci il nome del castello"
                class="form-control @error('title') is-invalid @enderror" id="title"
                value="{{ old('title', $house?->title) }}" required minlength="3" maxlength="100">
            @error('title')
                <small class="text-danger fw-bold">
                    {{ $message }}
                </small>
            @enderror
        </div>

        {{-- stanze --}}
        <div class="col-12 col-md-6 mb-3">
            <label for="rooms" class="form-label">Stanze (*)</label>
            <input name="rooms" type="number" placeholder="Inserisci il numero di stanze"
                class="form-control @error('rooms') is-invalid @enderror" id="rooms"
                value="{{ old('rooms', $house?->rooms) }}" required min="1" max="125">
            @error('rooms')
                <small class="text-danger fw-bold">
                    {{ $message }}
                </small>
            @enderror
        </div>

        {{-- bagni --}}
        <div class="col-12 col-md-6 mb-3">
            <label for="bathrooms" class="form-label">Bagni (*)</label>
            <input name="bathrooms" type="number" placeholder="Inserisci il numero di bagni"
                class="form-control @error('bathrooms') is-invalid @enderror" id="bathrooms"
                value="{{ old('bathrooms', $house?->bathrooms) }}" required min="1" max="125">
            @error('bathrooms')
                <small class="text-danger fw-bold">
                    {{ $message }}
                </small>
            @enderror
        </div>

        {{-- letti --}}
        <div class="col-12 col-md-6 mb-3">
            <label for="bed" class="form-label">Posti Letto (*)</label>
            <input name="bed" type="number" placeholder="Inserisci il numero di posti letto"
                class="form-control @error('bed') is-invalid @enderror" id="bed"
                value="{{ old('bed', $house?->bed) }}" required min="1" max="125">
            @error('bed')
                <small class="text-danger fw-bold">
                    {{ $message }}
                </small>
            @enderror
        </div>

        {{-- mq --}}
        <div class="col-12 col-md-6 mb-3">
            <label for="square_meters" class="form-label">Metri Quadri</label>
            <input name="square_meters" type="number" placeholder="Inserisci i metri quadri"
                class="form-control @error('square_meters') is-invalid @enderror" id="square_meters"
                value="{{ old('square_meters', $house?->square_meters) }}">
            @error('square_meters')
                <small class="text-danger fw-bold">
                    {{ $message }}
                </small>
            @enderror
        </div>

        {{-- visibilità --}}
        <div class="col-12 col-md-6 mb-3 align-content-center">

            <label for="is_visible" class="form-label m-0 pe-2">Visibilità del Castello</label>
            <select name="is_visible" id="is_visible" class="p-1 rounded-2">
                <option @if ($house?->is_visible == 1) selected @endif value="1">Sì</option>
                <option @if ($house?->is_visible == 0) selected @endif value="0">No</option>
            </select>
        </div>

        {{-- Indirizzo --}}
        <div class="col-12 col-xl-6 mb-3">
            <label for="address" class="form-label">Indirizzo (*)</label>
            <input type="text" name="address" id="address" placeholder="Inserisci l'indirizzo" class="form-control"
                value="{{ old('', $house?->address) }}" required min="2" max="100">
            <div id="addressList" role="button" class="autocomplete-items rounded-bottom-3 overflow-hidden"></div>
        </div>

        <input name="latitude" type="hidden" id="latitude" value="{{ old('latitude', $house?->latitude) }}" required
            min="-90" max="90">

        <input name="longitude" type="hidden" id="longitude" value="{{ old('longitude', $house?->longitude) }}" required
            min="-180" max="180">

        {{-- descrizione  --}}
        <div class="col-12 col-xl-6 mb-3">
            <label for="description" class="form-label">Descrizione</label>
            <textarea name="description" placeholder="Inserisci una descrizione"
                class="form-control @error('description') is-invalid @enderror" id="description" rows="6">{{ old('description', $house?->description) }}</textarea>
            @error('description')
                <small class="text-danger fw-bold">
                    {{ $message }}
                </small>
            @enderror
        </div>

        {{-- servizi --}}
        <div class="btn-group col-12 d-block" role="group" aria-label="Basic checkbox toggle button group">
            <p class="pe-2">Seleziona i Servizi:</p>
            <div class="d-flex flex-wrap">
                @foreach ($services as $service)
                    <input type="checkbox" value="{{ $service->id }}" name="services[]" class="btn-check"
                        id="tech-{{ $service->id }}" autocomplete="off"
                        @if (($errors->any() && in_array($service->id, old('services', []))) || $house?->services->contains($service)) checked @endif>
                    <label class="btn btn-light btn-outline-primary fw-medium m-2"
                        for="tech-{{ $service->id }}">{{ $service->name }} <i
                            class="{{ $service->icon }} ms-1"></i></label>
                @endforeach
            </div>
        </div>

        {{-- immagini --}}
        <div class="col-12 mb-3">
            <label class="mt-5 mb-4" for="images" class="form-label">Immagini - La prima immagine caricata verrà
                salvata come copertina</label>
            <input name="images[]" type="file" class="form-control @error('images.*') is-invalid @enderror"
                id="images" multiple>
            @error('images.*')
                <small class="text-danger fw-bold">
                    {{ $message }}
                </small>
            @enderror

            {{-- Preview delle immagini --}}
            <div id="image-preview" class="mt-3">
                @if ($isEdit && $house->images->count() > 0)
                    @foreach ($house->images as $image)
                        <div class="image-container position-relative d-inline-block mx-1">
                            <img src="{{ asset('storage/' . $image->image_path) }}" class="img-thumbnail"
                                style="max-height: 100px;">
                            <button type="button" data-image-id="{{ $image->id }}"
                                class="btn btn-danger btn-sm position-absolute top-0 end-0 remove-image-btn">x</button>
                        </div>
                    @endforeach
                @endif
            </div>

            <input type="hidden" name="remove_images" id="remove_images" value="">
        </div>

        <div class="text-center pt-3">
            <button type="submit"
                class="btn w-25 me-3 {{ Route::currentRouteName() === 'admin.houses.create' ? 'btn-success' : 'btn-warning' }}">{{ $button }}</button>
            <button type="reset" class="btn btn-danger w-25">Reset</button>
        </div>
    </form>

    {{-- javascript  --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
    const addressInput = document.getElementById('address');
    const addressList = document.getElementById('addressList');
    const latitudeInput = document.getElementById('latitude');
    const longitudeInput = document.getElementById('longitude');
    const imagePreviewDiv = document.getElementById('image-preview');
    const imagesInput = document.getElementById('images');
    const removeImagesInput = document.getElementById('remove_images');

    let addressSelected = @json($isEdit);

    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    const handleAddressInput = debounce(function() {
        let query = addressInput.value;
        addressSelected = false;

        if (query.length > 1) {
            fetch('{{ route('autocomplete') }}?query=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => {
                    addressList.innerHTML = '';

                    data.forEach(item => {
                        const option = document.createElement('div');
                        option.classList.add('bg-white', 'p-1', 'ps-2', 'border-bottom', 'border-secondary-subtle');
                        option.innerHTML = "<strong>" + item.address.freeformAddress + "</strong>";

                        option.addEventListener('click', function() {
                            addressInput.value = item.address.freeformAddress;
                            latitudeInput.value = item.position.lat;
                            longitudeInput.value = item.position.lon;
                            addressList.innerHTML = '';
                            addressSelected = true;
                        });

                        addressList.appendChild(option);
                    });
                });
        } else {
            addressList.innerHTML = '';
        }
    }, 300);

    addressInput.addEventListener('input', handleAddressInput);

    document.addEventListener('click', function(e) {
        if (!addressList.contains(e.target) && e.target !== addressInput) {
            addressList.innerHTML = '';
        }
    });

    document.getElementById('houseForm').addEventListener('submit', function(event) {
        let valid = true;

        const title = document.getElementById('title').value;
        const rooms = document.getElementById('rooms').value;
        const bathrooms = document.getElementById('bathrooms').value;
        const bed = document.getElementById('bed').value;
        const address = document.getElementById('address').value;

        const images = document.getElementById('images').files;
        const validImageTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml'];

        for (let i = 0; i < images.length; i++) {
            if (!validImageTypes.includes(images[i].type)) {
                alert('I file selezionati devono essere di tipo jpeg, png, jpg, gif o svg');
                valid = false;
                break;
            }
        }

        if (title.length < 3 || title.length > 100) {
            alert('Il titolo deve contenere tra 3 e 100 caratteri');
            valid = false;
        }
        if (rooms < 1) {
            alert('Il numero di stanze deve essere almeno 1');
            valid = false;
        }
        if (bathrooms < 1) {
            alert('Il numero di bagni deve essere almeno 1');
            valid = false;
        }
        if (bed < 1) {
            alert('Il numero di letti deve essere almeno 1');
            valid = false;
        }
        if (!address || address.length < 2 || address.length > 100 || addressSelected === false) {
            alert('L\'indirizzo inserito non è valido. Per favore, seleziona un indirizzo dalla lista');
            valid = false;
        }

        if (!valid) {
            event.preventDefault();
        }
    });

    function handleImageRemoval(event) {
        const btn = event.target;
        const imageContainer = btn.parentElement;
        const imageId = btn.getAttribute('data-image-id');

        if (imageId) {
            let removeImages = removeImagesInput.value ? removeImagesInput.value.split(',') : [];
            removeImages.push(imageId);
            removeImagesInput.value = removeImages.join(',');
        }

        imageContainer.remove();

        // Remove image from the input file
        let dt = new DataTransfer();
        let files = imagesInput.files;

        for (let i = 0; i < files.length; i++) {
            if (files[i] !== imageContainer.file) {
                dt.items.add(files[i]);
            }
        }

        imagesInput.files = dt.files;
    }

    imagesInput.addEventListener('change', function() {
        const images = this.files;
        imagePreviewDiv.innerHTML = ''; // Clear previous preview

        for (let i = 0; i < images.length; i++) {
            const file = images[i];
            const reader = new FileReader();

            reader.onload = function(e) {
                const imageContainer = document.createElement('div');
                imageContainer.classList.add('image-container', 'position-relative', 'd-inline-block', 'mx-1');
                imageContainer.file = file; // Store file reference

                const image = document.createElement('img');
                image.src = e.target.result;
                image.classList.add('img-thumbnail');
                image.style.maxHeight = '100px';

                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.classList.add('btn', 'btn-danger', 'btn-sm', 'position-absolute', 'top-0', 'end-0', 'remove-image-btn');
                removeButton.textContent = 'x';

                imageContainer.appendChild(image);
                imageContainer.appendChild(removeButton);
                imagePreviewDiv.appendChild(imageContainer);

                removeButton.addEventListener('click', function() {
                    handleImageRemoval(event);
                });
            };

            reader.readAsDataURL(file);
        }
    });

    document.querySelectorAll('.remove-image-btn').forEach(btn => {
        btn.addEventListener('click', handleImageRemoval);
    });
});

    </script>
@endsection
