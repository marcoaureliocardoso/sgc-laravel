@extends('layouts.basic')

@section('title', 'Cadastrar Polo')

@section('content')
    <section>
        <h2>Cadastrar Polo</h2>
    </section>
    <section id="pageContent">
        <main role="main">
            <form action={{ route('poles.store') }} method="POST">
                @component('pole.componentPoleForm',  compact('pole'))@endcomponent
                <button type="submit" class="btn btn-primary">Cadastrar</button> <button type="button" onclick="history.back()" class="btn btn-secondary">Cancelar</button>
                @error('noStore')
                    <div class="error">> {{ $message }}</div>
                @enderror
                <br /><br />
            </form>
        </main>
    </section>
@endsection
