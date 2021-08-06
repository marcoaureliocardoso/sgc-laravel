@csrf
<div class="mb-3">
    <label for="inputName1" class="form-label">Nome*</label>
<input name="name" type="text" id="inputName1" class="form-control" placeholder="Nome do Curso" value="{{ $course->name ?? old('name') }}" />
@error('name')
    <div class="error">> {{ $message }}</div>
@enderror
</div>
<div class="mb-3">
    <label for="inputDescription1" class="form-label">Descrição</label>
<input name="description" type="text" id="inputDescription1" class="form-control" placeholder="Descrição do curso" value="{{ $course->description ?? old('description') }}" />
@error('description')
    <div class="error">> {{ $message }}</div>
@enderror
</div>
<div class="mb-3">
    <label for="selectType1" class="form-label">Tipo*</label>
<select name="courseTypes" id="selectType1" class="form-select">
    <option value="">Selecione o tipo</option>
    @foreach ($courseTypes as $courseType)
        <option value="{{ $courseType->id }}" {{($courseType->id == $course->course_type_id) ? 'selected' : ''}}>{{ $courseType->name }}</option>
    @endforeach
</select>
@error('courseTypes')
    <div class="error">> {{ $message }}</div>
@enderror
</div>
<div class="mb-3">
    <label for="inputBegin1" class="form-label">Início</label>
<input type="date" name="begin" id="inputBegin1" class="form-control" value="{{ $course->begin ?? old('begin') }}">
</div>
<div class="mb-3">
    <label for="inputEnd1" class="form-label">Fim</label>
<input type="date" name="end" id="inputEnd1" class="form-control" value="{{ $course->end ?? old('end') }}">
</div>
