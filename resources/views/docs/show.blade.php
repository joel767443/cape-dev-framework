@extends('layouts.app')

@section('title', $title)

@section('content')
  <div class="py-3 py-lg-4">
    <div class="row g-4">
      <div class="col-12 col-lg-3">
        <div class="list-group shadow-sm">
          <a class="list-group-item list-group-item-action @if(($active ?? '') === 'README.md') active @endif" href="/docs">Docs index</a>
          @foreach(($nav ?? []) as $item)
            <a
              class="list-group-item list-group-item-action @if(($active ?? '') === ($item['file'] ?? '')) active @endif"
              href="{{ $item['href'] }}"
            >
              {{ $item['label'] }}
            </a>
          @endforeach
        </div>
      </div>

      <div class="col-12 col-lg-9">
        <div class="card shadow-sm">
          <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="fw-semibold">{{ $title }}</div>
            <div class="text-secondary small">{{ $active }}</div>
          </div>
          <div class="card-body md">
            {!! $html !!}
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

