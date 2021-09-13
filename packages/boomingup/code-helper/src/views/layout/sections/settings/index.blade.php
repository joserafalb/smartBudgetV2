@extends('code-helper::layout')

@section('title', 'Laravel Code Helper')

@section('main')
<form method="POST">
    @csrf
    @include('code-helper::components.text', [
    'label' => 'Models root folder',
    'name' => 'modelsFolder',
    'value' => $currentFolder
    ])
    <button type="submit">Save</button>
</form>
@endsection
