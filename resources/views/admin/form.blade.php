@extends('admin.layout')
@section('title',($item->exists?'Edit ':'Tambah ').$definition['label'])
@section('action')<a class="inline-link" href="/admin/{{ $resource }}">← Kembali ke daftar</a>@endsection
@section('content')
<form action="/admin/{{ $resource }}{{ $item->exists?'/'.$item->id:'' }}" method="post" enctype="multipart/form-data" class="admin-panel admin-form">@csrf @if($item->exists)@method('PUT')@endif
<div class="form-grid">@foreach($definition['fields'] as $field=>$spec)
@php
$value=old($field,$item->$field);
if($spec['type']==='datetime-local'&&$value)$value=\Carbon\Carbon::parse($value)->format('Y-m-d\TH:i');
if($spec['type']==='time'&&$value)$value=substr($value,0,5);
if($spec['type']==='number'&&$value===null&&$field==='display_order')$value=0;
$required=str_contains($spec['rules'],'required');
@endphp
<div class="form-field {{ in_array($spec['type'],['textarea','file','checkbox'])?'full-width':'' }}">
@if($spec['type']==='checkbox')<label class="checkbox-label"><input type="checkbox" name="{{ $field }}" value="1" @checked(old($field,session()->hasOldInput()?false:($item->exists?$item->$field:($field==='is_active'&&$resource!=='event-editions'))))><span>{{ $spec['label'] }}</span></label>@if($resource==='event-editions')<small>Mengaktifkan edisi ini akan menonaktifkan edisi lainnya secara otomatis.</small>@endif
@else<label for="field-{{ $field }}">{{ $spec['label'] }} @if($required)<span>*</span>@endif</label>
@if(in_array($spec['type'],['event','campus']))<select id="field-{{ $field }}" name="{{ $field }}" @required($required)><option value="">Pilih {{ strtolower($spec['label']) }}</option>@foreach($spec['type']==='event'?$events:$campuses as $option)<option value="{{ $option->id }}" @selected((string)$value===(string)$option->id)>{{ $option->name }}</option>@endforeach</select>
@elseif($spec['type']==='textarea')<textarea name="{{ $field }}" id="field-{{ $field }}" rows="4" @required($required)>{{ $value }}</textarea>
@elseif($spec['type']==='file')<div class="upload-field"><input type="file" name="{{ $field }}" id="field-{{ $field }}" accept="image/jpeg,image/png,image/webp" data-preview="preview-{{ $field }}" @required($resource==='galleries'&&!$item->exists)><small>JPG, PNG, WebP · maksimal 5 MB. Gambar dioptimalkan menjadi WebP.</small><img id="preview-{{ $field }}" src="{{ $item->$field?\App\Support\Media::url($item->$field):'' }}" alt="Preview {{ $spec['label'] }}" @unless($item->$field) hidden @endunless></div>
@else<input id="field-{{ $field }}" name="{{ $field }}" type="{{ $spec['type'] }}" value="{{ $value }}" @required($required) @if(isset($spec['placeholder'])) placeholder="{{ $spec['placeholder'] }}" @endif @if(isset($spec['help'])) aria-describedby="help-{{ $field }}" @endif @if($spec['type']==='number') min="0" @endif>@endif
@if(isset($spec['help']))<small id="help-{{ $field }}">{{ $spec['help'] }}</small>@endif
@endif
@error($field)<span class="field-error">{{ $message }}</span>@enderror</div>@endforeach</div><div class="form-actions"><button class="button" type="submit">Simpan Perubahan <x-icon name="check"/></button><a href="/admin/{{ $resource }}">Batal</a></div></form>
@endsection
