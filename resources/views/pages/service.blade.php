@extends('layouts.app')

@section('title', $title)

@section('content')
<section class="rounded-[28px] bg-white p-6 shadow-[0_4px_24px_rgba(30,40,110,0.04)]">
    <p class="text-xs font-extrabold tracking-[0.2em] text-[#4154e8] uppercase">{{ $eyebrow }}</p>
    <h1 class="mt-2 text-3xl font-extrabold text-[#1e293b]">{{ $title }}</h1>
    <p class="mt-2 max-w-2xl text-sm leading-6 text-[#7e8ba0]">{{ $description }}</p>
</section>

<div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
    @foreach ($cards as $card)
        <article class="rounded-[28px] bg-white p-5 shadow-[0_4px_24px_rgba(30,40,110,0.04)]">
            <div class="mb-4 h-36 overflow-hidden rounded-2xl bg-[#edf1f7]">
                <img src="{{ $card['image'] }}" alt="" class="h-full w-full object-cover">
            </div>
            <p class="text-sm font-extrabold text-[#1e293b]">{{ $card['title'] }}</p>
            <p class="mt-1 text-xs text-[#7e8ba0]">{{ $card['meta'] }}</p>
            <button class="mt-4 rounded-xl bg-[#4154e8] px-4 py-2 text-xs font-bold text-white transition hover:bg-[#3243d4]">{{ $card['cta'] }}</button>
        </article>
    @endforeach
</div>
@endsection
