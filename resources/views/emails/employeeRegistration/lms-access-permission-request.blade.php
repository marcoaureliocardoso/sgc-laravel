{{$article = $employeeGender->name === 'Masculino' ? 'o' : 'a'}}

Prezada Equipe DEDI\Moodle 

A pedido da Coordenação do Curso de {{$courseName}}, venho solicitar a liberação de acesso d{{$article}} colaborador{{$article}} abaixo mencionad{{$article}} às salas das disciplinas deste referido curso.

Nome: {{$employeeName}}

Função: {{$employeeRoleName}}

Polo: {{$poleName}}

Login de acesso: {{$employeeInstitutionLogin}}

E-mail Pessoal: {{$employeePersonalEmail}}

E-mail Institucional: {{$employeeInstitutionEmail}}

Telefone: {{$employeePhone}}
Celular: {{$employeeMobile}}


Atenciosamente,

{{$senderName ?? 'Secretaria Sead'}}
Secretaria Acadêmica - Sead/Ufes

