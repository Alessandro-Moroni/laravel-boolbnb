@extends('layouts.admin')

@section('content')
  <div class="row py-2 px-lg-5 px-2 justify-content-between">
    <div class="col-12">
      {{-- alert di avvenuta cancellazione  --}}
      @if (session('deleted'))
        <div class="alert alert-success" role="alert">
          {{ session('deleted') }}
        </div>
      @endif
    </div>

    <div class="col-8 my-3 col-lg-4">
      <form action="{{ route('admin.home') }}" method="GET" class="d-flex" role="search">
        <input name="toSearch" class=" toSearch form-control me-2" type="search" placeholder="Cerca" aria-label="Search">

        <button class="btn btn-outline-success" type="submit"><i class="fa-solid fa-circle-chevron-right"></i></button>
      </form>
    </div>
    <div class="col-4 my-3 text-end">
      <a href="{{ route('admin.houses.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i></a>
    </div>
  </div>

  <div class="row pt-2 pb-5 px-0 px-sm-5 px-md-0 px-lg-5">
    @if (isset($_GET['toSearch']) && !empty($_GET['toSearch']))
      <div class="mt-2 ps-4">
        <h1>Castelli trovati per <span class="text-danger">"{{ $_GET['toSearch'] }}"</span> : {{ $count_search }} </h1>
      </div>
    @endif
    <div class="col-12">
      <div class="px-2 rounded-3 pb-1">
        @if (count($houses) > 0)
          <h2 class="py-3 text-dark rounded-3 fw-bold fs-2 p-3 mt-3">I tuoi Castelli</h2>
        @endif

        <table class="table table-light table-striped table-responsive">
          @if (count($houses) > 0)
            <thead>
              <tr>

                <th scope="col" class="d-none d-md-table-cell">Immagine</th>
                <th scope="col">
                  <a class="text-dark text-decoration-none"
                    href="{{ route('admin.orderby', ['direction' => $direction, 'column' => 'title']) }}">Nome

                    @if ($direction === 'asc')
                      <i class="fa-solid fa-sort-up"></i>
                    @else
                      <i class="fa-solid fa-sort-down"></i>
                    @endif

                  </a>

                </th>
                <th class="text-center d-none d-md-table-cell " scope="col">Sponsor</th>
                <th class="text-center d-none  d-md-table-cell " scope="col">Pubblica</th>
                <th class="text-center" scope="col">Azioni</th>
              </tr>
            </thead>
          @endif
          <tbody class="table-group-divider">

            @forelse ($houses as $house)
              <tr>

                <td class="align-content-center d-none d-md-table-cell">

                  <img src="{{ asset('storage/' . $house?->images->first()?->image_path) }}" alt="{{ $house?->title }}"
                    class="img-100"  onerror="this.src='/img/not-found.jpg'">

                </td>

                <td class="align-content-center">
                  {{ $house->title }}
                </td>

                <td class="align-content-center text-center d-none d-md-table-cell ">
                  {{-- @if ($house->sponsors()->exists()) --}}
                  @if ($house->sponsors()->max('expiration_date') >= $today)
                    Si
                  @else
                    No
                  @endif
                </td>

                <td class="align-content-center text-center d-none  d-md-table-cell ">
                  @if ($house->is_visible === 1)
                    <i class="fa-solid fa-circle text-success"></i>
                  @else
                    <i class="fa-solid fa-circle text-danger"></i>
                  @endif
                </td>

                <td class="align-content-center text-center">

                  <a href="{{ route('admin.houses.show', $house) }}" class="btn btn-info me-2 mb-2">
                    <i class="fa-solid fa-eye"></i>
                  </a>

                  <a href="{{ route('admin.houses.edit', $house) }}" class="btn btn-warning me-2 mb-2">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </a>

                  <a href="{{ route('admin.sponsors', $house) }}" role="button" aria-disabled="true"
                    class="btn btn-success mb-2 @if (!$house->is_visible) disabled @endif">
                    <i class="fa-solid fa-rocket"></i>
                  </a>

                  <a href="{{ route('admin.stats', $house) }}" class="btn btn-secondary mx-2  mb-2">
                    <i class="fa-solid fa-chart-simple"></i>
                  </a>

                  @include('admin.partials.formdelete', [
                      'route' => route('admin.houses.destroy', $house),
                      'message' => "Sei sicuro di voler eliminare  $house->title ?",
                  ])

                </td>

              </tr>
            @empty
              <h2 class=" ms-3">Nessun Castello Presente</h2>
            @endforelse

          </tbody>
        </table>
        {{ $houses->links() }}
      </div>
    </div>
  </div>
@endsection
