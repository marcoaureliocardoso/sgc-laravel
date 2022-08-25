@php
$article = $employeeGender->name === 'Masculino' ? 'o' : 'a';
@endphp
@component('mail::message')
Prezada Equipe DEDI,

A pedido da Coordenação do Curso de {{ $courseName }}, venho solicitar a liberação de acesso d{{ $article }} colaborador{{ $employeeGender->name === 'Masculino' ? '' : 'a' }} abaixo mencionad{{ $article }} às salas das disciplinas deste referido curso.

Nome: {{ $employeeName }}

Função: {{ $employeeRoleName }}

@if ($poleName !== 'SEAD')
Polo: {{ $poleName }}

@endif
Login de acesso: {{ $employeeInstitutionLogin }}

E-mail Pessoal: {{ $employeePersonalEmail }}

E-mail Institucional: {{ $employeeInstitutionEmail }}

Telefone: {{ $employeePhone }}<br />
Celular: {{ $employeeMobile }}


Atenciosamente,

{{ $senderName ?? 'Secretaria Sead' }}<br />
Secretaria Acadêmica - Sead/Ufes
@endcomponent
