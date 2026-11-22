@extends('layouts.admin')

@section('title', 'New Room Type')

@section('content')
<h1 class="text-2xl" style="font-family: 'Playfair Display', serif;">New Room Type</h1>

<form method="POST" action="{{ route('admin.room-types.store') }}" class="bg-white rounded-xl border border-[#e9dcc2] p-6 mt-6 max-w-2xl">
    @csrf
    @include('admin.room-types._form')
    <div class="mt-6 flex gap-3">
        <button type="submit" class="rounded-lg bg-[#5a1f28] text-[#f6ecd9] px-5 py-2 text-sm hover:bg-[#75323d]">Create room type</button>
        <a href="{{ route('admin.room-types.index') }}" class="rounded-lg border border-[#e9dcc2] px-5 py-2 text-sm">Cancel</a>
    </div>
</form>
@endsection
