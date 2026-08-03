@extends('backend.layouts.master')
@section('title', $pageTitle)
@section('body')
@php($isEdit = filled($record))
<div class="page-heading"><h1>{{ $pageTitle }}</h1><p>Create and manage homepage hero slide content.</p></div>
<article class="panel form-panel form-panel-full">
<form action="{{ $isEdit ? route('backend.resource.update',['resource'=>'hero-slides','id'=>$record->id]) : route('backend.resource.store','hero-slides') }}" method="POST" enctype="multipart/form-data" class="admin-form">
  @csrf @if($isEdit) @method('PUT') @endif
  <div class="product-form-layout">
    <section class="form-section form-section-main">
      <div class="form-section-head"><span>01</span><h2>Slide Content</h2></div>
      <div class="form-grid">
        <label class="wide">Subtitle<input name="subtitle" value="{{ old('subtitle',$record->subtitle ?? '') }}" placeholder="Featured performance product">@error('subtitle')<span>{{ $message }}</span>@enderror</label>
        <label class="wide">Title<input name="title" value="{{ old('title',$record->title ?? '') }}" required placeholder="Reach your potential">@error('title')<span>{{ $message }}</span>@enderror</label>
        <label class="wide">Paragraph<textarea name="paragraph" rows="4" placeholder="Slide description...">{{ old('paragraph',$record->paragraph ?? '') }}</textarea>@error('paragraph')<span>{{ $message }}</span>@enderror</label>
      </div>
    </section>
    <section class="form-section">
      <div class="form-section-head"><span>02</span><h2>Settings</h2></div>
      <div class="form-grid">
        <label>Status<select name="status" required><option value="active" @selected(old('status',$record->status ?? 'active')==='active')>Active</option><option value="inactive" @selected(old('status',$record->status ?? '')==='inactive')>Inactive</option></select></label>
        <label>Display Order<input type="number" min="0" name="sort_order" value="{{ old('sort_order',$record->sort_order ?? 0) }}" required></label>
        <label class="wide">Button Label<input name="button_label" value="{{ old('button_label',$record->button_label ?? '') }}" placeholder="Shop now"></label>
        <label class="wide">Button URL<input name="button_url" value="{{ old('button_url',$record->button_url ?? '') }}" placeholder="/shop"></label>
      </div>
    </section>
    <section class="form-section form-section-main">
      <div class="form-section-head"><span>03</span><h2>Images</h2></div>
      <div class="form-grid product-media-grid">
        <label>Product Image<input type="file" name="product_image_file" accept="image/png,image/jpeg,image/webp">@error('product_image_file')<span>{{ $message }}</span>@enderror</label>
        <label>Background Image<input type="file" name="background_image_file" accept="image/png,image/jpeg,image/webp">@error('background_image_file')<span>{{ $message }}</span>@enderror</label>
        <div class="image-preview-grid wide">
          @if($isEdit && $record->product_image)<div class="image-preview-card"><img src="{{ url($record->product_image) }}" alt="Product image"><label class="switch-row"><input type="checkbox" name="remove_product_image" value="1"><span>Remove product image</span></label></div>@endif
          @if($isEdit && $record->background_image)<div class="image-preview-card"><img src="{{ url($record->background_image) }}" alt="Background image"><label class="switch-row"><input type="checkbox" name="remove_background_image" value="1"><span>Remove background image</span></label></div>@endif
        </div>
      </div>
    </section>
  </div>
  <div class="form-actions"><a href="{{ route('backend.page','hero-slides') }}">Cancel</a><button type="submit"><i class="fa-solid fa-floppy-disk"></i> {{ $isEdit ? 'Update' : 'Create' }} Slide</button></div>
</form>
</article>
@endsection