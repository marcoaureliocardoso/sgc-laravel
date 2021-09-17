@extends('layouts.basic')

@section('title', 'Exibir Vínculo')

@section('content')
<nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
    <ol class="breadcrumb border-top border-bottom bg-light">
        <li class="breadcrumb-item">Colaboradores</li>
        <li class="breadcrumb-item"><a href="{{ route('bonds.index') }}">Listar Vínculos</a></li>
        <li class="breadcrumb-item active" aria-current="page">Exibir:
            [{{ $bond->employee->name . '-' . $bond->role->name . '-' . $bond->course->name . '-' . $bond->pole->name }}]
        </li>
    </ol>
</nav>
<section id="pageContent">
    <main role="main">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-xxl-8">
                @include('_components.alerts')

                @component('bond.componentBondDetails', compact('bond'))@endcomponent

                <div class="card mb-3">
                    <div class="card-header" data-bs-toggle="collapse" href="#bondDocumentListContent" role="button" aria-expanded="true" aria-controls="bondDocumentListContent">
                        <h4 class='mb-0'>Documentos do vínculo</h4>
                    </div>
                    <div class="collapse show" id="bondDocumentListContent" >
                        <div class="card-body">
                            @if($documents->count() > 0)
                                @component('bond.document.componentList', compact('documents'))@endcomponent
                                <a href="{{ route('bonds.document.massdownload', $bond) }}" class="btn btn-primary btn-sm">
                                    &nbsp;&#8627; Fazer o download de todos os documentos do vínculo (zip)
                                </a>
                            @else
                                <p class="mb-0">O vínculo não possui documentos cadastrados.</p>
                            @endif
                        </div>
                    </div>
                </div>

                @canany(['bond-review', 'bond-requestReview'])

                    @php
                        $hasRights = $documents->where('document_type_id', App\Models\DocumentType::where('name', 'Ficha de Inscrição - Termos e Licença')->first()->id)->count() > 0;
                    @endphp

                    <div class="card {{ $bond->impediment == 0 ? 'border-success' : 'border-warning'}}  mb-3">
                        <div class="card-header {{ $bond->impediment == 0 ? 'bg-success' : 'bg-warning'}}" data-bs-toggle="collapse" href="#bondReviewContent" role="button" aria-expanded="true" aria-controls="bondReviewContent">
                            <h4 class='mb-0'>Revisão do vínculo</h4>
                        </div>

                        <div class="collapse show" id="bondReviewContent" >
                            <div class="card-body">

                                @can('bond-requestReview')

                                    <div class="mb-2 row">
                                        <div class="col-sm-4 col-lg-3"><strong>Última revisão:</strong></div>
                                        <div class="col-sm-8 col-lg-9">{{ ($bond->uaba_checked_at != null) ? \Carbon\Carbon::parse($bond->uaba_checked_at)->isoFormat('DD/MM/Y hh:mm') : '-' }}</div>
                                    </div>

                                    <div class="mb-2 row">
                                        <div class="col-sm-4 col-lg-3">
                                            <strong>Status do vínculo:</strong>
                                        </div>
                                        <div class="col-sm-8 col-lg-9">
                                            {{ $bond->impediment == 0 ? 'Sem pendências' : 'Impedido'}}
                                            {{ $bond->impediment == 0 && !$hasRights ? ' - OBS: Sem o documento "Ficha de Inscrição - Termos e Licença" o vínculo permanecerá impedido.' : ''}}
                                        </div>
                                    </div>

                                    @if ($bond->impediment == '1')
                                        <div class="mb-2 row">
                                            <div class="col-sm-4 col-lg-3">
                                                <strong>Descrição do impedimento:</strong>
                                            </div>
                                            <div class="col-sm-8 col-lg-9">
                                                {{ $bond->impediment == '1' ? $bond->impediment_description : '' }}
                                                <br>
                                                {{ !$hasRights ? 'OBS: Sem o documento "Ficha de Inscrição - Termos e Licença" o vínculo permanecerá impedido.' : ''}}
                                            </div>
                                        </div>
                                    @endif

                                    <a href="{{ route('bonds.requestReview', $bond->id) }}" class="btn btn-primary btn-sm">Enviar notificação de revisão de vínculo</a>
                                @endcan

                                @can(['bond-review', 'bond-requestReview'])
                                    <hr>
                                @endcan

                                @can('bond-review')
                                        <form name="{{ 'formReview' . $bond->id }}" action="{{ route('bonds.review', $bond) }}" method="POST">
                                            @csrf
                                            <label class="form-label">Impedido:</label>
                                            <div class="mb-3 form-check">
                                                <input class="form-check-input" type="radio" id="sim" name="impediment" value="1"
                                                    {{ $bond->impediment == '1' ? 'checked' : '' }}
                                                    onclick="document.getElementById('impedimentDescription').disabled=false;">
                                                <label for="sim" class="form-check-label">
                                                    Sim
                                                </label>
                                                <br>
                                                
                                                <input class="form-check-input" type="radio" id="nao" name="impediment" value="0"
                                                    {{ $bond->impediment == '0' ? 'checked' : '' }}
                                                    onclick="document.getElementById('impedimentDescription').disabled=true;">
                                                <label for="nao" class="form-check-label">
                                                    Não (Sem o documento "Ficha de Inscrição - Termos e Licença" o vínculo permanecerá impedido.)
                                                </label>
                                            </div>
                                            <div class="mb-3">
                                                <label for="impedimentDescription" class="form-label">Motivo do impedimento:</label><br />
                                                <textarea class="form-control" id="impedimentDescription" name="impedimentDescription"
                                                    rows="4">{{ $bond->impediment_description ?? '' }}</textarea>
                                                <script>
                                                    if ({{ $bond->impediment }} == '0')
                                                        document.getElementById('impedimentDescription').disabled = true;
                                                </script>
                                            </div>
                                            <input type="submit" value="Revisar" class="btn btn-primary btn-sm my-2 mx-1">
                                        </form>
                                @endcan

                            </div>
                        </div>
                    </div>
                @endcanany

                <button type="button" onclick="history.back()" class="btn btn-secondary">Voltar</button>
                <br />
            </div>
        </div>
    </main>
</section>
@endsection
