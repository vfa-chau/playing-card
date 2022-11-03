@extends('layouts.app')
@section('title')@lang('view.card.title-header') @endsection

@section('content')
<h1><span>@lang('view.card.title')</span></h1>

<form method="POST" action="{{ route('card.distribute') }}">
  {{ csrf_field() }}

  <div class="form-group">
    <label for="number_of_player" class="col-md-4 control-label">@lang('view.card.number-player')</label>

    <div class="col-md-6">
      <input
        id="number_of_player"
        type="text"
        class="form-control"
        name="number_of_player"
        value="{{ old('number_of_player') }}"
        autofocus
      />
      <div class="mt-1">@lang('view.card.number-player-helper')</div>
    </div>
  </div>
  <div class="form-group mt-3">
    <div>
      <button id="register" type="submit" class="btn btn-primary btn-wide">
        <span>@lang('view.card.btn-distribute')</span>
      </button>
    </div>
  </div>
</form>
<!-- Distributed cards -->
@if (isset($distributedCards))
  <div class="mt-3">
    <h3 class="text-primary fw-bold">@lang('view.card.distributed-result')</h3>
    <div>@lang('view.card.total-distributed-card') {{ $distributedCardTotal }}</div>
    @foreach ($distributedCards as $distributedCard)
      <div>
          <span class="fw-bold">@lang('view.card.player'){{ $distributedCard['person'] }}: </span>
          @if ($distributedCard['cards']->count())
            <span>{{ $distributedCard['cards']->join(', ') }}</span>
          @else
            <span>@lang('view.card.no-card')</span>
          @endif
      </div>
    @endforeach
  </div>
@endif
<!-- Remained cards -->
@if (isset($remainedCards))
  <div class="my-3">
    <h3 class="text-primary fw-bold">@lang('view.card.remained-result')</h3>
    <div>@lang('view.card.total-remained-card') {{ $remainedCards->count() }}</div>
    @if ($remainedCards->count())
      <div>
        <span class="fw-bold">@lang('view.card.remained-card') </span>
        <span>{{ $remainedCards->join(', ') }}</span>
      </div>
    @endif
  </div>
@endif
@endsection
