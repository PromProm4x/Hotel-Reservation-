@extends('layouts.admin')

@section('title', 'Edit '.$service->name)

@section('content')
<div>
    <a href="{{ route('admin.services.index') }}" class="text-sm text-[#a9853f] hover:underline">&larr; Services</a>
    <h1 class="text-2xl mt-1" style="font-family: 'Playfair Display', serif;">Edit {{ $service->name }}</h1>
</div>

<form method="POST" action="{{ route('admin.services.update', $service) }}" class="mt-6 max-w-2xl bg-white rounded-xl border border-[#e9dcc2] p-6">
    @csrf
    @method('PUT')
    @include('admin.services._form')
    <div class="mt-6 flex items-center gap-3">
        <button type="submit" class="rounded-lg bg-[#5a1f28] text-[#f6ecd9] px-5 py-2.5 text-sm hover:bg-[#75323d]">Save changes</button>
        <a href="{{ route('admin.services.index') }}" class="rounded-lg border border-[#e9dcc2] px-5 py-2.5 text-sm hover:bg-[#f6ecd9]">Cancel</a>
    </div>
</form>
@endsection
